<?php
require_once 'model/Database.php';

class ListingModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // =======================================================
    // NHÓM HÀM LẤY DỮ LIỆU ĐỔ RA FORM (GET)
    // =======================================================

    // Lấy danh sách danh mục (chỉ lấy các danh mục đang active)
    public function getAllCategories()
    {
        $sql = "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BỔ SUNG THÊM: Hàm lấy danh sách khu vực Phường/Xã từ Database có sẵn
    public function getWards()
    {
        $sql = "SELECT id, name FROM wards ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách tình trạng sản phẩm
    public function getAllConditions()
    {
        $sql = "SELECT id, name FROM conditions ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =======================================================
    // NHÓM HÀM THÊM DỮ LIỆU VÀO DATABASE (POST)
    // =======================================================

    // Thêm tin đăng mới vào bảng product_listings
    public function createListing($userId, $categoryId, $conditionId, $statusId, $wardId, $title, $description, $price, $isNegotiable, $stockQuantity)
    {
        $sql = "INSERT INTO product_listings 
                (user_id, category_id, condition_id, status_id, ward_id, title, description, price, is_negotiable, stock_quantity) 
                VALUES 
                (:user_id, :category_id, :condition_id, :status_id, :ward_id, :title, :description, :price, :is_negotiable, :stock_quantity)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':condition_id', $conditionId, PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        $stmt->bindParam(':ward_id', $wardId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR); // Decimal/Float
        $stmt->bindParam(':is_negotiable', $isNegotiable, PDO::PARAM_INT);
        $stmt->bindParam(':stock_quantity', $stockQuantity, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Trả về ID của tin vừa tạo để liên kết với bảng ảnh
        }
        return false;
    }

    // Thêm ảnh vào bảng listing_images
    public function addListingImage($listingId, $imageUrl, $sortOrder, $isPrimary)
    {
        $sql = "INSERT INTO listing_images (listing_id, image_url, sort_order, is_primary) 
                VALUES (:listing_id, :image_url, :sort_order, :is_primary)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $imageUrl, PDO::PARAM_STR);
        $stmt->bindParam(':sort_order', $sortOrder, PDO::PARAM_INT);
        $stmt->bindParam(':is_primary', $isPrimary, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
