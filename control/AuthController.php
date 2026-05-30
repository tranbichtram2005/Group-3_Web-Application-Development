<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../model/LocationModel.php'; 
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $userModel;
    private $locationModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
        $this->locationModel = new LocationModel($this->db);
    }

    // 1. Hàm Đăng nhập (Đã tích hợp phân luồng Admin/User)
    public function login() {
        $error = null;
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
            $identifier = $_POST['identifier']; 
            $password = $_POST['password'];

            $user = $this->userModel->login($identifier, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id']; // Quan trọng: Phải lưu Role
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];

                // PHÂN LUỒNG: Role 2 = Admin, Role 1 = User thường
                if ($user['role_id'] == 2) {
                    header("Location: index.php?controller=admin-home");
                } else {
                    header("Location: index.php?controller=home");
                }
                exit;
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác!";
            }
        }
        require_once __DIR__ . '/../view/Auth/login.php';
    }

    // 2. Hàm Đăng ký (Khớp với action=register)
    public function register() {
        $error = null;
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
            $fullName = $_POST['full_name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];
            $province_id = $_POST['province_id'];
            $district_id = $_POST['district_id'];
            $ward_id = $_POST['ward_id'];
            $street = $_POST['street'];

            if ($this->userModel->checkExists($username, $email)) {
                $error = "Username hoặc Email đã tồn tại!";
            } else {
                $otp = rand(100000, 999999);
                $_SESSION['temp_user'] = [
                    'full_name' => $fullName,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    'province_id' => $province_id,
                    'district_id' => $district_id,
                    'ward_id' => $ward_id,
                    'street' => $street,
                    'otp' => $otp,
                    'otp_expiry' => time() + 300
                ];

                if ($this->sendOTPEmail($email, $otp)) {
                    // TỰ ĐỘNG CHUYỂN HƯỚNG QUA TRANG OTP THEO CHUẨN ĐƯỜNG DẪN ROUTER
                    header("Location: index.php?controller=auth&action=verify_otp");
                    exit;
                } else {
                    $error = "Không thể gửi email OTP. Vui lòng thử lại!";
                }
            }
        }
        $provinces = $this->locationModel->getAllProvinces();
        require_once __DIR__ . '/../view/Auth/register.php';
    }

    // 3. Hàm Xác thực OTP (Khớp với action=verify_otp)
    public function verify_otp() {
        $result = null;
        if (!isset($_SESSION['temp_user'])) {
            header("Location: index.php?controller=auth&action=register");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
            $user_otp = $_POST['otp'];
            $temp = $_SESSION['temp_user'];

            if (time() > $temp['otp_expiry']) {
                $result = ['status' => 'error', 'msg' => 'Mã OTP đã hết hạn! Vui lòng đăng ký lại.'];
            } elseif ($user_otp == $temp['otp']) {
                $success = $this->userModel->register(
                    $temp['full_name'], $temp['username'], $temp['email'], $temp['phone'],
                    $temp['password_hash'], $temp['province_id'], $temp['district_id'],
                    $temp['ward_id'], $temp['street']
                );

                if ($success) {
                    unset($_SESSION['temp_user']);
                    $result = ['status' => 'success', 'msg' => 'Đăng ký tài khoản thành công!'];
                } else {
                    $result = ['status' => 'error', 'msg' => 'Lỗi hệ thống khi khởi tạo tài khoản!'];
                }
            } else {
                $result = ['status' => 'error', 'msg' => 'Mã OTP không chính xác!'];
            }
        }
        require_once __DIR__ . '/../view/Auth/verify_otp.php';
    }

    // 4. Hàm Đăng xuất (Khớp với action=logout)
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['full_name']);
        session_destroy();
        header("Location: index.php?controller=home");
        exit;
    }

    private function sendOTPEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // ĐIỀN THẲNG THÔNG TIN Ở ĐÂY ĐỂ CẢ TEAM DÙNG CHUNG KHÔNG CẦN .ENV
            $mail->Username = 'vyle.31231022150@st.ueh.edu.vn'; 
            $mail->Password = 'slxuvfirffypbfcs'; // 16 ký tự mật khẩu ứng dụng Google
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Thêm đúng 3 dòng này để máy bạn nào trong nhóm chạy XAMPP cũng không bị chặn SSL
            $mail->SMTPOptions = array(
                'ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true)
            );
            
            $mail->setFrom($mail->Username, '2Life Marketplace');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = '=[2Life] Ma xac thuc OTP dang ky tai khoan=';
            
            // Giữ nguyên mẫu hiển thị ngắn gọn, dễ nhìn của cậu
            $mail->Body    = "<h3>Mã xác thực đăng ký tài khoản của bạn là: <b style='color:#FF7A3D; font-size:24px;'>$otp</b></h3><p>Mã có hiệu lực trong vòng 5 phút.</p>";
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // API Lấy danh sách Quận/Huyện
    public function getDistricts() {
        header('Content-Type: application/json');
        if (isset($_GET['province_id'])) {
            $districts = $this->locationModel->getDistrictsByProvince($_GET['province_id']);
            echo json_encode($districts);
        } else {
            echo json_encode([]);
        }
        exit;
    }

    // API Lấy danh sách Phường/Xã
    public function getWards() {
        header('Content-Type: application/json');
        if (isset($_GET['district_id'])) {
            $wards = $this->locationModel->getWardsByDistrict($_GET['district_id']);
            echo json_encode($wards);
        } else {
            echo json_encode([]);
        }
        exit;
    }
}
?>