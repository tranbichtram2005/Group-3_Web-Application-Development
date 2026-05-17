<?php
// Bắt buộc phải có __DIR__ kèm dấu nháy đơn để PHP không bị lạc đường
require_once __DIR__ . '/../model/CartModel.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    // Giao diện hiển thị giỏ hàng
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: view/Auth/login.php");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $cartId = $this->cartModel->getCartId($userId);
        
        // Lấy danh sách sản phẩm từ DB
        $cartItems = $this->cartModel->getCartItems($cartId);
        
        // ĐÃ SỬA: Gọi đúng file giao diện nằm trong thư mục view/app/
        require_once __DIR__ . '/../view/app/cart.php';
    }

    // Xử lý AJAX - Trả về JSON
    public function updateAjax() {
        header('Content-Type: application/json; charset=utf-8');

        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập!']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu Webservice không hợp lệ']);
            return;
        }

        $listingId = $input['listingId'];
        $action = $input['action']; 
        
        $userId = $_SESSION['user_id'];
        $cartId = $this->cartModel->getCartId($userId);

        try {
            if ($action === 'remove') {
                $this->cartModel->removeItem($cartId, $listingId);
            } elseif ($action === 'update') {
                $newQty = $input['quantity'];
                $this->cartModel->updateQuantity($cartId, $listingId, $newQty);
            }

            // Tính toán lại tổng tiền để trả về cho Client
            $updatedItems = $this->cartModel->getCartItems($cartId);
            $totalAmount = 0;
            $totalItems = 0;
            
            foreach ($updatedItems as $item) {
                $totalAmount += ($item['price_snapshot'] * $item['quantity']);
                $totalItems++; 
            }

            echo json_encode([
                'status' => 'success',
                'totalAmountFormat' => number_format($totalAmount, 0, ',', '.') . 'đ',
                'totalItems' => $totalItems
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
?>