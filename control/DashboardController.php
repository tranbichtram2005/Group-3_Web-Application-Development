<?php
require_once 'model/DashboardModel.php';

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Ép đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $this->dashboardModel = new DashboardModel();
    }

    public function index() {
        $sellerId = $_SESSION['user_id'];
        
        // Bắt bộ lọc ngày tháng (Mặc định là 30 ngày gần nhất)
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Kéo data từ Model
        $stats = $this->dashboardModel->getOverviewStats($sellerId, $startDate, $endDate);
        $pendingCount = $this->dashboardModel->getPendingOrdersCount($sellerId);
        $chartData = $this->dashboardModel->getRevenueChartData($sellerId, $startDate, $endDate);

        // Chuẩn bị mảng để ném vào Javascript vẽ Chart.js
        $dates = [];
        $revenues = [];
        foreach ($chartData as $row) {
            $dates[] = date('d/m', strtotime($row['order_date']));
            $revenues[] = (float)$row['daily_revenue'];
        }

        require_once __DIR__ . '/../view/app/seller_dashboard.php';
    }

    // Chức năng xuất dữ liệu ra file Excel (CSV)
    public function export() {
        $sellerId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $chartData = $this->dashboardModel->getRevenueChartData($sellerId, $startDate, $endDate);

        // Header ép trình duyệt tải file
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Bao_Cao_Doanh_Thu_2Life_' . date('Ymd') . '.csv');

        $output = fopen('php://output', 'w');
        // Ghi BOM để Excel không bị lỗi font Tiếng Việt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cột tiêu đề
        fputcsv($output, ['Ngày giao dịch', 'Doanh thu (VNĐ)']);
        
        $total = 0;
        foreach ($chartData as $row) {
            fputcsv($output, [$row['order_date'], $row['daily_revenue']]);
            $total += $row['daily_revenue'];
        }
        fputcsv($output, ['TỔNG CỘNG', $total]);
        
        fclose($output);
        exit;
    }
}