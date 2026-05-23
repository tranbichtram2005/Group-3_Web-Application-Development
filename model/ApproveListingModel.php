<?php
require_once 'model/Database.php';

class ApproveListingModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách tin đăng theo trạng thái (có kèm tên người bán và danh mục)
    public function getListingsByStatus($status_id) {
        $sql = "SELECT p.*, u.full_name as seller_name, c.name as category_name
                FROM product_listings p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.status_id = :status_id
                ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 tin đăng cụ thể (để Admin xem xét kỹ trước khi duyệt)
    public function getListingDetail($id) {
        $sql = "SELECT p.*, u.full_name as seller_name, u.phone, u.email, 
                       c.name as category_name, w.name as ward_name, cond.name as condition_name
                FROM product_listings p
                JOIN users u ON p.user_id = u.id
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN wards w ON p.ward_id = w.id
                LEFT JOIN conditions cond ON p.condition_id = cond.id
                WHERE p.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Bổ sung thêm hàm lấy danh sách tất cả hình ảnh của 1 tin đăng
    public function getListingImages($listing_id) {
        $sql = "SELECT image_url, is_primary FROM listing_images WHERE listing_id = :listing_id ORDER BY is_primary DESC, sort_order ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':listing_id', $listing_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái duyệt tin
    public function updateListingStatus($id, $status_id) {
        $sql = "UPDATE product_listings SET status_id = :status_id WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>