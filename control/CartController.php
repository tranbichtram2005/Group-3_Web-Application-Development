<?php
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';

class CartController {
    private $cartModel;
    private $voucherModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->voucherModel = new VoucherModel();
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
    // Xử lý AJAX áp dụng voucher - Trả về JSON
    public function applyVoucher() {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập!']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $code = trim($input['code'] ?? '');
        $orderTotal = intval($input['orderTotal'] ?? 0);

        if (empty($code)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập mã voucher.']);
            return;
        }

        $voucher = $this->voucherModel->getVoucherByCode($code, $orderTotal);

        if (isset($voucher['error'])) {
            echo json_encode(['status' => 'error', 'msg' => $voucher['error']]);
            return;
        }

        $discount = intval($voucher['discount_value']);
        // Không cho giảm quá tổng đơn hàng
        if ($discount > $orderTotal) $discount = $orderTotal;
        $finalTotal = $orderTotal - $discount;

        echo json_encode([
            'status'           => 'success',
            'voucherId'        => $voucher['id'],
            'discount'         => $discount,
            'discountFormat'   => '-' . number_format($discount, 0, ',', '.') . 'đ',
            'finalTotal'       => $finalTotal,
            'finalTotalFormat' => number_format($finalTotal, 0, ',', '.') . 'đ',
            'msg'              => 'Áp dụng voucher thành công!'
        ]);
    }
}
?>