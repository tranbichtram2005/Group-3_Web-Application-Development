<?php
require_once __DIR__ . '/Database.php';

class VoucherModel {
    private $db;

    public function __construct() {
        // Khởi tạo đối tượng Database theo đúng file của bạn
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllVouchers() {
        $query = "SELECT * FROM vouchers ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createVoucher($code, $discountValue, $minOrderValue, $expiryDate) {
        $query = "INSERT INTO vouchers (code, discount_value, min_order_value, expiry_date) VALUES (:code, :discountValue, :minOrderValue, :expiryDate)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':code' => $code,
            ':discountValue' => $discountValue,
            ':minOrderValue' => $minOrderValue,
            ':expiryDate' => $expiryDate
        ]);
    }

    public function deleteVoucher($voucherId) {
        $query = "DELETE FROM vouchers WHERE id = :voucherId";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':voucherId' => $voucherId]);
    }
}
?>