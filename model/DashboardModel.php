<?php
require_once 'model/Database.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy thống kê tổng quan (Chỉ tính đơn Hoàn Thành - status_id = 4)
    public function getOverviewStats($sellerId, $startDate, $endDate) {
        $sql = "SELECT 
                    COUNT(id) as total_orders, 
                    SUM(total_amount) as total_revenue 
                FROM orders 
                WHERE seller_id = :seller_id AND status_id = 4";
        
        $params = [':seller_id' => $sellerId];

        if (!empty($startDate) && !empty($endDate)) {
            $sql .= " AND DATE(created_at) >= :start_date AND DATE(created_at) <= :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy data vẽ biểu đồ (Doanh thu theo từng ngày)
    public function getRevenueChartData($sellerId, $startDate, $endDate) {
        $sql = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_revenue 
                FROM orders 
                WHERE seller_id = :seller_id AND status_id = 4";
        
        $params = [':seller_id' => $sellerId];

        if (!empty($startDate) && !empty($endDate)) {
            $sql .= " AND DATE(created_at) >= :start_date AND DATE(created_at) <= :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }

        $sql .= " GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm số đơn đang chờ xử lý (Status 1 và 2)
    public function getPendingOrdersCount($sellerId) {
        $stmt = $this->conn->prepare("SELECT COUNT(id) FROM orders WHERE seller_id = :seller_id AND status_id IN (1, 2)");
        $stmt->execute([':seller_id' => $sellerId]);
        return $stmt->fetchColumn();
    }
}