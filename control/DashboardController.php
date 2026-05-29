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
        if (session_status() == PHP_SESSION_NONE) session_start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $_SESSION['show_unauth_modal'] = true;
            header("Location: index.php?controller=home");
            exit;
        }

        // Khởi tạo PDO ở đây
        $database = new Database();
        $pdo = $database->getConnection();

        if (!$pdo) {
            $_SESSION['show_unauth_modal'] = true;
            header("Location: index.php?controller=home");
            exit;
        }

        // Query kiểm tra role và seller_profile
        $stmt = $pdo->prepare("
            SELECT u.id, r.id AS role_id, sp.id AS seller_profile_id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN seller_profiles sp ON sp.user_id = u.id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['show_unauth_modal'] = true;
            header("Location: index.php?controller=home");
            exit;
        }

        $isAdmin  = (int)$user['role_id'] === 2;
        $isSeller = !empty($user['seller_profile_id']);

        if (!$isAdmin && !$isSeller) {
            $_SESSION['show_unauth_modal'] = true;
            header("Location: index.php?controller=home");
            exit; 
        }
        $this->dashboardModel = new DashboardModel();
    }

    public function index() {
        $sellerId = $_SESSION['user_id'];
        
        // Bắt bộ lọc ngày tháng (Mặc định là 30 ngày gần nhất)
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Kéo data từ Model (Các hàm mới đã được nâng cấp)
        $stats = $this->dashboardModel->getSummaryStats($sellerId, $startDate, $endDate);
        
        // Kéo dữ liệu cho 3 biểu đồ insight
        $dailyRevenue = $this->dashboardModel->getDailyRevenue($sellerId, $startDate, $endDate);
        $statusDist   = $this->dashboardModel->getOrderStatusDistribution($sellerId, $startDate, $endDate);
        $topProducts  = $this->dashboardModel->getTopSellingProducts($sellerId, $startDate, $endDate);

        // ============================================
        // XỬ LÝ DỮ LIỆU ĐỂ NÉM VÀO JAVASCRIPT (CHART.JS)
        // ============================================
        
        // 1. Dữ liệu Biểu đồ Doanh thu (Line/Area Chart)
        $chartDates = []; 
        $chartRevenues = [];
        foreach ($dailyRevenue as $row) {
            $chartDates[] = date('d/m', strtotime($row['order_date']));
            $chartRevenues[] = (float)$row['daily_revenue'];
        }

        // 2. Dữ liệu Biểu đồ Trạng thái (Doughnut Chart)
        $statusLabels = []; 
        $statusCounts = [];
        foreach ($statusDist as $status) {
            $statusLabels[] = $status['status_name'];
            $statusCounts[] = (int)$status['order_count'];
        }

        // 3. Dữ liệu Biểu đồ Top Sản phẩm (Bar Chart)
        $productLabels = []; 
        $productSold = [];
        foreach ($topProducts as $prod) {
            // Cắt ngắn tên SP nếu dài quá 20 ký tự để khung chữ trên biểu đồ không bị tràn
            $name = mb_strlen($prod['product_name']) > 20 ? mb_substr($prod['product_name'], 0, 20) . '...' : $prod['product_name'];
            $productLabels[] = $name;
            $productSold[] = (int)$prod['total_sold'];
        }

        require_once __DIR__ . '/../view/app/Seller_dashboard.php';
    }

    // Chức năng xuất dữ liệu ra file Excel (CSV) - FULL Thông tin chi tiết khách hàng
    public function exportCsv() { 
        $sellerId = $_SESSION['user_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Gọi hàm lấy toàn bộ chi tiết đơn hàng
        $detailedOrders = $this->dashboardModel->getDetailedOrdersForExport($sellerId, $startDate, $endDate);

        // Header ép trình duyệt tải file
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Bao_Cao_Chi_Tiet_Don_Hang_2Life_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        
        // Ghi BOM để mở bằng Microsoft Excel không bị lỗi font Tiếng Việt
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cột tiêu đề FULL thông tin
        fputcsv($output, [
            'Mã Đơn', 'Ngày Đặt', 'Tên Khách Hàng', 'Số Điện Thoại', 'Địa Chỉ Giao Hàng', 
            'Tên Sản Phẩm', 'Số Lượng', 'Đơn Giá (VNĐ)', 'Thành Tiền (VNĐ)', 
            'Phương Thức Thanh Toán', 'Trạng Thái', 'Ghi Chú'
        ]);
        
        foreach ($detailedOrders as $row) {
            fputcsv($output, [
                '2L' . $row['order_id'],
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['buyer_name'],
                "'" . $row['buyer_phone'], 
                $row['street_address'],
                $row['product_name'],
                $row['quantity'],
                $row['unit_price'],
                $row['item_total'],
                $row['payment_method'],
                $row['status_name'],
                $row['shipping_note']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>