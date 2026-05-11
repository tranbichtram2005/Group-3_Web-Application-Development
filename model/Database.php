<?php
class Database {
    // 1. Khai báo các thông số kết nối lên mây (Aiven)
    private $host = "c2c-web-c2c-web.i.aivencloud.com";
    private $db_name = "defaultdb"; // Tên database mặc định trên Aiven
    private $username = "avnadmin";
    private $password = "matkhau"; 
    private $port = "19707";
    
    public $conn;

    // Hàm lấy kết nối
    public function getConnection() {
        $this->conn = null;

        // 2. Trỏ đường dẫn tới vé thông hành (file ca.pem)
        // Lệnh __DIR__ tự động hiểu là thư mục hiện tại đang chứa file code này
        $certPath = __DIR__ . '/ca.pem';

        try {
            // Chuỗi cấu hình DSN (Data Source Name)
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;

            // 3. Đưa cái vé thông hành SSL vào cấu hình của PDO
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::MYSQL_ATTR_SSL_CA => $certPath,
                // Bật chế độ báo lỗi chi tiết để lỡ có lỗi thì dễ biết đường mà sửa
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // 4. Khởi tạo kết nối vút lên mây
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch(PDOException $exception) {
            // Bắt lỗi nếu rớt mạng hoặc sai pass
            echo "Toang rồi Máy chủ ơi, lỗi kết nối Database: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>