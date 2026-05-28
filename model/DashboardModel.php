<?php
require_once 'model/Database.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy thống kê tổng quan 3 thẻ chỉ số (Doanh thu, Đơn thành công, Đơn chờ xử lý)
    public function getSummaryStats($sellerId, $startDate, $endDate) {
        $stmtRev = $this->conn->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE seller_id = ? AND status_id = 4 AND DATE(created_at) BETWEEN ? AND ?");
        $stmtRev->execute([$sellerId, $startDate, $endDate]);
        $revResult = $stmtRev->fetch(PDO::FETCH_ASSOC);

        $stmtOrd = $this->conn->prepare("SELECT COUNT(id) as total_orders FROM orders WHERE seller_id = ? AND status_id = 4 AND DATE(created_at) BETWEEN ? AND ?");
        $stmtOrd->execute([$sellerId, $startDate, $endDate]);
        $ordResult = $stmtOrd->fetch(PDO::FETCH_ASSOC);

        $stmtPend = $this->conn->prepare("SELECT COUNT(id) as pending_orders FROM orders WHERE seller_id = ? AND status_id IN (1, 2) AND DATE(created_at) BETWEEN ? AND ?");
        $stmtPend->execute([$sellerId, $startDate, $endDate]);
        $pendResult = $stmtPend->fetch(PDO::FETCH_ASSOC);

        return [
            'revenue' => $revResult['total_revenue'] ?? 0,
            'orders'  => $ordResult['total_orders'] ?? 0,
            'pending' => $pendResult['pending_orders'] ?? 0
        ];
    }

    // Biểu đồ 1: Doanh thu theo từng ngày (Line Chart)
    public function getDailyRevenue($sellerId, $startDate, $endDate) {
        $stmt = $this->conn->prepare("SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_revenue 
                                      FROM orders 
                                      WHERE seller_id = ? AND status_id = 4 AND DATE(created_at) BETWEEN ? AND ? 
                                      GROUP BY DATE(created_at) ORDER BY order_date ASC");
        $stmt->execute([$sellerId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Biểu đồ 2: Phân bổ trạng thái đơn hàng (Doughnut Chart)
    public function getOrderStatusDistribution($sellerId, $startDate, $endDate) {
        $stmt = $this->conn->prepare("SELECT s.name as status_name, COUNT(o.id) as order_count 
                                      FROM orders o
                                      JOIN order_statuses s ON o.status_id = s.id
                                      WHERE o.seller_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
                                      GROUP BY s.id ORDER BY order_count DESC");
        $stmt->execute([$sellerId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Biểu đồ 3: Top 5 Sản phẩm bán chạy nhất (Bar Chart)
    public function getTopSellingProducts($sellerId, $startDate, $endDate) {
        $stmt = $this->conn->prepare("SELECT p.title as product_name, SUM(oi.quantity) as total_sold 
                                      FROM order_items oi
                                      JOIN orders o ON oi.order_id = o.id
                                      JOIN product_listings p ON oi.listing_id = p.id
                                      WHERE o.seller_id = ? AND o.status_id = 4 AND DATE(o.created_at) BETWEEN ? AND ?
                                      GROUP BY p.id ORDER BY total_sold DESC LIMIT 5");
        $stmt->execute([$sellerId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính năng Xuất Excel: Lấy toàn bộ thông tin chi tiết (Info KH, Giá, Ghi chú...)
    // Tính năng Xuất Excel: Lấy toàn bộ thông tin chi tiết (Info KH, Giá, Ghi chú...)
    // Tính năng Xuất Excel: Lấy toàn bộ thông tin chi tiết (Info KH, Giá, Ghi chú...)
    public function getDetailedOrdersForExport($sellerId, $startDate, $endDate) {
        // ĐÃ FIX: Nối thêm bảng payments (pay) để lấy pay.method_id
        $query = "SELECT o.id as order_id, o.created_at, 
                         u.full_name as buyer_name, u.phone as buyer_phone, o.street_address,
                         p.title as product_name, oi.quantity, oi.unit_price, 
                         (oi.quantity * oi.unit_price) as item_total,
                         s.name as status_name, 
                         IF(pay.method_id = 1, 'Tiền mặt (COD)', 'VNPay') as payment_method,
                         o.shipping_note
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  JOIN users u ON o.buyer_id = u.id
                  JOIN product_listings p ON oi.listing_id = p.id
                  JOIN order_statuses s ON o.status_id = s.id
                  LEFT JOIN payments pay ON o.id = pay.order_id
                  WHERE o.seller_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
                  ORDER BY o.created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sellerId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>