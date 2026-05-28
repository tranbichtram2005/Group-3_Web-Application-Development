<?php
require_once __DIR__ . '/../model/ChatModel.php';

class ChatController {
    private $chatModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $this->chatModel = new ChatModel();
        // Quét Deal cũ mỗi khi người dùng truy cập Controller này
        $this->chatModel->autoCloseExpiredOffers(); 
    }

    public function index() {
        // ===============================================
        // TRICK: BẮT SÓNG RADAR ĐỂ VƯỢT RÀO INDEX.PHP
        // ===============================================
        if (isset($_GET['ajax_radar'])) {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'msg' => 'Mất Session']);
                exit;
            }
            
            $userId = $_SESSION['user_id'];
            $totalUnread = $this->chatModel->countTotalUnreadMessages($userId);
            $unreadPerConv = $this->chatModel->getUnreadCountPerConversation($userId);
            
            echo json_encode([
                'status' => 'success',
                'total' => $totalUnread,
                'per_conv' => (object)$unreadPerConv
            ]);
            exit; // Bắt buộc exit để không in ra giao diện chat
        }
        // ===============================================

        // LUỒNG HIỂN THỊ TRANG CHAT BÌNH THƯỜNG
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $userId = $_SESSION['user_id'];
        $tradeConvs = $this->chatModel->getTradeConversations($userId);
        $supportConvs = $this->chatModel->getSupportConversations($userId);
        require_once __DIR__ . '/../view/app/chat.php';
    }

 // ===============================================
    // 3 HÀM API REAL-TIME (ĐÃ BỌC BỘ BẮT LỖI)
    // ===============================================
    public function getUnreadCountsAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); } // Càn quét sạch 100% rác
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Mất Session đăng nhập']);
            exit;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $totalUnread = $this->chatModel->countTotalUnreadMessages($userId);
            $unreadPerConv = $this->chatModel->getUnreadCountPerConversation($userId);
            
            echo json_encode([
                'status' => 'success',
                'total' => $totalUnread,
                'per_conv' => (object)$unreadPerConv
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi DB: ' . $e->getMessage()]);
        }
        exit;
    }

   // API Lấy tin nhắn Mua Bán
    public function getTradeMessagesAjax() {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $convId = isset($_GET['conv_id']) ? intval($_GET['conv_id']) : 0;
        $lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
        $listingId = isset($_GET['listing_id']) ? intval($_GET['listing_id']) : 0;
        $buyerId = isset($_GET['buyer_id']) ? intval($_GET['buyer_id']) : 0;
        $userId = $_SESSION['user_id'];
        
        if ($convId > 0) {
            $this->chatModel->markMessagesAsRead($convId, $userId); // Báo đã xem tin Mua Bán
            $messages = $this->chatModel->getTradeMessages($convId, $lastId);
            $offer = $this->chatModel->getActiveOffer($listingId, $buyerId);
            $readUntilId = $this->chatModel->getLastReadMessageId($convId, $userId);

            echo json_encode(['status' => 'success', 'data' => $messages, 'offer' => $offer, 'read_until_id' => $readUntilId]);
        }
        exit;
    }

   public function getSupportMessagesAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        try {
            $convId = isset($_GET['conv_id']) ? intval($_GET['conv_id']) : 0;
            $lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
            
            if ($convId > 0) {
                $this->chatModel->markSupportMessagesAsRead($convId);
                $messages = $this->chatModel->getSupportMessages($convId, $lastId);
                echo json_encode(['status' => 'success', 'data' => $messages]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi DB: ' . $e->getMessage()]);
        }
        exit;
    }

   // ===============================================
    // API Nghiệp vụ Deal Giá (ĐÃ FIX LỖI TƯƠNG TỰ)
    // ===============================================
    public function dealAjax() {
        if (ob_get_level() > 0) @ob_clean(); // Quét sạch rác HTML
        header('Content-Type: application/json; charset=utf-8');
        
        $action = isset($_POST['action']) ? $_POST['action'] : ''; 
        $listingId = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;
        $buyerId = isset($_POST['buyer_id']) ? intval($_POST['buyer_id']) : 0;
        $convId = isset($_POST['conv_id']) ? intval($_POST['conv_id']) : 0;
        $offerId = isset($_POST['offer_id']) ? intval($_POST['offer_id']) : 0;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $userId = $_SESSION['user_id'];

        if ($action === 'create' || $action === 'counter') {
            $isCounter = ($action === 'counter');
            $this->chatModel->createOrUpdateOffer($listingId, $buyerId, $userId, $price, $quantity, $isCounter);
            
            $msgQty = $isCounter ? "" : " (Số lượng: $quantity)";
            $msg = ($action === 'create') ? "🤝 Đã gửi yêu cầu trả giá: " : "🤝 Đã phản hồi mức giá (Counter): ";
            $msg .= number_format($price, 0, ',', '.') . "đ" . $msgQty;
            $this->chatModel->sendTradeMessage($convId, $userId, 1, $msg);
        } 
        elseif ($action === 'accept') {
            $this->chatModel->respondOffer($offerId, 2, $price); 
            $this->chatModel->syncCartAfterDealAccept($buyerId, $listingId, $offerId, $price);
            $this->chatModel->sendTradeMessage($convId, $userId, 1, "✅ Đã CHẤP NHẬN giá " . number_format($price, 0, ',', '.') . "đ. Sản phẩm đã cập nhật giá trong giỏ hàng (Hạn thanh toán 24h).");
        } 
        elseif ($action === 'reject') {
            $this->chatModel->respondOffer($offerId, 3); 
            $this->chatModel->sendTradeMessage($convId, $userId, 1, "❌ Đã TỪ CHỐI mức giá đề xuất.");
        }
        
        echo json_encode(['status' => 'success']);
        
        exit; // 🚨 CỰC KỲ QUAN TRỌNG: Bắt buộc ngắt luồng tại đây!
    }

   // ===============================================
    // API Gửi tin nhắn (ĐÃ FIX LỖI LAG VÀ KẸT ĐANG GỬI)
    // ===============================================
    public function sendAjax() {
        if (ob_get_level() > 0) @ob_clean(); // Quét sạch rác HTML
        header('Content-Type: application/json; charset=utf-8');
        
        $convId = isset($_POST['conv_id']) ? intval($_POST['conv_id']) : 0;
        $chatType = isset($_POST['chat_type']) ? $_POST['chat_type'] : 'trade';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $typeId = 1; 
        $attachment = null;

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $typeId = 2; 
            $uploadDir = __DIR__ . '/../uploads/chat/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName)) {
                $attachment = 'uploads/chat/' . $fileName;
            }
        }

        if ($convId > 0 && ($content !== '' || $attachment !== null)) {
            if ($chatType === 'trade') {
                $this->chatModel->sendTradeMessage($convId, $_SESSION['user_id'], $typeId, $content, $attachment);
            } else {
                $this->chatModel->sendSupportMessage($convId, $_SESSION['user_id'], $typeId, $content, $attachment);
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu rỗng']);
        }
        
        exit; // 🚨 CỰC KỲ QUAN TRỌNG: Bắt buộc ngắt luồng tại đây!
    }

    public function startTrade() {
        $listingId = isset($_GET['listing_id']) ? intval($_GET['listing_id']) : 0;
        $sellerId = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;
        if ($listingId > 0 && $sellerId > 0 && $sellerId != $_SESSION['user_id']) {
            $convId = $this->chatModel->findOrCreateTradeConv($_SESSION['user_id'], $sellerId, $listingId);
            $openDeal = isset($_GET['deal']) ? '&deal=1' : '';
            header("Location: index.php?controller=chat&active_trade=$convId&listing_id=$listingId&seller_id=$sellerId$openDeal");
            exit;
        }
        header("Location: index.php?controller=chat");
    }

    public function startSupport() {
        $convId = $this->chatModel->findOrCreateSupportConv($_SESSION['user_id']);
        header("Location: index.php?controller=chat&active_support=$convId");
        exit;
    }
}
?>