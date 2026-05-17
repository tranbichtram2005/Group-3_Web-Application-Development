<?php
require_once 'model/Database.php';

class CartModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCartId($userId) {
        $query = "SELECT id FROM carts WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            return $cart['id'];
        } else {
            $queryInsert = "INSERT INTO carts (user_id) VALUES (:user_id)";
            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->execute([':user_id' => $userId]);
            return $this->conn->lastInsertId();
        }
    }

    public function getCartItems($cartId) {
        $query = "SELECT ci.listing_id, ci.quantity, ci.price_snapshot, 
                         p.title, p.stock_quantity, 
                         u.full_name as seller_name, 
                         img.image_url
                  FROM cart_items ci
                  JOIN product_listings p ON ci.listing_id = p.id
                  JOIN users u ON p.user_id = u.id
                  LEFT JOIN listing_images img ON p.id = img.listing_id AND img.is_primary = 1
                  WHERE ci.cart_id = :cart_id
                  ORDER BY ci.updated_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':cart_id' => $cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($cartId, $listingId, $qty) {
        // Tự động xóa nếu user bấm lùi số lượng về 0
        if ($qty <= 0) {
            return $this->removeItem($cartId, $listingId);
        }
        $query = "UPDATE cart_items SET quantity = :qty WHERE cart_id = :cart_id AND listing_id = :listing_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':qty' => $qty,
            ':cart_id' => $cartId,
            ':listing_id' => $listingId
        ]);
    }

    public function removeItem($cartId, $listingId) {
        $query = "DELETE FROM cart_items WHERE cart_id = :cart_id AND listing_id = :listing_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':cart_id' => $cartId,
            ':listing_id' => $listingId
        ]);
    }
}
?>