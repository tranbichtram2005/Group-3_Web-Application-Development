<?php
require_once __DIR__ . '/Database.php';

class ChatModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->conn->exec("SET time_zone = '+07:00';");
    }

    // 1. TỰ ĐỘNG HỦY DEAL QUÁ 24H (QUÉT NGẦM)
    public function autoCloseExpiredOffers() {
        $sql = "UPDATE price_offers 
                SET status_id = 4, updated_at = CURRENT_TIMESTAMP 
                WHERE status_id IN (1, 5) AND updated_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)";
        $this->conn->prepare($sql)->execute();
    }

    // 2. LẤY DANH SÁCH CUỘC TRÒ CHUYỆN MUA BÁN
    public function getTradeConversations($userId) {
        $sql = "SELECT tc.*, pl.title AS product_title, pl.price AS product_price,
                       (SELECT image_url FROM listing_images WHERE listing_id = pl.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS product_image,
                       u_b.full_name AS buyer_name, u_b.avatar_url AS buyer_avatar,
                       u_s.full_name AS seller_name, u_s.avatar_url AS seller_avatar
                FROM trade_conversations tc
                JOIN product_listings pl ON tc.listing_id = pl.id
                JOIN users u_b ON tc.buyer_id = u_b.id
                JOIN users u_s ON tc.seller_id = u_s.id
                WHERE tc.buyer_id = :user_id OR tc.seller_id = :user_id
                ORDER BY tc.last_message_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. POLLING: LẤY TIN NHẮN THEO LAST_ID ĐỂ CHỐNG LAG
    public function getTradeMessages($convId, $lastId = 0) {
        $sql = "SELECT tm.* FROM trade_messages tm 
                WHERE tm.trade_conversation_id = :conv_id AND tm.id > :last_id 
                ORDER BY tm.sent_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':conv_id', $convId, PDO::PARAM_INT);
        $stmt->bindParam(':last_id', $lastId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendTradeMessage($convId, $senderId, $typeId, $content, $attachment = null) {
        $sql = "INSERT INTO trade_messages (trade_conversation_id, sender_id, message_type_id, content, attachment_url) 
                VALUES (?, ?, ?, ?, ?)";
        $this->conn->prepare($sql)->execute([$convId, $senderId, $typeId, $content, $attachment]);
        $this->conn->prepare("UPDATE trade_conversations SET last_message_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$convId]);
        return true;
    }

    // 4. KIỂM TRA DEAL ĐANG ACTIVE
    public function getActiveOffer($listingId, $buyerId) {
        $sql = "SELECT po.id, po.status_id, po.quantity, po.updated_at, pod.proposed_price, pod.proposed_by 
                FROM price_offers po
                JOIN price_offer_details pod ON po.id = pod.offer_id
                WHERE po.listing_id = :l_id AND po.buyer_id = :b_id AND po.status_id IN (1, 2, 5)
                ORDER BY pod.created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':l_id'=>$listingId, ':b_id'=>$buyerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. TẠO HOẶC TRẢ GIÁ LẠI (COUNTER) DEAL
    public function createOrUpdateOffer($listingId, $buyerId, $proposedBy, $price, $quantity, $isCounter = false) {
        $stmtCheck = $this->conn->prepare("SELECT id, quantity FROM price_offers WHERE listing_id = ? AND buyer_id = ? AND status_id IN (1, 2, 5)");
        $stmtCheck->execute([$listingId, $buyerId]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $statusId = $isCounter ? 5 : 1; 

        if ($existing) {
            $offerId = $existing['id'];
            $finalQty = $isCounter ? $existing['quantity'] : $quantity; 
            $this->conn->prepare("UPDATE price_offers SET status_id = ?, quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                       ->execute([$statusId, $finalQty, $offerId]);
        } else {
            $this->conn->prepare("INSERT INTO price_offers (listing_id, buyer_id, quantity, status_id) VALUES (?, ?, ?, ?)")
                       ->execute([$listingId, $buyerId, $quantity, $statusId]);
            $offerId = $this->conn->lastInsertId();
        }

        $this->conn->prepare("INSERT INTO price_offer_details (offer_id, proposed_by, proposed_price) VALUES (?, ?, ?)")
                   ->execute([$offerId, $proposedBy, $price]);
        return true;
    }

    // 6. CHỐT HOẶC TỪ CHỐI DEAL
    public function respondOffer($offerId, $statusId, $acceptedPrice = null) {
        return $this->conn->prepare("UPDATE price_offers SET status_id = ?, accepted_price = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                   ->execute([$statusId, $acceptedPrice, $offerId]);
    }

    // 7. ĐỒNG BỘ GIỎ HÀNG NGẦM KHI DEAL ĐƯỢC CHẤP NHẬN
    public function syncCartAfterDealAccept($buyerId, $listingId, $offerId, $price) {
        $stmt = $this->conn->prepare("SELECT id FROM carts WHERE user_id = ?");
        $stmt->execute([$buyerId]);
        if ($cart = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sql = "UPDATE cart_items SET offer_id = ?, price_snapshot = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ? AND listing_id = ?";
            $this->conn->prepare($sql)->execute([$offerId, $price, $cart['id'], $listingId]);
        }
    }

    // 8. TÌM/TẠO PHÒNG CHAT TỪ TRANG CHI TIẾT
    public function findOrCreateTradeConv($buyerId, $sellerId, $listingId) {
        $stmtCheck = $this->conn->prepare("SELECT id FROM trade_conversations WHERE buyer_id = ? AND seller_id = ? AND listing_id = ?");
        $stmtCheck->execute([$buyerId, $sellerId, $listingId]);
        if ($conv = $stmtCheck->fetch(PDO::FETCH_ASSOC)) return $conv['id'];
        $this->conn->prepare("INSERT INTO trade_conversations (buyer_id, seller_id, listing_id) VALUES (?, ?, ?)")->execute([$buyerId, $sellerId, $listingId]);
        return $this->conn->lastInsertId();
    }
// Đánh dấu mình đã đọc tin nhắn của đối phương (Chỉ update những tin đang is_read = 0)
    public function markMessagesAsRead($convId, $userId) {
        $sql = "UPDATE trade_messages SET is_read = 1 WHERE trade_conversation_id = ? AND sender_id != ? AND is_read = 0";
        $this->conn->prepare($sql)->execute([$convId, $userId]);
    }

    // Kiểm tra xem đối phương đã đọc tin nhắn của mình tới ID nào rồi
    public function getLastReadMessageId($convId, $userId) {
        $sql = "SELECT MAX(id) as max_read_id FROM trade_messages WHERE trade_conversation_id = ? AND sender_id = ? AND is_read = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$convId, $userId]);
        $res = $stmt->fetch();
        return $res['max_read_id'] ?? 0;
    }

// Đếm TỔNG số tin nhắn chưa đọc (Gồm cả Mua bán + Admin)
    public function countTotalUnreadMessages($userId) {
        // Đếm tin Mua Bán
        $sql1 = "SELECT COUNT(tm.id) FROM trade_messages tm JOIN trade_conversations tc ON tm.trade_conversation_id = tc.id WHERE (tc.buyer_id = ? OR tc.seller_id = ?) AND tm.sender_id != ? AND tm.is_read = 0";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute([$userId, $userId, $userId]);
        $tradeCount = $stmt1->fetchColumn() ?: 0;

        // Đếm tin Hỗ Trợ (Từ Admin gửi tới User)
        $sql2 = "SELECT COUNT(sm.id) FROM support_messages sm JOIN support_conversations sc ON sm.support_conversation_id = sc.id WHERE sc.user_id = ? AND sm.sender_type_id = 2 AND sm.is_read = 0";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([$userId]);
        $supportCount = $stmt2->fetchColumn() ?: 0;

        return $tradeCount + $supportCount;
    }

   // Đếm số tin chưa đọc CỦA TỪNG PHÒNG CHAT (Phân biệt rõ Mua Bán / Hỗ Trợ)
    public function getUnreadCountPerConversation($userId) {
        $map = [];
        // Mua Bán (Gắn tiền tố trade_)
        $sql1 = "SELECT tc.id as conv_id, COUNT(tm.id) as unread_count FROM trade_conversations tc JOIN trade_messages tm ON tc.id = tm.trade_conversation_id WHERE (tc.buyer_id = ? OR tc.seller_id = ?) AND tm.sender_id != ? AND tm.is_read = 0 GROUP BY tc.id";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute([$userId, $userId, $userId]);
        foreach($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map['trade_' . $row['conv_id']] = $row['unread_count'];
        }

        // Hỗ Trợ (Gắn tiền tố support_)
        $sql2 = "SELECT sc.id as conv_id, COUNT(sm.id) as unread_count FROM support_conversations sc JOIN support_messages sm ON sc.id = sm.support_conversation_id WHERE sc.user_id = ? AND sm.sender_type_id = 2 AND sm.is_read = 0 GROUP BY sc.id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([$userId]);
        foreach($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map['support_' . $row['conv_id']] = $row['unread_count'];
        }
        return $map;
    }

    // Hàm báo "Đã xem" cho tin nhắn Admin
    public function markSupportMessagesAsRead($convId) {
        $sql = "UPDATE support_messages SET is_read = 1 WHERE support_conversation_id = ? AND sender_type_id = 2 AND is_read = 0";
        $this->conn->prepare($sql)->execute([$convId]);
    }
    
    // ==========================================
    // NHÓM HÀM ADMIN SUPPORT (GIỮ NGUYÊN)
    // ==========================================
    public function getSupportConversations($userId) {
        $sql = "SELECT sc.*, cat.name AS category_name FROM support_conversations sc JOIN support_categories cat ON sc.category_id = cat.id WHERE sc.user_id = :user_id ORDER BY sc.last_message_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSupportMessages($convId, $lastId = 0) {
        $sql = "SELECT sm.* FROM support_messages sm WHERE sm.support_conversation_id = :conv_id AND sm.id > :last_id ORDER BY sm.sent_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':conv_id' => $convId, ':last_id' => $lastId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function sendSupportMessage($convId, $senderId, $typeId, $content, $attachment = null) {
        $sql = "INSERT INTO support_messages (support_conversation_id, sender_id, sender_type_id, message_type_id, content, attachment_url) VALUES (?, ?, 1, ?, ?, ?)";
        $this->conn->prepare($sql)->execute([$convId, $senderId, $typeId, $content, $attachment]);
        $this->conn->prepare("UPDATE support_conversations SET last_message_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$convId]);
        return true;
    }
    public function findOrCreateSupportConv($userId, $categoryId = 10) {
        $stmtCheck = $this->conn->prepare("SELECT id FROM support_conversations WHERE user_id = ? AND status_id IN (1, 2) ORDER BY last_message_at DESC LIMIT 1");
        $stmtCheck->execute([$userId]);
        if ($conv = $stmtCheck->fetch(PDO::FETCH_ASSOC)) return $conv['id'];
        $this->conn->prepare("INSERT INTO support_conversations (user_id, category_id, status_id) VALUES (?, ?, 1)")->execute([$userId, $categoryId]);
        return $this->conn->lastInsertId();
    }

    // ==========================================
    // NHÓM HÀM CHO ADMIN XỬ LÝ HỖ TRỢ (HELPDESK)
    // ==========================================
    public function getAdminSupportConversations($adminId) {
        $sql = "SELECT sc.*, cat.name AS category_name, u.full_name AS user_name, u.avatar_url AS user_avatar
                FROM support_conversations sc JOIN support_categories cat ON sc.category_id = cat.id JOIN users u ON sc.user_id = u.id
                WHERE (sc.admin_id IS NULL OR sc.admin_id = ?) ORDER BY sc.status_id ASC, sc.last_message_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function claimSupportTicket($convId, $adminId) {
        return $this->conn->prepare("UPDATE support_conversations SET admin_id = ?, status_id = 2 WHERE id = ? AND admin_id IS NULL")->execute([$adminId, $convId]);
    }

    public function sendAdminSupportMessage($convId, $adminId, $typeId, $content, $attachment = null) {
        $this->conn->prepare("INSERT INTO support_messages (support_conversation_id, sender_id, sender_type_id, message_type_id, content, attachment_url) VALUES (?, ?, 2, ?, ?, ?)")->execute([$convId, $adminId, $typeId, $content, $attachment]);
        $this->conn->prepare("UPDATE support_conversations SET last_message_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$convId]);
        return true;
    }
    
    public function closeSupportTicket($convId) {
        return $this->conn->prepare("UPDATE support_conversations SET status_id = 3 WHERE id = ?")->execute([$convId]);
    }
}
?>