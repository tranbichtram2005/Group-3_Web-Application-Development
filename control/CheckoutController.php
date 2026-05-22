<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/CartModel.php'; // Bổ sung CartModel để lấy dữ liệu giỏ hàng

class CheckoutController {
    private $db;
    private $orderModel;
    private $cartModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel(); // Khởi tạo model giỏ hàng
    }

    // Hiển thị giao diện trang Đặt hàng (Checkout)
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // 1. Đổ dữ liệu từ Database Giỏ hàng (Cart) vào thay vì dùng biến ảo
        $cartId = $this->cartModel->getCartId($userId);
        $rawCartItems = $this->cartModel->getCartItems($cartId);
        
        if (empty($rawCartItems)) {
            header("Location: index.php?controller=cart");
            exit;
        }

        // 2. Chuyển đổi key dữ liệu từ CartModel (database) cho khớp với Giao diện View
        $checkoutItems = [];
        foreach ($rawCartItems as $item) {
            $checkoutItems[] = [
                'listing_id' => $item['listing_id'] ?? 0,
                'product_name' => $item['title'] ?? $item['product_name'] ?? 'Sản phẩm 2Life',
                'image' => $item['image_url'] ?? $item['image'] ?? 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=100',
                'seller_id' => $item['seller_id'] ?? 1,
                'seller_name' => $item['seller_name'] ?? 'Người bán 2Life',
                'quantity' => $item['quantity'],
                'unit_price' => $item['price_snapshot'] ?? $item['price'] ?? 0
            ];
        }

        // 3. Lấy thông tin người mua từ Session (nếu có bảng User thì lấy từ database)
        $buyerName = $_SESSION['full_name'] ?? "Người mua";
        $buyerPhone = $_SESSION['phone'] ?? "(+84) 901 234 567";
        $buyerAddress = $_SESSION['address'] ?? "Phường Điện Hồng, Quận 10, TP. Hồ Chí Minh";

        require_once __DIR__ . '/../view/checkout.php';
    }

    // Xử lý khi nhấn nút Đặt Hàng cuối cùng (Đổ vào bảng Orders)
    public function processOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            
            // Lấy lại giỏ hàng từ DB để đảm bảo tính chính xác lúc đặt hàng
            $cartId = $this->cartModel->getCartId($userId);
            $rawCartItems = $this->cartModel->getCartItems($cartId);
            
            if (empty($rawCartItems)) {
                header("Location: index.php?controller=cart");
                exit;
            }

            // Map lại dữ liệu
            $checkoutItems = [];
            foreach ($rawCartItems as $item) {
                $checkoutItems[] = [
                    'listing_id' => $item['listing_id'] ?? 0,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price_snapshot'] ?? $item['price'] ?? 0,
                    'seller_id' => $item['seller_id'] ?? 1
                ];
            }

            // Cấu trúc data để đẩy vào OrderModel
            $orderData = [
                'buyer_id' => $userId,
                'seller_id' => $checkoutItems[0]['seller_id'], 
                'ward_id' => 1, 
                'street_address' => $_POST['streetAddress'] ?? '',
                'payment_method_id' => $_POST['paymentMethod'] ?? 1,
                'total_amount' => $_POST['totalFinal'] ?? 0,
                'shipping_note' => $_POST['shippingNote'] ?? '',
                'items' => $checkoutItems
            ];

            // 4. Đổ xuống database thông qua OrderModel
            $orderId = $this->orderModel->placeOrder($orderData);

            if ($orderId) {
                // Xóa các sản phẩm đã đặt khỏi giỏ hàng
                foreach ($checkoutItems as $item) {
                    $this->cartModel->removeItem($cartId, $item['listing_id']);
                }
                
                // Chuyển hướng thành công
                if ($orderData['payment_method_id'] != 1) { 
                    header("Location: payment_gateway.php?order_id=" . $orderId);
                } else {
                    echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php?controller=home';</script>";
                }
                exit;
            } else {
                echo "<script>alert('Lỗi hệ thống khi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }
}
?>