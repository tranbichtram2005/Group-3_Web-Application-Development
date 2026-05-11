<?php
class Database {
    private $mayChu = "c2c-web-c2c-web.i.aivencloud.com";
    private $tenCSDL = "defaultdb";
    private $taiKhoan = "avnadmin";
    private $congPort = "19707";
    
    public $conn;

    public function getConnection() {
        $this->conn = null;

        // Tự động mò vào file .env để lấy mật khẩu
        $duongDanEnv = __DIR__ . '/.env';
        if (file_exists($duongDanEnv)) {
            $cauHinh = parse_ini_file($duongDanEnv);
            $matKhau = $cauHinh['DB_PASS'];
        } else {
            die("Cảnh báo: Không tìm thấy file .env để lấy mật khẩu!");
        }

        // Lấy chứng chỉ bảo mật lên mây
        $duongDanChungChi = __DIR__ . '/ca.pem';

        try {
            $chuoiKetNoi = "mysql:host=" . $this->mayChu . ";port=" . $this->congPort . ";dbname=" . $this->tenCSDL;
            
            $tuyChon = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::MYSQL_ATTR_SSL_CA => $duongDanChungChi,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            $this->conn = new PDO($chuoiKetNoi, $this->taiKhoan, $matKhau, $tuyChon);

        } catch(PDOException $ngoaiLe) {
            echo "Lỗi kết nối Database: " . $ngoaiLe->getMessage();
        }

        return $this->conn;
    }
}
?>