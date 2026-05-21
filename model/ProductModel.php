<?php
require_once 'model/Database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Giả sử class Database của bạn trả về kết nối PDO
    }

    // Lấy thông tin chính của sản phẩm & người bán
    public function getProductDetail($id) {
        $conn = $this->db->getConnection();
        $sql = "SELECT p.*, u.full_name, u.avatar, u.created_at as seller_join_date, 
                       u.phone, u.is_verified 
                FROM products p 
                JOIN users u ON p.seller_id = u.id 
                WHERE p.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách hình ảnh của sản phẩm
    public function getProductImages($product_id) {
        $conn = $this->db->getConnection();
        $sql = "SELECT image_url FROM product_images WHERE product_id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông số kỹ thuật (để render ra bảng thông số như Chợ Tốt)
    public function getProductSpecs($product_id) {
        $conn = $this->db->getConnection();
        $sql = "SELECT spec_name, spec_value FROM product_specs WHERE product_id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy các sản phẩm cùng chuyên mục (Sản phẩm tương tự)
    public function getRelatedProducts($category_id, $exclude_id, $limit = 4) {
        $conn = $this->db->getConnection();
        $sql = "SELECT id, title, price, location, thumbnail 
                FROM products 
                WHERE category_id = :category_id AND id != :exclude_id 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Tăng lượt xem
    public function incrementViews($id) {
        $conn = $this->db->getConnection();
        $sql = "UPDATE products SET views = views + 1 WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>