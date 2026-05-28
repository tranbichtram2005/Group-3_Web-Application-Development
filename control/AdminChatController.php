<?php
require_once __DIR__ . '/../model/ChatModel.php';
require_once __DIR__ . '/../model/Database.php';

class AdminChatController {
    private $chatModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        // 🚨 CHỐT BẢO MẬT: Phải đăng nhập và có role_id = 2 (Admin)
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            die("<h2 style='color:red; text-align:center; margin-top:50px;'>⛔ TRUY CẬP BỊ TỪ CHỐI. Bạn không có quyền Admin!</h2>");
        }
        $this->chatModel = new ChatModel();
    }

    public function index() {
        $adminId = $_SESSION['user_id'];
        // Lấy danh sách toàn bộ Ticket
        $supportConvs = $this->chatModel->getAdminSupportConversations($adminId);
        require_once __DIR__ . '/../view/admin/chat.php';
    }

    // LẤY TIN NHẮN VÀ TRẠNG THÁI PHÒNG
    public function getMessagesAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        $convId = isset($_GET['conv_id']) ? intval($_GET['conv_id']) : 0;
        $lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
        
        if ($convId > 0) {
            $db = new Database(); $conn = $db->getConnection();
            
            // Báo đã đọc tin nhắn của User
            $conn->prepare("UPDATE support_messages SET is_read = 1 WHERE support_conversation_id = ? AND sender_type_id = 1 AND is_read = 0")->execute([$convId]);
            
            // Lấy tin nhắn
            $messages = $this->chatModel->getSupportMessages($convId, $lastId);
            
            // Lấy trạng thái phòng (để biết ai đang nhận, đã đóng chưa)
            $stmt = $conn->prepare("SELECT status_id, admin_id FROM support_conversations WHERE id = ?");
            $stmt->execute([$convId]);
            $convInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $messages, 'conv_info' => $convInfo]);
        }
        exit;
    }

    // ADMIN BẤM "TIẾP NHẬN TICKET"
    public function claimAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        $convId = isset($_POST['conv_id']) ? intval($_POST['conv_id']) : 0;
        
        if ($convId > 0) {
            $this->chatModel->claimSupportTicket($convId, $_SESSION['user_id']);
            $this->chatModel->sendAdminSupportMessage($convId, $_SESSION['user_id'], 1, "👋 Xin chào! Admin 2Life đã tiếp nhận yêu cầu. Mình có thể hỗ trợ gì cho bạn?");
            echo json_encode(['status' => 'success']);
        }
        exit;
    }

    // ADMIN BẤM "ĐÓNG TICKET"
    public function closeAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        $convId = isset($_POST['conv_id']) ? intval($_POST['conv_id']) : 0;
        
        if ($convId > 0) {
            $this->chatModel->closeSupportTicket($convId);
            $this->chatModel->sendAdminSupportMessage($convId, $_SESSION['user_id'], 1, "🔒 Yêu cầu này đã được giải quyết và ĐÓNG lại. Cảm ơn bạn đã đồng hành cùng 2Life!");
            echo json_encode(['status' => 'success']);
        }
        exit;
    }

    // ADMIN GỬI TIN NHẮN
    public function sendAjax() {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        
        $convId = isset($_POST['conv_id']) ? intval($_POST['conv_id']) : 0;
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $typeId = 1; $attachment = null;

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
            $this->chatModel->sendAdminSupportMessage($convId, $_SESSION['user_id'], $typeId, $content, $attachment);
            echo json_encode(['status' => 'success']);
        }
        exit;
    }
}
?>