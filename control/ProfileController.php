<?php
// Nhớ check lại đường dẫn Database và user.php cho đúng nha cậu
require_once __DIR__ . '/../model/Database.php'; 
require_once __DIR__ . '/../model/User.php';     

class ProfileController {
    private $db;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Chưa đăng nhập thì đá về trang login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        // Nạp View
require_once __DIR__ . '/../view/partials/profile.php';    }

    public function updateAjax() {
        $userId = $_SESSION['user_id'];
        
        // Nhận dữ liệu text từ FormData (Dùng $_POST thay vì json_decode)
        $fullName = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        $avatarUrl = null;

        // Xử lý Upload File Ảnh (Nếu người dùng có chọn ảnh)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            // Tạo thư mục uploads/avatars nếu chưa có
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Đổi tên file để không bị trùng (Thêm timestamp)
            $fileName = time() . '_' . basename($_FILES['avatar']['name']);
            $targetFilePath = $uploadDir . $fileName;

            // Di chuyển file từ thư mục tạm vào thư mục thật
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                // Lưu đường dẫn tương đối vào Database
                $avatarUrl = 'uploads/avatars/' . $fileName;
            }
        }
        
        if ($this->userModel->updateProfile($userId, $fullName, $phone, $bio, $avatarUrl)) {
            echo json_encode(['status' => 'success', 'msg' => 'Cập nhật thông tin thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra, vui lòng thử lại!']);
        }
    }

    public function changePasswordAjax() {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        // Kiểm tra mật khẩu cũ
        if (!password_verify($data['old_password'], $user['password_hash'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Mật khẩu hiện tại không đúng!']);
            return;
        }
        
        // Cập nhật mật khẩu mới (Mã hóa BCRYPT)
        $newHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($userId, $newHash)) {
            echo json_encode(['status' => 'success', 'msg' => 'Đổi mật khẩu thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra!']);
        }
    }

public function registerSellerAjax() {
        $userId = $_SESSION['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);

        $shopName = $data['shop_name'];
        $taxCode = $data['tax_code']; 
        $description = $data['description'];

        // 1. Kiểm tra xem user đã có hồ sơ đăng ký chưa
        $profile = $this->userModel->getSellerProfile($userId);
        
        if ($profile) {
            // Nếu is_verified = 0 (Đang chờ duyệt)
            if ($profile['is_verified'] == 0) {
                echo json_encode([
                    'status' => 'info', 
                    'msg' => 'Hồ sơ của cậu đang được xét duyệt rồi. Vui lòng kiên nhẫn đợi Ban quản trị nhé!'
                ]);
                return;
            }
            // Nếu is_verified = 1 (Đã duyệt)
            if ($profile['is_verified'] == 1) {
                echo json_encode([
                    'status' => 'info', 
                    'msg' => 'Cậu đã là Người bán uy tín rồi, không cần gửi đăng ký thêm nữa đâu!'
                ]);
                return;
            }
        }

        // 2. Nếu chưa có hồ sơ thì mới gọi Model để lưu
        if ($this->userModel->registerSeller($userId, $shopName, $taxCode, $description)) {
            echo json_encode(['status' => 'success', 'msg' => 'Gửi yêu cầu thành công! Chúng tôi sẽ xét duyệt trong 24h.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Có lỗi kết nối cơ sở dữ liệu, vui lòng thử lại sau.']);
        }
    }
}
?>