<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/ManageOrderSellerModel.php';

class ManageOrderSellerController {
    private $db;
    private $orderModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $database = new Database();
        $dbConn = $database->getConnection();

        $roleId = $_SESSION['role_id'] ?? 1;
        $isSeller = $_SESSION['is_seller'] ?? 0;
// Nếu KHÔNG PHẢI là Admin (role 2) VÀ CŨNG KHÔNG PHẢI là Người bán (is_seller 1)
        if ($roleId != 2 && $isSeller != 1) {
            
            // Gửi một tín hiệu vào Session để yêu cầu bật Modal
            $_SESSION['show_unauth_modal'] = true;
            
            // Đá người dùng về lại trang chủ an toàn
            header("Location: index.php?controller=home");
            exit; 
        }

        $this->orderModel = new ManageOrderSellerModel($dbConn);
    }

    // [HIỂN THỊ TRANG CHỦ DANH SÁCH ĐƠN HÀNG]
    public function index() {
        $sellerId = $_SESSION['user_id'];
        $statusFilter = isset($_GET['status']) ? (int)$_GET['status'] : 0;
        
        $orders = $this->orderModel->getOrdersBySeller($sellerId, $statusFilter);
        $orderCounts = $this->orderModel->getOrderStatusCounts($sellerId);

        require_once __DIR__ . '/../view/app/manage_order_seller.php';
    }

    // [API AJAX: FETCH DANH SÁCH HTML KHI CHUYỂN TAB]
    public function fetchList() {
        $sellerId = $_SESSION['user_id'];
        $statusFilter = isset($_GET['status']) ? (int)$_GET['status'] : 0;
        
        $orders = $this->orderModel->getOrdersBySeller($sellerId, $statusFilter);
        $orderCounts = $this->orderModel->getOrderStatusCounts($sellerId);

        // Bắt đầu bộ nhớ đệm để render file list partial thành HTML String
        ob_start();
        require __DIR__ . '/../view/app/manage_order_seller_list.php';
        $html = ob_get_clean();

        echo json_encode([
            'success' => true,
            'html' => $html,
            'counts' => $orderCounts
        ]);
        exit;
    }

    // [HIỂN THỊ TRANG CHI TIẾT ĐƠN HÀNG]
    public function detail() {
        $sellerId = $_SESSION['user_id'];
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $order = $this->orderModel->getOrderById($orderId, $sellerId);
        if (!$order) {
            $_SESSION['toast_msg'] = 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập!';
            header("Location: index.php?controller=manageorderseller");
            exit;
        }

        $items = $this->orderModel->getOrderItems($orderId);
        require_once __DIR__ . '/../view/app/manage_order_seller_detail.php';
    }

    // [API AJAX: XÁC NHẬN ĐƠN HÀNG]
    public function accept() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sellerId = $_SESSION['user_id'];
            $orderId = (int)$_POST['order_id'];

            $result = $this->orderModel->updateOrderStatus($orderId, $sellerId, 1, 3);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Đã xác nhận đơn hàng! Hãy chuẩn bị gói hàng.' : 'Xác nhận thất bại hoặc đơn đã được xử lý.'
            ]);
            exit;
        }
    }

    // [API AJAX: GIAO ĐƠN VỊ VẬN CHUYỂN]
    public function ship() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sellerId = $_SESSION['user_id'];
            $orderId = (int)$_POST['order_id'];

            $result = $this->orderModel->updateOrderStatus($orderId, $sellerId, 3, 4);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Cập nhật thành công: Đơn hàng đang được vận chuyển.' : 'Cập nhật thất bại.'
            ]);
            exit;
        }
    }

    // [API AJAX: HỦY ĐƠN HÀNG]
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sellerId = $_SESSION['user_id'];
            $orderId = (int)$_POST['order_id'];
            $reason = trim($_POST['cancel_reason'] ?? 'Hủy do sự cố hàng hóa');

            $result = $this->orderModel->cancelOrderBySeller($orderId, $sellerId, $reason);
            
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Đã hủy đơn hàng và hoàn lại số lượng kho.' : 'Không thể hủy đơn hàng này.'
            ]);
            exit;
        }
    }
}
?>