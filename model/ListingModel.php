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
    // LẤY THỐNG KÊ ĐÁNH GIÁ (SỐ SAO, TỔNG SỐ ĐÁNH GIÁ)
    // =======================================================
    public function getProductReviewStats($listingId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE listing_id = :listing_id";
        // Đã sửa $this->db thành $this->conn để khớp với Model của cậu
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':listing_id' => $listingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =======================================================
    // LẤY DANH SÁCH BÌNH LUẬN CHI TIẾT
    // =======================================================
    public function getProductReviews($listingId, $star = 0) {
        $sql = "SELECT r.*, u.full_name as reviewer_name 
                FROM reviews r 
                JOIN users u ON r.reviewer_id = u.id 
                WHERE r.listing_id = :listing_id";
        
        $params = [':listing_id' => $listingId];

        // Nếu có chọn lọc sao (từ 1 đến 5) thì thêm điều kiện vào SQL
        if ($star > 0 && $star <= 5) {
            $sql .= " AND r.rating = :star";
            $params[':star'] = $star;
        }

        $sql .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
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

  // 1. Đếm tổng số sản phẩm (Đang bán & Đã bán)
    public function getTotalActiveListings($keyword = '') {
        $sql = "SELECT COUNT(*) FROM product_listings WHERE status_id IN (2, 3)";
        if (!empty($keyword)) {
            $sql .= " AND title LIKE :keyword";
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($keyword)) {
            $kw = "%" . $keyword . "%";
            $stmt->bindParam(':keyword', $kw, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 2. Lấy danh sách sản phẩm phân trang (Đang bán & Đã bán)
    public function getPaginatedListings($limit, $offset, $keyword = '') {
        $sql = "SELECT pl.*, w.name AS ward_name, 
                       (SELECT image_url FROM listing_images WHERE listing_id = pl.id AND is_primary = 1 LIMIT 1) as image_url
                FROM product_listings pl
                LEFT JOIN wards w ON pl.ward_id = w.id
                WHERE pl.status_id IN (2, 3)";
        
        if (!empty($keyword)) {
            $sql .= " AND pl.title LIKE :keyword";
        }
        $sql .= " ORDER BY pl.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        if (!empty($keyword)) {
            $kw = "%" . $keyword . "%";
            $stmt->bindParam(':keyword', $kw, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Lấy chi tiết một sản phẩm
    public function getListingDetail($id) {
        $sql = "SELECT pl.*, u.full_name, u.username, u.avatar_url, u.created_at as user_created_at,
                       c.name as category_name, cond.name as condition_name, w.name as ward_name
                FROM product_listings pl
                JOIN users u ON pl.user_id = u.id
                LEFT JOIN categories c ON pl.category_id = c.id
                LEFT JOIN conditions cond ON pl.condition_id = cond.id
                LEFT JOIN wards w ON pl.ward_id = w.id
                WHERE pl.id = :id AND pl.status_id IN (2, 3)"; 
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Lấy toàn bộ ảnh phụ
    public function getListingImages($listingId) {
        $sql = "SELECT image_url, is_primary FROM listing_images WHERE listing_id = :listing_id ORDER BY sort_order ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Đếm tổng sản phẩm theo Danh mục
    public function getTotalActiveListingsByCategory($categoryId) {
        $sql = "SELECT COUNT(*) FROM product_listings WHERE status_id IN (2, 3) AND category_id = :cat_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // 6. Lấy danh sách sản phẩm theo Danh mục
    public function getPaginatedListingsByCategory($limit, $offset, $categoryId) {
        $sql = "SELECT pl.*, w.name AS ward_name, 
                       (SELECT image_url FROM listing_images WHERE listing_id = pl.id AND is_primary = 1 LIMIT 1) as image_url
                FROM product_listings pl
                LEFT JOIN wards w ON pl.ward_id = w.id
                WHERE pl.status_id IN (2, 3) AND pl.category_id = :cat_id
                ORDER BY pl.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
