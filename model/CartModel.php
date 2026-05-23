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
                         u.full_name as seller_name, u.id as seller_id,
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

    // ✅ Lấy tồn kho của 1 listing
    public function getStock($listingId) {
        $stmt = $this->conn->prepare("SELECT stock_quantity FROM product_listings WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $listingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['stock_quantity'] : 0;
    }

    public function updateQuantity($cartId, $listingId, $qty) {
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

    // ✅ Lấy danh sách địa chỉ của user
    public function getUserAddresses($userId) {
        // Thử lấy từ bảng user_addresses nếu có; nếu không có cột tên/SĐT thì fallback
        try {
            $stmt = $this->conn->prepare("SELECT id, full_name, phone, province, district, ward, street, is_default 
                                          FROM user_addresses 
                                          WHERE user_id = :uid 
                                          ORDER BY is_default DESC, id DESC");
            $stmt->execute([':uid' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Nếu bảng chưa có các cột mới, fallback lấy từ users
            $stmt = $this->conn->prepare("SELECT id, full_name, phone, province_id as province, district_id as district, ward_id as ward, street, is_default 
                                          FROM user_addresses WHERE user_id = :uid ORDER BY is_default DESC, id DESC");
            $stmt->execute([':uid' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // ✅ Thêm địa chỉ mới cho user
    public function addUserAddress($userId, $fullName, $phone, $province, $district, $ward, $street) {
        try {
            // Nếu user chưa có địa chỉ nào, set làm mặc định
            $stmtCount = $this->conn->prepare("SELECT COUNT(*) FROM user_addresses WHERE user_id = :uid");
            $stmtCount->execute([':uid' => $userId]);
            $count = $stmtCount->fetchColumn();
            $isDefault = ($count == 0) ? 1 : 0;

            $stmt = $this->conn->prepare(
                "INSERT INTO user_addresses (user_id, full_name, phone, province, district, ward, street, is_default)
                 VALUES (:uid, :fn, :ph, :prov, :dist, :ward, :street, :def)"
            );
            $stmt->execute([
                ':uid'    => $userId,
                ':fn'     => $fullName,
                ':ph'     => $phone,
                ':prov'   => $province,
                ':dist'   => $district,
                ':ward'   => $ward,
                ':street' => $street,
                ':def'    => $isDefault
            ]);
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }

    // ✅ Set địa chỉ mặc định
    public function setDefaultAddress($userId, $addressId) {
        $this->conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :uid")->execute([':uid' => $userId]);
        $stmt = $this->conn->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = :id AND user_id = :uid");
        return $stmt->execute([':id' => $addressId, ':uid' => $userId]);
    }

    // ✅ Lấy địa chỉ mặc định
    public function getDefaultAddress($userId) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM user_addresses WHERE user_id = :uid ORDER BY is_default DESC, id ASC LIMIT 1"
            );
            $stmt->execute([':uid' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public function addItem($cartId, $listingId, $quantity) {
        
        // 1. Lấy giá tiền hiện tại của sản phẩm để làm price_snapshot
        $sqlPrice = "SELECT price FROM product_listings WHERE id = :listing_id";
        $stmtPrice = $this->conn->prepare($sqlPrice);
        $stmtPrice->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
        $stmtPrice->execute();
        $product = $stmtPrice->fetch(PDO::FETCH_ASSOC);
        $priceSnapshot = $product ? $product['price'] : 0;

        // 2. Kiểm tra xem sản phẩm này đã có trong giỏ chưa
        $sqlCheck = "SELECT quantity FROM cart_items WHERE cart_id = :cart_id AND listing_id = :listing_id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
        $stmtCheck->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        if ($stmtCheck->rowCount() > 0) {
            // Nếu đã có trong giỏ -> Chỉ cần cộng dồn số lượng
            $sql = "UPDATE cart_items SET quantity = quantity + :qty WHERE cart_id = :cart_id AND listing_id = :listing_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
            $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        } else {
            // Nếu chưa có -> Thêm mới dòng này vào giỏ, nhớ chèn thêm price_snapshot
            $sql = "INSERT INTO cart_items (cart_id, listing_id, quantity, price_snapshot) 
                    VALUES (:cart_id, :listing_id, :qty, :price_snapshot)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':listing_id', $listingId, PDO::PARAM_INT);
            $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':price_snapshot', $priceSnapshot, PDO::PARAM_INT); // Chèn giá tiền vào đây
        }
        
        return $stmt->execute();
    }
}
?>