<?php
class OrderModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function placeOrder($data) {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo đơn hàng chính
            $queryOrder = "INSERT INTO orders
                           (buyer_id, seller_id, voucher_id, status_id, ward_id, street_address, total_amount, discount_amount, shipping_note)
                           VALUES (:buyer_id, :seller_id, :voucher_id, 1, :ward_id, :street_address, :total_amount, :discount_amount, :note)";
           
            $stmtOrder = $this->conn->prepare($queryOrder);
            $stmtOrder->execute([
                ':buyer_id'        => $data['buyer_id'],
                ':seller_id'       => $data['seller_id'],
                ':voucher_id'      => $data['voucher_id'] ?? null,
                ':ward_id'         => $data['ward_id'],
                ':street_address'  => $data['street_address'],
                ':total_amount'    => $data['total_amount'],
                ':discount_amount' => $data['discount_amount'] ?? 0,
                ':note'            => $data['shipping_note'] ?? ''
            ]);

            $orderId = $this->conn->lastInsertId();

            // 2. Lưu chi tiết sản phẩm
            $queryItem = "INSERT INTO order_items (order_id, listing_id, quantity, unit_price)
                          VALUES (:order_id, :listing_id, :quantity, :unit_price)";
            $stmtItem = $this->conn->prepare($queryItem);

            foreach ($data['items'] as $item) {
                $stmtItem->execute([
                    ':order_id'   => $orderId,
                    ':listing_id' => $item['listing_id'],
                    ':quantity'   => $item['quantity'],
                    ':unit_price' => $item['unit_price']
                ]);

                // 3. Giảm tồn kho
                $queryUpdateStock = "UPDATE product_listings SET stock_quantity = stock_quantity - :qty
                                     WHERE id = :id AND stock_quantity >= :qty";
                $stmtStock = $this->conn->prepare($queryUpdateStock);
                $stmtStock->execute([':qty' => $item['quantity'], ':id' => $item['listing_id']]);
            }

            // 4. Khởi tạo bản ghi thanh toán
            $queryPay = "INSERT INTO payments (order_id, method_id, status_id, amount)
                         VALUES (:order_id, :method_id, 1, :amount)";
            $stmtPay = $this->conn->prepare($queryPay);
            $stmtPay->execute([
                ':order_id'  => $orderId,
                ':method_id' => $data['payment_method_id'],
                ':amount'    => $data['total_amount']
            ]);

            $this->conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("placeOrder error: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Cập nhật trạng thái đơn hàng
    public function updateOrderStatus($orderId, $status) {
        // Map string status sang status_id (điều chỉnh theo DB thực tế)
        $statusMap = [
            'pending'   => 1,
            'confirmed' => 2,
            'shipping'  => 3,
            'delivered' => 4,
            'cancelled' => 5,
        ];
        $statusId = $statusMap[$status] ?? 1;

        try {
            $stmt = $this->conn->prepare("UPDATE orders SET status_id = :sid WHERE id = :id");
            return $stmt->execute([':sid' => $statusId, ':id' => $orderId]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Cập nhật thanh toán thành công (IPN hoặc Return URL)
    public function markAsPaid($order_id, $transaction_ref) {
        try {
            $query = "UPDATE payments SET status_id = 2, transaction_ref = :ref, completed_at = CURRENT_TIMESTAMP
                      WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':ref' => $transaction_ref, ':order_id' => $order_id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>