<?php
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';
require_once __DIR__ . '/../model/Database.php'; // Bổ sung để fix lỗi kết nối DB

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
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $cartId = $this->cartModel->getCartId($userId);
        
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

    // Để phòng hờ Giỏ hàng gọi nhầm tên hàm
    public function applyVoucher() {
        $this->applyVoucherAjax();
    }

    // ==========================================
    // ÁP DỤNG VOUCHER (ĐÃ FIX KẾT NỐI DATABASE VÀ BIẾN SỐ)
    // ==========================================
    public function applyVoucherAjax() {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập!']);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $code = trim($input['code'] ?? '');
        
        // Hỗ trợ cả 2 biến từ cart.php (orderTotal) và checkout.php (subtotal)
        $orderTotal = (float)($input['subtotal'] ?? $input['orderTotal'] ?? 0);

        if (empty($code)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập mã voucher.']);
            return;
        }

        $userId = $_SESSION['user_id'];

        try {
            // Tự khởi tạo kết nối DB riêng để tránh sập Model
            $database = new Database();
            $db = $database->getConnection();
            
            // 1. Tìm voucher trong CSDL
            $stmt = $db->prepare("SELECT * FROM vouchers WHERE code = :code LIMIT 1");
            $stmt->execute([':code' => $code]);
            $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$voucher) {
                echo json_encode(['status' => 'error', 'msg' => 'Mã giảm giá không tồn tại hoặc đã hết hạn!']);
                return;
            }

            // 2. Kiểm tra xem người dùng đã sử dụng mã này chưa 
            // (Tra cứu lịch sử mua hàng trong bảng orders)
            $stmtCheck = $db->prepare("SELECT id FROM orders WHERE buyer_id = :user_id AND voucher_id = :voucher_id LIMIT 1");
            $stmtCheck->execute([':user_id' => $userId, ':voucher_id' => $voucher['id']]);
            if ($stmtCheck->fetch()) {
                echo json_encode(['status' => 'error', 'msg' => 'Cậu đã sử dụng voucher này cho đơn hàng trước đó rồi!']);
                return;
            }

            // 3. Kiểm tra giá trị tối thiểu
            if ($orderTotal < $voucher['min_order_value']) {
                echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng chưa đạt tối thiểu ' . number_format($voucher['min_order_value'], 0, ',', '.') . 'đ']);
                return;
            }

            // 4. Tính toán giảm giá thành công
            $discount = (float)$voucher['discount_value'];
            if ($discount > $orderTotal) {
                $discount = $orderTotal; 
            }
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

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi truy xuất hệ thống: ' . $e->getMessage()]);
        }
    }

    // AJAX lấy địa chỉ của user
    public function getAddresses() {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
            return;
        }
        $addresses = $this->cartModel->getUserAddresses($_SESSION['user_id']);
        echo json_encode(['status' => 'success', 'addresses' => $addresses]);
    }

    // AJAX thêm địa chỉ mới
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
    // HÀM AJAX THÊM VÀO GIỎ HÀNG (BẢN PRO ĐỒNG BỘ DEAL)
    // ==========================================
    public function addAjax() {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập để mua hàng!']);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // 1. Hỗ trợ bắt ID từ GET (Trang chủ) và POST (Phòng Chat)
        $listingId = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
        $qtyToAdd = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($listingId <= 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không hợp lệ!']);
            return;
        }

        try {
            $cartId = $this->cartModel->getCartId($userId);
            
            // 2. Lấy thông tin sản phẩm (Giá gốc và Tồn kho)
            $product = $this->cartModel->getListingInfo($listingId);
            if (!$product || $product['stock_quantity'] <= 0) {
                echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm này đã hết hàng!']);
                return;
            }

            $stock = (int)$product['stock_quantity'];
            $finalPrice = $product['price']; // Tạm thời dùng giá gốc
            $offerId = null;

            // ===============================================
            // 3. KIỂM TRA DEAL GIÁ (Sự kỳ diệu nằm ở đây)
            // ===============================================
            $validDeal = $this->cartModel->checkValidDeal($userId, $listingId);
            if ($validDeal) {
                $finalPrice = $validDeal['proposed_price']; // Phát hiện Deal! Ghi đè giá rẻ vào.
                $offerId = $validDeal['id'];                // Cột offer_id sẽ lưu vết cái Deal này
            }

            // 4. Kiểm tra số lượng trong giỏ xem có vượt tồn kho không
            $cartItems = $this->cartModel->getCartItems($cartId);
            $currentQty = 0;
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if ($item['listing_id'] == $listingId) {
                        $currentQty = (int)$item['quantity'];
                        break;
                    }
                }
            }

            if ($currentQty + $qtyToAdd > $stock) {
                echo json_encode(['status' => 'error', 'msg' => "Giỏ hàng đã có $currentQty món này. Kho chỉ còn $stock, không thể thêm!"]);
                return;
            }

            // 5. Lưu vào Database (Hàm upsert thông minh sẽ tự cập nhật giá mới nếu trùng)
            $this->cartModel->upsertCartItem($cartId, $listingId, $qtyToAdd, $finalPrice, $offerId);

            // 6. Cập nhật đếm số lượng giỏ hàng trên Header
            $updatedCart = $this->cartModel->getCartItems($cartId);
            $newCartCount = is_array($updatedCart) ? count($updatedCart) : 0;

            echo json_encode([
                'status' => 'success', 
                'msg' => 'Đã thêm vào giỏ hàng!',
                'newCartCount' => $newCartCount
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
?>