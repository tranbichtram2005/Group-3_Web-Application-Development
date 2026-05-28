<?php
require_once __DIR__ . '/Database.php';

class VoucherModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllVouchers() {
    $query = "SELECT v.*, vs.name AS status_name 
              FROM vouchers v
              LEFT JOIN voucher_statuses vs ON v.status_id = vs.id
              ORDER BY v.created_at DESC";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function isCodeExists($code) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM vouchers WHERE code = :code");
    $stmt->execute([':code' => $code]);
    return $stmt->fetchColumn() > 0;
}

public function createVoucher($code, $typeId, $discountValue, $maxDiscount, $minOrderValue, $totalQty, $startsAt, $expiresAt) {
        try {
            $createdBy = $_SESSION['user_id'] ?? 1;
            $query = "INSERT INTO vouchers 
                        (code, type_id, discount_value, max_discount_amount, min_order_value, total_quantity, used_quantity, starts_at, expires_at, status_id, created_by) 
                      VALUES 
                        (:code, :typeId, :discountValue, :maxDiscount, :minOrderValue, :totalQty, 0, :startsAt, :expiresAt, 1, :createdBy)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':code'          => $code,
                ':typeId'        => $typeId,
                ':discountValue' => $discountValue,
                ':maxDiscount'   => $maxDiscount ?: null, // Nếu rỗng thì lưu NULL
                ':minOrderValue' => $minOrderValue ?: 0,
                ':totalQty'      => $totalQty,
                ':startsAt'      => $startsAt,
                ':expiresAt'     => $expiresAt,
                ':createdBy'     => $createdBy,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

public function deleteVoucher($voucherId) {
        // Chỉ cho phép xóa khi chưa có lượt sử dụng nào (used_quantity = 0)
        $query = "DELETE FROM vouchers WHERE id = :voucherId AND used_quantity = 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':voucherId' => $voucherId]);
        // Trả về true nếu xóa thành công (có dòng bị ảnh hưởng), false nếu voucher đã dùng hoặc không tồn tại
        return $stmt->rowCount() > 0;
    }

public function getVoucherByCode($code, $orderTotal) {
    $code = strtoupper(trim($code));
    $query = "SELECT * FROM vouchers WHERE code = :code AND status_id = 1 LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute([':code' => $code]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voucher) {
        return ['error' => 'Mã voucher không tồn tại.'];
    }
    if (!empty($voucher['expires_at']) && strtotime($voucher['expires_at']) < time()) {
        return ['error' => 'Mã voucher đã hết hạn.'];
    }
    if (!empty($voucher['min_order_value']) && $orderTotal < $voucher['min_order_value']) {
        return ['error' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher['min_order_value'], 0, ',', '.') . 'đ để dùng voucher này.'];
    }
    if ($voucher['used_quantity'] >= $voucher['total_quantity']) {
        return ['error' => 'Mã voucher đã hết lượt sử dụng.'];
    }
    return $voucher;
}

public function isVoucherUsedByUser($code, $userId) {
    $code = strtoupper(trim($code));
    try {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM orders o
             JOIN vouchers v ON o.voucher_id = v.id
             WHERE v.code = :code AND o.buyer_id = :uid LIMIT 1"
        );
        $stmt->execute([':code' => $code, ':uid' => $userId]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
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