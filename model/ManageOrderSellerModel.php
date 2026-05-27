<?php
class ManageOrderSellerModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOrdersBySeller($sellerId, $statusFilter = 0) {
        $sql = "SELECT o.*, os.name as status_name, pm.name as payment_method, ps.name as payment_status
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                JOIN payments p ON o.id = p.order_id
                JOIN payment_methods pm ON p.method_id = pm.id
                JOIN payment_statuses ps ON p.status_id = ps.id
                WHERE o.seller_id = :seller_id";
                
        if ($statusFilter > 0) {
            $sql .= " AND o.status_id = :status_id";
        }
        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $params = [':seller_id' => $sellerId];
        if ($statusFilter > 0) {
            $params[':status_id'] = $statusFilter;
        }
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderStatusCounts($sellerId) {
        $sql = "SELECT status_id, COUNT(id) as total FROM orders WHERE seller_id = :seller_id GROUP BY status_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $sellerId]);
        $rawCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $counts = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
        foreach ($rawCounts as $row) {
            $counts[$row['status_id']] = (int)$row['total'];
            $counts[0] += (int)$row['total'];
        }
        return $counts;
    }

    public function getOrderById($orderId, $sellerId) {
        $sql = "SELECT o.*, os.name as status_name, u.full_name as buyer_name, u.phone as buyer_phone
                FROM orders o
                JOIN order_statuses os ON o.status_id = os.id
                JOIN users u ON o.buyer_id = u.id
                WHERE o.id = :order_id AND o.seller_id = :seller_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':order_id' => $orderId, ':seller_id' => $sellerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.title, img.image_url 
                FROM order_items oi
                JOIN product_listings p ON oi.listing_id = p.id
                LEFT JOIN listing_images img ON p.id = img.listing_id AND img.is_primary = 1
                WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($orderId, $sellerId, $fromStatus, $toStatus) {
        try {
            $sql = "UPDATE orders SET status_id = :to_status 
                    WHERE id = :id AND seller_id = :seller_id AND status_id = :from_status";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':to_status' => $toStatus,
                ':id' => $orderId,
                ':seller_id' => $sellerId,
                ':from_status' => $fromStatus
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function cancelOrderBySeller($orderId, $sellerId, $reason) {
        try {
            $this->conn->beginTransaction();

            $sqlOrder = "UPDATE orders 
                         SET status_id = 6, cancel_reason = :reason, cancelled_by = :cancelled_by 
                         WHERE id = :id AND seller_id = :seller_id AND status_id IN (1, 3)";
            $stmtOrder = $this->conn->prepare($sqlOrder);
            $stmtOrder->execute([
                ':reason' => $reason,
                ':cancelled_by' => $sellerId,
                ':id' => $orderId,
                ':seller_id' => $sellerId
            ]);

            if ($stmtOrder->rowCount() === 0) {
                $this->conn->rollBack();
                return false;
            }

            $items = $this->getOrderItems($orderId);
            $sqlUpdateStock = "UPDATE product_listings SET stock_quantity = stock_quantity + :qty WHERE id = :id";
            $stmtStock = $this->conn->prepare($sqlUpdateStock);

            foreach ($items as $item) {
                $stmtStock->execute([
                    ':qty' => $item['quantity'],
                    ':id' => $item['listing_id']
                ]);
            }

            $sqlCheckPay = "SELECT id FROM payments WHERE order_id = :order_id AND status_id = 2";
            $stmtCheckPay = $this->conn->prepare($sqlCheckPay);
            $stmtCheckPay->execute([':order_id' => $orderId]);
            
            if ($stmtCheckPay->rowCount() > 0) {
                $sqlUpdatePay = "UPDATE payments SET status_id = 4, refunded_at = CURRENT_TIMESTAMP WHERE order_id = :order_id";
                $this->conn->prepare($sqlUpdatePay)->execute([':order_id' => $orderId]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>