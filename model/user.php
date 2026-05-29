<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkExists($username, $email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

   public function register($fullName, $username, $email, $phone, $passwordHash, $province_id, $district_id, $ward_id, $street) {
        try {
            // Bắt đầu giao dịch an toàn (Transaction)
            $this->conn->beginTransaction();

            // 1. Thêm thành viên vào bảng users
            $queryUser = "INSERT INTO " . $this->table_name . " 
                      (full_name, username, email, phone, password_hash, role_id, status_id) 
                      VALUES (:full_name, :username, :email, :phone, :password_hash, 1, 1)";
            $stmtUser = $this->conn->prepare($queryUser);
            $stmtUser->bindParam(':full_name', $fullName);
            $stmtUser->bindParam(':username', $username);
            $stmtUser->bindParam(':email', $email);
            $stmtUser->bindParam(':phone', $phone);
            $stmtUser->bindParam(':password_hash', $passwordHash);
            $stmtUser->execute();

            // Lấy ID của user vừa mới tạo thành công
            $user_id = $this->conn->lastInsertId();

            // 2. Thêm địa chỉ vào bảng user_addresses (Đặt làm mặc định)
            $queryAddr = "INSERT INTO user_addresses 
                      (user_id, province_id, district_id, ward_id, street, is_default) 
                      VALUES (:user_id, :province_id, :district_id, :ward_id, :street, 1)";
            $stmtAddr = $this->conn->prepare($queryAddr);
            $stmtAddr->bindParam(':user_id', $user_id);
            $stmtAddr->bindParam(':province_id', $province_id);
            $stmtAddr->bindParam(':district_id', $district_id);
            $stmtAddr->bindParam(':ward_id', $ward_id);
            $stmtAddr->bindParam(':street', $street);
            $stmtAddr->execute();

            // 3. TỰ ĐỘNG KHỞI TẠO GIỎ HÀNG TRỐNG CHO THÀNH VIÊN MỚI
            $queryCart = "INSERT INTO carts (user_id) VALUES (:user_id)";
            $stmtCart = $this->conn->prepare($queryCart);
            $stmtCart->bindParam(':user_id', $user_id);
            $stmtCart->execute();

            // Hoàn tất giao dịch khi cả 3 bước trên đều thành công tốt đẹp
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Nếu có bất kỳ lỗi gì xảy ra ở 1 trong 3 bước, hủy bỏ toàn bộ thao tác để bảo vệ dữ liệu
            $this->conn->rollBack();
            return false;
        }
    }

    public function login($identifier, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :identifier OR email = :identifier LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }
    // --- GIỮ NGUYÊN CODE CŨ CỦA FILE USER.PHP Ở TRÊN, CHỈ DÁN THÊM HÀM NÀY VÀO TRƯỚC DẤU } CUỐI CÙNG ---

    public function getHeaderStats($user_id) {
        $stats = ['cartCount' => 0, 'notiCount' => 0, 'msgCount' => 0];
        try {
            // 1. Đếm số món trong giỏ hàng
            $stmtCart = $this->conn->prepare("SELECT COUNT(*) as count FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE c.user_id = :user_id");
            $stmtCart->execute([':user_id' => $user_id]);
            $stats['cartCount'] = $stmtCart->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // 2. Đếm thông báo chưa đọc
            $stmtNoti = $this->conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0");
            $stmtNoti->execute([':user_id' => $user_id]);
            $stats['notiCount'] = $stmtNoti->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // 3. Đếm tin nhắn chưa đọc
            $stmtMsg = $this->conn->prepare("SELECT COUNT(tm.id) as count FROM trade_messages tm JOIN trade_conversations tc ON tm.trade_conversation_id = tc.id WHERE tm.is_read = 0 AND tm.sender_id != :user_id AND (tc.buyer_id = :user_id OR tc.seller_id = :user_id)");
            $stmtMsg->execute([':user_id' => $user_id]);
            $stats['msgCount'] = $stmtMsg->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            // Lỗi thì giữ nguyên bằng 0
        }
        return $stats;
    }
    // ==========================================
    // CÁC HÀM QUẢN LÝ TÀI KHOẢN (PROFILE)
    // ==========================================
    
    // 1. Lấy thông tin user hiện tại
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Cập nhật thông tin cá nhân
    public function updateProfile($id, $fullName, $phone, $bio, $avatarUrl = null) {
        $query = "UPDATE " . $this->table_name . " SET full_name = :full_name, phone = :phone, bio = :bio";
        
        // Nếu có ảnh mới thì thêm vào câu lệnh SQL
        if ($avatarUrl != null) {
            $query .= ", avatar_url = :avatar_url";
        }
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $params = [
            ':full_name' => $fullName,
            ':phone' => $phone,
            ':bio' => $bio,
            ':id' => $id
        ];
        if ($avatarUrl != null) {
            $params[':avatar_url'] = $avatarUrl;
        }
        
        return $stmt->execute($params);
    }

    // 3. Cập nhật mật khẩu
    public function updatePassword($id, $newPasswordHash) {
        $query = "UPDATE " . $this->table_name . " SET password_hash = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':password' => $newPasswordHash, ':id' => $id]);
    }

    // 4. Gửi yêu cầu đăng ký bán hàng
// 4. Gửi yêu cầu đăng ký bán hàng
    public function registerSeller($userId, $shopName, $taxCode, $description) {
        // FIX MÂU THUẪN: Dùng thuật toán tự động cập nhật nếu đã từng bị từ chối
        $query = "INSERT INTO seller_profiles (user_id, shop_name, tax_code, description, is_verified) 
                  VALUES (:user_id, :shop_name, :tax_code, :description, 0)
                  ON DUPLICATE KEY UPDATE 
                  shop_name = VALUES(shop_name), 
                  tax_code = VALUES(tax_code), 
                  description = VALUES(description), 
                  is_verified = 0,
                  verified_at = NULL";
                  
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':shop_name' => $shopName,
            ':tax_code' => $taxCode,
            ':description' => $description
        ]);
    }

// Hàm kiểm tra xem user đã gửi form đăng ký bán hàng chưa
    public function getSellerProfile($userId) {
        $query = "SELECT * FROM seller_profiles WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>