<?php
require_once 'model/Database.php';

class ApproveSellerModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách yêu cầu dựa trên is_verified (0 = Chờ duyệt, 1 = Đã duyệt)
    public function getRequestsByStatus($isVerified = 0) {
        $sql = "SELECT s.*, u.full_name, u.email, u.phone 
                FROM seller_profiles s
                JOIN users u ON s.user_id = u.id
                WHERE s.is_verified = :is_verified
                ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':is_verified', $isVerified, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết một hồ sơ đăng ký
    public function getRequestById($id) {
        $sql = "SELECT s.*, u.full_name, u.email, u.phone 
                FROM seller_profiles s
                JOIN users u ON s.user_id = u.id
                WHERE s.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Phê duyệt người bán
    public function approveRequest($profileId, $userId) {
        try {
            $this->conn->beginTransaction();

            // Cập nhật bảng seller_profiles
            $sql1 = "UPDATE seller_profiles SET is_verified = 1, verified_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->bindParam(':id', $profileId, PDO::PARAM_INT);
            $stmt1->execute();

            // Cập nhật bảng users
            $sql2 = "UPDATE users SET is_verified_seller = 1 WHERE id = :user_id";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Từ chối hồ sơ (Xóa bản ghi)
    public function rejectRequest($profileId) {
        $sql = "DELETE FROM seller_profiles WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $profileId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>