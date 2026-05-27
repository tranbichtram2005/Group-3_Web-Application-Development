<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/ApproveSellerModel.php';

class ApproveSellerController {
    private $db;
    private $approveModel;

    public function __construct() {
        // Kiểm tra đăng nhập chung
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $database = new Database();
        $dbConn = $database->getConnection();

        // KIỂM TRA BẢO MẬT: Xác thực phân quyền tài khoản Admin (role_id = 2)
        $userId = $_SESSION['user_id'];
        $checkAdmin = $dbConn->prepare("SELECT role_id FROM users WHERE id = :user_id");
        $checkAdmin->execute([':user_id' => $userId]);
        $user = $checkAdmin->fetch(PDO::FETCH_ASSOC);

        if (!$user || (int)$user['role_id'] !== 2) {
            $_SESSION['toast_msg'] = 'Cảnh báo bảo mật: Bạn không có quyền truy cập vào khu vực Admin!';
            header("Location: index.php?controller=home");
            exit;
        }

        $this->approveModel = new ApproveSellerModel($dbConn);
    }

    // Hiển thị giao diện chính danh sách duyệt
    public function index() {
        $sellers = $this->approveModel->getSellersByStatus(0); // Mặc định tải danh sách chờ duyệt (0)
        $stats = $this->approveModel->getCountStats();
        
        require_once __DIR__ . '/../view/admin/approve_seller.php';
    }

    // API AJAX: Trả về khối danh sách HTML khi chuyển đổi qua lại giữa các Tab
    public function fetchList() {
        $isVerified = isset($_GET['status']) ? (int)$_GET['status'] : 0;
        $sellers = $this->approveModel->getSellersByStatus($isVerified);
        $stats = $this->approveModel->getCountStats();

        ob_start();
        require __DIR__ . '/../view/admin/approve_seller_list.php';
        $html = ob_get_clean();

        echo json_encode([
            'success' => true,
            'html' => $html,
            'stats' => $stats
        ]);
        exit;
    }

    // Xem chi tiết hồ sơ yêu cầu cụ thể
    public function detail() {
        $profileId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $seller = $this->approveModel->getSellerRequestDetail($profileId);

        if (!$seller) {
            $_SESSION['toast_msg'] = 'Hồ sơ đăng ký không tồn tại hoặc đã được xử lý xong trước đó.';
            header("Location: index.php?controller=approveseller");
            exit;
        }

        require_once __DIR__ . '/../view/admin/approve_seller_detail.php';
    }

    // API AJAX: Tiếp nhận lệnh phê duyệt
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profileId = (int)$_POST['profile_id'];
            $userId = (int)$_POST['user_id'];

            $result = $this->approveModel->approveSellerProfile($profileId, $userId);
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Đã phê duyệt tài khoản thành Người bán chính thức.' : 'Phê duyệt thất bại.'
            ]);
            exit;
        }
    }

    // API AJAX: Tiếp nhận lệnh từ chối
    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profileId = (int)$_POST['profile_id'];
            $userId = (int)$_POST['user_id'];
            $reason = trim($_POST['reject_reason'] ?? 'Thông tin cung cấp hoặc mã số thuế không chính xác');

            $result = $this->approveModel->rejectSellerProfile($profileId, $userId, $reason);
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Đã từ chối hồ sơ đăng ký và gửi thông báo tới người dùng.' : 'Xử lý lệnh từ chối thất bại.'
            ]);
            exit;
        }
    }
}
?>