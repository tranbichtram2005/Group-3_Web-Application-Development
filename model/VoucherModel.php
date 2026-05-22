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

    /**
     * Tìm voucher theo code, kiểm tra còn hạn và đủ điều kiện đơn hàng
     * Trả về voucher data hoặc ['error' => 'message']
     */
    public function getVoucherByCode($code, $orderTotal) {
        $code = strtoupper(trim($code));
        $query = "SELECT * FROM vouchers WHERE code = :code LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':code' => $code]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$voucher) {
            return ['error' => 'Mã voucher không tồn tại.'];
        }
        if (!empty($voucher['expiry_date']) && strtotime($voucher['expiry_date']) < time()) {
            return ['error' => 'Mã voucher đã hết hạn.'];
        }
        if (!empty($voucher['min_order_value']) && $orderTotal < $voucher['min_order_value']) {
            return ['error' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher['min_order_value'], 0, ',', '.') . 'đ để dùng voucher này.'];
        }
        return $voucher;
    }
}
?>