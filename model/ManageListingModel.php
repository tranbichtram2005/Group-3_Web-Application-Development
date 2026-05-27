<?php
require_once 'model/Database.php';

class ManageListingModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách tin đăng có Phân trang (Thêm $limit và $offset)
    public function getSellerListings($userId, $statusTab = 'all', $searchKeyword = '', $limit = 5, $offset = 0)
    {
        $sql = "SELECT p.*, c.name AS category_name, cond.name AS condition_name, s.name AS status_name,
                       (SELECT image_url FROM listing_images WHERE listing_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
                FROM product_listings p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN conditions cond ON p.condition_id = cond.id
                LEFT JOIN listing_statuses s ON p.status_id = s.id
                WHERE p.user_id = :user_id";

        // Logic Filter Tab
        if ($statusTab === 'active') {
            $sql .= " AND p.status_id = 2";
        } elseif ($statusTab === 'pending') {
            $sql .= " AND p.status_id = 1";
        } elseif ($statusTab === 'hidden') {
            $sql .= " AND p.status_id = 4";
        } else {
            // Nếu là Tab "Tất cả", ta BỎ QUA những tin đã xóa/ẩn (status_id = 4)
            $sql .= " AND p.status_id != 4";
        }

        if (!empty($searchKeyword)) {
            $sql .= " AND p.title LIKE :keyword";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if (!empty($searchKeyword)) {
            $keywordParam = '%' . $searchKeyword . '%';
            $stmt->bindParam(':keyword', $keywordParam, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Đếm tổng số tin (phục vụ tính số trang)
    public function getTotalSellerListings($userId, $statusTab = 'all', $searchKeyword = '')
    {
        $sql = "SELECT COUNT(*) as total FROM product_listings WHERE user_id = :user_id";

        if ($statusTab === 'active') {
            $sql .= " AND status_id = 2";
        } elseif ($statusTab === 'pending') {
            $sql .= " AND status_id = 1";
        } elseif ($statusTab === 'hidden') {
            $sql .= " AND status_id = 4";
        } else {
            // Nếu là Tab "Tất cả", ta BỎ QUA những tin đã xóa/ẩn
            $sql .= " AND status_id != 4";
        }

        if (!empty($searchKeyword)) $sql .= " AND title LIKE :keyword";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        if (!empty($searchKeyword)) {
            $keywordParam = '%' . $searchKeyword . '%';
            $stmt->bindParam(':keyword', $keywordParam, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    // Lấy 1 sản phẩm cụ thể cho Modal
    public function getListingById($listingId, $userId)
    {
        $sql = "SELECT p.*, c.name AS category_name, cond.name AS condition_name
                FROM product_listings p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN conditions cond ON p.condition_id = cond.id
                WHERE p.id = :id AND p.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $listingId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa tin đăng (Soft Delete)
    public function deleteListing($listingId, $userId)
    {
        $sql = "UPDATE product_listings 
                SET status_id = 4, deleted_at = NOW() 
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $listingId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateListingStatus($listingId, $userId, $statusId)
    {
        $sql = "UPDATE product_listings SET status_id = :status_id WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $listingId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getStatusCounts($userId)
    {
        $sql = "SELECT 
                    SUM(CASE WHEN status_id != 4 THEN 1 ELSE 0 END) as all_count,
                    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) as hidden_count
                FROM product_listings 
                WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
