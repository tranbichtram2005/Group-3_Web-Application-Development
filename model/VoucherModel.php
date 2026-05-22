<?php
require_once __DIR__ . '/Database.php';

class VoucherModel {
    private $db;

    public function __construct() {
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

    // ✅ Kiểm tra user đã dùng voucher này chưa
    public function isVoucherUsedByUser($code, $userId) {
        $code = strtoupper(trim($code));
        try {
            // Kiểm tra trong bảng orders xem user đã dùng voucher với code này chưa
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM orders o
                 JOIN vouchers v ON o.voucher_id = v.id
                 WHERE v.code = :code AND o.buyer_id = :uid LIMIT 1"
            );
            $stmt->execute([':code' => $code, ':uid' => $userId]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false; // Nếu bảng chưa có, cho phép dùng
        }
    }

    // ✅ Đánh dấu voucher đã được dùng (gọi sau khi tạo đơn thành công)
    public function markVoucherUsed($voucherId, $userId, $orderId) {
        try {
            // Có thể tạo bảng voucher_usage nếu muốn chi tiết hơn
            // Ở đây chúng ta dựa vào quan hệ orders.voucher_id để track
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>