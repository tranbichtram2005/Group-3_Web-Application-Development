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
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ']);
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
                $newQty = intval($input['quantity']);
                
                // ✅ Kiểm tra tồn kho trước khi cập nhật
                $stock = $this->cartModel->getStock($listingId);
                if ($newQty > $stock) {
                    echo json_encode([
                        'status' => 'error', 
                        'msg' => "Chỉ còn $stock sản phẩm trong kho!",
                        'maxQty' => $stock
                    ]);
                    return;
                }
                
                $this->cartModel->updateQuantity($cartId, $listingId, $newQty);
            }

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

    // Áp dụng voucher - Trả về JSON
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

        $userId = $_SESSION['user_id'];

        // ✅ Kiểm tra voucher đã dùng chưa
        $alreadyUsed = $this->voucherModel->isVoucherUsedByUser($code, $userId);
        if ($alreadyUsed) {
            echo json_encode(['status' => 'error', 'msg' => 'Bạn đã dùng voucher này rồi!']);
            return;
        }

        $voucher = $this->voucherModel->getVoucherByCode($code, $orderTotal);

        if (isset($voucher['error'])) {
            echo json_encode(['status' => 'error', 'msg' => $voucher['error']]);
            return;
        }

        $discount = intval($voucher['discount_value']);
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

    // ✅ AJAX lấy địa chỉ của user
    public function getAddresses() {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
            return;
        }
        $addresses = $this->cartModel->getUserAddresses($_SESSION['user_id']);
        echo json_encode(['status' => 'success', 'addresses' => $addresses]);
    }

    // ✅ AJAX thêm địa chỉ mới
    public function addAddress() {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
            return;
        }
        $input = json_decode(file_get_contents("php://input"), true);
        $fullName  = trim($input['full_name'] ?? '');
        $phone     = trim($input['phone'] ?? '');
        $province  = trim($input['province'] ?? '');
        $district  = trim($input['district'] ?? '');
        $ward      = trim($input['ward'] ?? '');
        $street    = trim($input['street'] ?? '');

        if (!$fullName || !$phone || !$province || !$district || !$ward || !$street) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin địa chỉ.']);
            return;
        }

        $result = $this->cartModel->addUserAddress(
            $_SESSION['user_id'], $fullName, $phone, $province, $district, $ward, $street
        );

        if ($result) {
            echo json_encode(['status' => 'success', 'msg' => 'Thêm địa chỉ thành công!', 'id' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Không thể thêm địa chỉ.']);
        }
    }

  // ==========================================
    // HÀM AJAX THÊM VÀO GIỎ HÀNG (BẢN PRO)
    // ==========================================
    public function addAjax() {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập để mua hàng!']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $listingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($listingId <= 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không hợp lệ!']);
            return;
        }

        try {
            $cartId = $this->cartModel->getCartId($userId);
            
            // 1. Kiểm tra tồn kho gốc
            $stock = (int)$this->cartModel->getStock($listingId);
            if ($stock <= 0) {
                echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm này đã hết hàng!']);
                return;
            }

            // 2. Kiểm tra xem trong giỏ đã có bao nhiêu cái rồi
            $cartItems = $this->cartModel->getCartItems($cartId);
            $itemExists = false;
            $currentQty = 0;
            
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if ($item['listing_id'] == $listingId) {
                        $itemExists = true;
                        $currentQty = (int)$item['quantity'];
                        break;
                    }
                }
            }

            // 3. Xử lý logic cộng dồn
            if ($itemExists) {
                // Nếu trong giỏ đã có, cộng thêm 1 xem có bị lố tồn kho không
                if ($currentQty + 1 > $stock) {
                    // Thông báo rõ ràng để khách không bị hoang mang
                    echo json_encode(['status' => 'error', 'msg' => "Bạn đã bỏ $currentQty món này vào giỏ rồi. Không thể thêm vượt quá tồn kho ($stock)!"]);
                    return;
                }
                $this->cartModel->updateQuantity($cartId, $listingId, $currentQty + 1);
            } else {
                // Nếu chưa có thì thêm món mới
                $this->cartModel->addItem($cartId, $listingId, 1);
            }

            // 4. BÍ KÍP ĐỂ HEADER NHẢY SỐ: Đếm lại tổng số món trong giỏ và gửi về
            $updatedCart = $this->cartModel->getCartItems($cartId);
            $newCartCount = is_array($updatedCart) ? count($updatedCart) : 0;

            echo json_encode([
                'status' => 'success', 
                'msg' => 'Đã thêm vào giỏ hàng!',
                'newCartCount' => $newCartCount // Trả số lượng về cho file JS chụp lại
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
?>