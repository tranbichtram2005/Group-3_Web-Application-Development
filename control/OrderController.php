<?php
require_once __DIR__ . '/../model/Database.php';

class OrderController {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // ===============================================
    // HIỂN THỊ DANH SÁCH ĐƠN HÀNG MUA
    // ===============================================
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Đếm số lượng đơn hàng theo từng trạng thái (Đủ 6 trạng thái)
        $countSql = "SELECT status_id, COUNT(id) as total FROM orders WHERE buyer_id = :buyer_id GROUP BY status_id";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute([':buyer_id' => $userId]);
        $rawCounts = $countStmt->fetchAll(PDO::FETCH_ASSOC);

        $orderCounts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        foreach ($rawCounts as $row) {
            $orderCounts[$row['status_id']] = (int)$row['total'];
            $orderCounts[0] += (int)$row['total']; 
        }

        $statusFilter = isset($_GET['status']) ? (int)$_GET['status'] : 0;
        
        $sql = "SELECT o.*, os.name as status_name 
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                WHERE o.buyer_id = :buyer_id";
                
        if ($statusFilter > 0) {
            $sql .= " AND o.status_id = :status_id";
        }
        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $params = [':buyer_id' => $userId];
        if ($statusFilter > 0) {
            $params[':status_id'] = $statusFilter;
        }
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $key => $order) {
            // ĐÃ THÊM: Câu lệnh SELECT COUNT() để kiểm tra xem đơn hàng này đã được review chưa
            $itemSql = "SELECT oi.*, p.title, img.image_url, 
                               (SELECT COUNT(id) FROM reviews WHERE order_id = oi.order_id AND listing_id = oi.listing_id) as is_reviewed
                        FROM order_items oi
                        JOIN product_listings p ON oi.listing_id = p.id
                        LEFT JOIN listing_images img ON p.id = img.listing_id AND img.is_primary = 1
                        WHERE oi.order_id = :order_id";
            $itemStmt = $this->db->prepare($itemSql);
            $itemStmt->execute([':order_id' => $order['id']]);
            $orders[$key]['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        require_once __DIR__ . '/../view/app/order_history.php';
    }

    // ===============================================
    // XỬ LÝ: XÁC NHẬN ĐÃ NHẬN HÀNG (Hoàn thành - Status 4)
    // ===============================================
    public function confirmReceived() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $stmt = $this->db->prepare("UPDATE orders SET status_id = 4 WHERE id = :id AND status_id = 3");
            $stmt->execute([':id' => $orderId]);
            
            $_SESSION['toast_msg'] = 'Tuyệt vời! Đơn hàng đã hoàn thành.';
            header("Location: index.php?controller=order");
            exit;
        }
    }

    // ===============================================
    // XỬ LÝ: CẬP NHẬT THÔNG TIN ĐƠN HÀNG
    // ===============================================
    public function updateShippingInfo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $streetAddress = trim($_POST['street_address']);
            $shippingNote = trim($_POST['shipping_note']);

            $stmt = $this->db->prepare("UPDATE orders SET street_address = :street_address, shipping_note = :shipping_note WHERE id = :id AND status_id = 1");
            $stmt->execute([':street_address' => $streetAddress, ':shipping_note' => $shippingNote, ':id' => $orderId]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['toast_msg'] = 'Cập nhật thông tin giao nhận thành công!';
            } else {
                $_SESSION['toast_msg'] = 'Đơn hàng đã xử lý, không thể đổi thông tin!';
            }
            header("Location: index.php?controller=order");
            exit;
        }
    }

    // ===============================================
    // XỬ LÝ: HỦY ĐƠN HÀNG (Status 6)
    // ===============================================
    public function cancelOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $userId = $_SESSION['user_id']; 
            $reason = trim($_POST['cancel_reason'] ?? 'Người mua chủ động hủy đơn');

            $stmt = $this->db->prepare("UPDATE orders SET status_id = 6, cancel_reason = :reason, cancelled_by = :cancelled_by WHERE id = :id AND status_id IN (1, 2)");
            $stmt->execute([
                ':reason' => $reason, 
                ':cancelled_by' => $userId, 
                ':id' => $orderId
            ]);
            
            $_SESSION['toast_msg'] = 'Bạn đã hủy đơn hàng thành công!';
            header("Location: index.php?controller=order");
            exit;
        }
    }

    // ===============================================
    // XỬ LÝ: THÊM ĐÁNH GIÁ SẢN PHẨM (Khớp CSDL reviews)
    // ===============================================
    public function submitReview() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $listingId = (int)$_POST['listing_id'];
            $orderId = (int)$_POST['order_id'];
            $rating = (int)$_POST['rating'];
            $comment = trim($_POST['comment']);

            // 1. KIỂM TRA XEM ĐÃ ĐÁNH GIÁ CHƯA
            $checkStmt = $this->db->prepare("SELECT id FROM reviews WHERE order_id = ? AND listing_id = ?");
            $checkStmt->execute([$orderId, $listingId]);
            
            if ($checkStmt->rowCount() > 0) {
                // Nếu đã đánh giá rồi thì báo lỗi
                $_SESSION['toast_msg'] = 'Sản phẩm này cậu đã đánh giá rồi nhé!';
            } else {
                // 2. NẾU CHƯA CÓ THÌ MỚI CHO LƯU VÀO DATABASE
                $stmt = $this->db->prepare("INSERT INTO reviews (reviewer_id, listing_id, order_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$userId, $listingId, $orderId, $rating, $comment]);
                $_SESSION['toast_msg'] = 'Cảm ơn cậu đã gửi đánh giá sản phẩm!';
            }
            
            header("Location: index.php?controller=order");
            exit;
        }
    }
}
?>