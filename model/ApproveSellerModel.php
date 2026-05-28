<?php
class ApproveSellerModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách hồ sơ đăng ký theo trạng thái phê duyệt (is_verified = 0 hoặc 1)
    public function getSellersByStatus($isVerified = 0)
    {
        $sql = "SELECT sp.*, u.full_name, u.email, u.phone 
                FROM seller_profiles sp
                JOIN users u ON sp.user_id = u.id
                WHERE sp.is_verified = :is_verified
                ORDER BY sp.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':is_verified' => $isVerified]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm số lượng hồ sơ để hiển thị số badge trên các Tab điều hướng
    public function getCountStats()
    {
        $counts = [0 => 0, 1 => 0, 2 => 0]; // 0: Chờ duyệt, 1: Đã duyệt, 2: Từ chối

        $sql = "SELECT is_verified, COUNT(id) as total FROM seller_profiles GROUP BY is_verified";
        $stmt = $this->conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = (int)$row['is_verified'];
            if (isset($counts[$key])) {
                $counts[$key] = (int)$row['total'];
            }
        }
        return $counts;
    }
    // Xem chi tiết hồ sơ đăng ký đăng ký gian hàng
    public function getSellerRequestDetail($profileId)
    {
        $sql = "SELECT sp.*, u.full_name, u.username, u.email, u.phone, u.avatar_url
                FROM seller_profiles sp
                JOIN users u ON sp.user_id = u.id
                WHERE sp.id = :profile_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':profile_id' => $profileId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NGHIỆP VỤ: Phê duyệt hồ sơ mở gian hàng (Sử dụng Transaction)
    public function approveSellerProfile($profileId, $userId)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Cập nhật hồ sơ đăng ký gian hàng thành đã xác thực
            $sqlProfile = "UPDATE seller_profiles SET is_verified = 1, verified_at = CURRENT_TIMESTAMP WHERE id = :profile_id";
            $stmtProfile = $this->conn->prepare($sqlProfile);
            $stmtProfile->execute([':profile_id' => $profileId]);

            // 2. Nâng cấp tài khoản User thành Người bán chính thức
            $sqlUser = "UPDATE users SET is_verified_seller = 1 WHERE id = :user_id";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([':user_id' => $userId]);

            // 3. Tạo thông báo chúc mừng gửi tới người dùng (Notification Type 4: account_action)
            $sqlNoti = "INSERT INTO notifications (user_id, type_id, title, body, is_read) 
                        VALUES (:user_id, 4, 'Gian hàng đã được phê duyệt', 'Chúc mừng! Hồ sơ đăng ký mở gian hàng của bạn đã được Admin phê duyệt thành công.', 0)";
            $stmtNoti = $this->conn->prepare($sqlNoti);
            $stmtNoti->execute([':user_id' => $userId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("approveSellerProfile Error: " . $e->getMessage());
            return false;
        }
    }

    // NGHIỆP VỤ: Từ chối hồ sơ đăng ký
    public function rejectSellerProfile($profileId, $userId, $reason)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo thông báo gửi cho User
            $bodyText = "Yêu cầu mở gian hàng của bạn đã bị từ chối. Lý do: " . $reason . ". Vui lòng kiểm tra lại thông tin và nộp lại hồ sơ.";
            $sqlNoti = "INSERT INTO notifications (user_id, type_id, title, body, is_read) 
                        VALUES (:user_id, 4, 'Yêu cầu mở gian hàng bị từ chối', :body, 0)";
            $stmtNoti = $this->conn->prepare($sqlNoti);
            $stmtNoti->execute([
                ':user_id' => $userId,
                ':body' => $bodyText
            ]);

            // 2. GIỮ LẠI BẢN GHI, đổi is_verified = 0 để hiển thị ở tab "Từ chối"
            $sqlUpdate = "UPDATE seller_profiles SET is_verified = 0 WHERE id = :profile_id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute([':profile_id' => $profileId]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("rejectSellerProfile Error: " . $e->getMessage());
            return false;
        }
    }
}
