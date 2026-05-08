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

            // 1. Thêm vào bảng users
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

            // Lấy ID của user vừa được tạo
            $user_id = $this->conn->lastInsertId();

            // 2. Thêm vào bảng user_addresses (Đặt làm mặc định)
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

            // Hoàn tất giao dịch
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Nếu có bất kỳ lỗi gì, quay xe (Rollback) không lưu gì cả
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
}
?>