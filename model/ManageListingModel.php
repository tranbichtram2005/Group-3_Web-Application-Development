<?php
require_once 'model/Database.php';

class ManageListingModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách tin đăng theo User, Tab bộ lọc và Từ khóa tìm kiếm
    public function getSellerListings($userId, $statusTab = 'all', $searchKeyword = '') {
        $sql = "SELECT p.*, c.name AS category_name, cond.name AS condition_name, s.name AS status_name,
                       (SELECT image_url FROM listing_images WHERE listing_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
                FROM product_listings p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN conditions cond ON p.condition_id = cond.id
                LEFT JOIN listing_statuses s ON p.status_id = s.id
                WHERE p.user_id = :user_id";

        // Lọc theo Tab trạng thái
        if ($statusTab === 'active') {
            $sql .= " AND p.status_id = 2"; // 2 = approved / đang bán
        } elseif ($statusTab === 'pending') {
            $sql .= " AND p.status_id = 1"; // 1 = pending / chờ duyệt
        } elseif ($statusTab === 'hidden') {
            $sql .= " AND p.status_id IN (3, 4)"; // 4 = hidden/closed, 3 = rejected
        }

        // Lọc theo Từ khóa tìm kiếm
        if (!empty($searchKeyword)) {
            $sql .= " AND p.title LIKE :keyword";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if (!empty($searchKeyword)) {
            $keywordParam = '%' . $searchKeyword . '%';
            $stmt->bindParam(':keyword', $keywordParam, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái tin đăng (Ví dụ: Đổi sang Ẩn hoặc Đã bán)
    public function updateListingStatus($listingId, $userId, $statusId) {
        $sql = "UPDATE product_listings SET status_id = :status_id WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $listingId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Đếm tổng số lượng tin đăng của Seller theo từng nhóm trạng thái để hiển thị lên số lượng ở Tab
    public function getStatusCounts($userId) {
        $sql = "SELECT 
                    COUNT(*) as all_count,
                    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status_id IN (3, 4) THEN 1 ELSE 0 END) as hidden_count
                FROM product_listings 
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>