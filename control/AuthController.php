<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng Model
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/user.php';

// Nhúng thư viện PHPMailer theo đúng cấu trúc hình cậu chụp
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function handleLogin() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
            $identifier = $_POST['identifier']; 
            $password = $_POST['password'];

            $user = $this->userModel->login($identifier, $password);
            
            if ($user) {
                // Đăng nhập thành công, lưu session và đá về trang chủ
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header("Location: ../../index.php"); 
                exit;
            } else {
                return "Tài khoản hoặc mật khẩu không chính xác!";
            }
        }
        return null;
    }

    public function handleRegister() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
            $fullName = trim($_POST['full_name']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']); // Bổ sung
            $password = $_POST['password'];
            
            // Dữ liệu địa chỉ
            $province_id = $_POST['province_id'];
            $district_id = $_POST['district_id'];
            $ward_id = $_POST['ward_id'];
            $street = trim($_POST['street']);

            //Validation cơ bản
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Định dạng email không hợp lệ (Ví dụ đúng: tenban@gmail.com)!";
            }

            // 2. Rào Số điện thoại: Chuẩn SĐT Việt Nam (10 số, bắt đầu bằng 03, 05, 07, 08, 09)
            if (!preg_match("/^(0[3|5|7|8|9])+([0-9]{8})$/", $phone)) {
                return "Số điện thoại không hợp lệ! Vui lòng nhập đúng 10 số của nhà mạng Việt Nam.";
            }

            if ($this->userModel->checkExists($username, $email)) {
                return "Username hoặc Email đã được sử dụng!";
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $otp = rand(100000, 999999);

            // Lưu toàn bộ vào session tạm
            $_SESSION['temp_user'] = [
                'full_name' => $fullName,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => $passwordHash,
                'province_id' => $province_id,
                'district_id' => $district_id,
                'ward_id' => $ward_id,
                'street' => $street
            ];
            $_SESSION['otp'] = $otp;

            if ($this->sendOTPEmail($email, $otp, $fullName)) {
                header("Location: verify_otp.php"); 
                exit;
            } else {
                return "Lỗi gửi mail!";
            }
        }
        return null;
    }

    public function handleVerifyOTP() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
            $userOtp = trim($_POST['otp']);
            
            if ($userOtp == $_SESSION['otp']) { 
                $u = $_SESSION['temp_user'];
                
                // Truyền đủ 9 tham số vào hàm register
                if ($this->userModel->register($u['full_name'], $u['username'], $u['email'], $u['phone'], $u['password_hash'], $u['province_id'], $u['district_id'], $u['ward_id'], $u['street'])) {
                    unset($_SESSION['temp_user']);
                    unset($_SESSION['otp']);
                    header("Location: login.php?registered=1");
                    exit;
                } else {
                    return ["status" => "error", "msg" => "Lỗi hệ thống khi tạo tài khoản."];
                }
            } else {
                return ["status" => "error", "msg" => "Mã OTP không chính xác!"];
            }
        }
        return null;
    }

    private function sendOTPEmail($toEmail, $otp, $fullName = 'bạn') {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 0; // Đổi thành 0 để tắt log rác trên màn hình khi gửi thành công
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // ================== THAY ĐỔI Ở ĐÂY ==================
            $mail->Username   = 'vyle.31231022150@st.ueh.edu.vn'; 
            $mail->Password   = 'slxuvfirffypbfcs'; 
            // ====================================================

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($mail->Username, '2Life Marketplace');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Mã xác nhận đăng ký tài khoản 2Life';
            
            // EMAIL TEMPLATE MỚI - CHUYÊN NGHIỆP HƠN
            $mail->Body    = "
            <div style=\"font-family: 'Segoe UI', Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e1e8ed;\">
                
                <div style=\"background-color: #FF7A3D; padding: 25px 20px; text-align: center;\">
                    <h1 style=\"color: #ffffff; margin: 0; font-size: 32px; letter-spacing: 2px;\">2Life</h1>
                    <p style=\"color: #ffe6dd; margin: 5px 0 0 0; font-size: 15px;\">Nền tảng giao thương C2C lớn nhất UEH</p>
                </div>
                
                <div style=\"padding: 30px 40px; color: #333333; line-height: 1.6;\">
                    <h2 style=\"color: #1F3C5A; margin-top: 0; font-size: 22px;\">Xin chào {$fullName},</h2>
                    <p style=\"font-size: 15px;\">Cảm ơn bạn đã lựa chọn tham gia <strong>2Life</strong>! Để hoàn tất quá trình đăng ký tài khoản và bắt đầu hành trình mua bán, vui lòng sử dụng mã xác nhận (OTP) dưới đây:</p>
                    
                    <div style=\"text-align: center; margin: 35px 0;\">
                        <span style=\"display: inline-block; font-size: 38px; font-weight: bold; color: #1F3C5A; background-color: #f4f8fb; padding: 15px 35px; border-radius: 12px; letter-spacing: 10px; border: 2px dashed #FF7A3D;\">{$otp}</span>
                    </div>
                    
                    <div style=\"background-color: #fff4f4; padding: 15px 20px; border-left: 4px solid #d9534f; border-radius: 4px; margin-bottom: 25px;\">
                        <p style=\"margin: 0; font-size: 14px; color: #c9302c;\">
                            <strong>Lưu ý quan trọng:</strong> Mã này chỉ có hiệu lực trong vòng <strong>15 phút</strong>. Tuyệt đối không chia sẻ mã này với bất kỳ ai (kể cả nhân viên 2Life) để bảo vệ tài khoản của bạn.
                        </p>
                    </div>
                    
                    <p style=\"font-size: 15px;\">Nếu bạn không thực hiện yêu cầu đăng ký này, xin vui lòng bỏ qua email này.</p>
                    
                    <p style=\"font-size: 15px; margin-top: 30px;\">Chúc bạn có những trải nghiệm tuyệt vời cùng 2Life!<br>
                    <strong style=\"color: #1F3C5A; font-size: 16px;\">Đội ngũ 2Life</strong></p>
                </div>
                
                <div style=\"background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #888888; border-top: 1px solid #e1e8ed;\">
                    <p style=\"margin: 0;\">© 2026 2Life Marketplace. Tất cả các quyền được bảo lưu.</p>
                    <p style=\"margin: 5px 0 0 0;\">Email này được gửi tự động từ hệ thống, vui lòng không trả lời.</p>
                </div>
                
            </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Lỗi chi tiết từ Google: " . $mail->ErrorInfo; 
            exit; 
        }
    }
    public function handleLogout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Xóa toàn bộ session
        session_unset();
        session_destroy();
        // Đá về trang đăng nhập
        header("Location: ../../index.php");
        exit;
    }
}