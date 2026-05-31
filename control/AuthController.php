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
            
            $mail->Username = 'vyle.31231022150@st.ueh.edu.vn'; 
            $mail->Password = 'slxuvfirffypbfcs'; 
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->SMTPOptions = array(
                'ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true)
            );
            
            $mail->setFrom($mail->Username, '2Life Marketplace');
            $mail->addAddress($email);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực (OTP) đăng ký tài khoản 2Life';
            
           // Lấy tên người dùng đang đăng ký để gửi mail cho thân thiện
            $user_name = isset($_SESSION['temp_user']['full_name']) ? $_SESSION['temp_user']['full_name'] : 'bạn';
            
            // ==========================================
            // TEMPLATE HTML ĐẸP LUNG LINH CỦA 2LIFE
            // ==========================================
            $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
            </head>
            <body style="margin: 0; padding: 0; background-color: #f4f8fb; font-family: Arial, Helvetica, sans-serif;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 30px 15px;">
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                                
                                <tr>
                                    <td align="center" style="background-color: #FF7A3D; padding: 25px;">
                                        <h1 style="color: #ffffff; margin: 0; font-size: 28px; letter-spacing: -1px;">2Life</h1>
                                        <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Marketplace</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 40px 30px; color: #333333; line-height: 1.6;">
                                        <h3 style="color: #1F3C5A; font-size: 20px; margin-top: 0;">Chào '.$user_name.',</h3>
                                        <p style="font-size: 15px;">Cảm ơn bạn đã tham gia cộng đồng trao đổi đồ cũ 2Life. Để hoàn tất việc đăng ký, vui lòng sử dụng mã xác thực (OTP) bên dưới:</p>
                                        
                                        <div style="text-align: center; margin: 35px 0;">
                                            <span style="display: inline-block; font-size: 32px; font-weight: bold; color: #FF7A3D; letter-spacing: 8px; background-color: #fff3ed; padding: 15px 35px; border-radius: 8px; border: 2px dashed #FF7A3D;">
                                                '.$otp.'
                                            </span>
                                        </div>

                                        <p style="font-size: 15px;">Mã này sẽ hết hạn trong vòng <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai để bảo mật tài khoản.</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" style="background-color: #f8f9fa; padding: 20px; border-top: 1px solid #eeeeee;">
                                        <p style="margin: 0; color: #888888; font-size: 13px;">© 2026 2Life Marketplace.</p>
                                        <p style="margin: 5px 0 0 0; color: #aaaaaa; font-size: 12px;">Email này được gửi tự động, vui lòng không trả lời.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            ';
            // ==========================================
            $mail->AltBody = "Chào bạn,\nMã xác thực (OTP) đăng ký tài khoản 2Life của bạn là: $otp\nMã này sẽ hết hạn trong 5 phút.\nVui lòng không chia sẻ mã này cho bất kỳ ai.";
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