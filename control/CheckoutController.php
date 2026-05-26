<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/CartModel.php';

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
        $this->cartModel = new CartModel();
    }

    // ===============================================
    // HIỂN THỊ GIAO DIỆN THANH TOÁN
    // ===============================================
    public function index() {
        $userId = $_SESSION['user_id'];
        $checkoutItems = [];
        $isDirectCheckout = false;
        $directListingId = 0;
        $directQuantity = 0;

        // Luồng 1: Mua ngay 1 sản phẩm từ trang chi tiết sản phẩm
        if (isset($_GET['listing_id']) && isset($_GET['quantity'])) {
            $isDirectCheckout = true;
            $directListingId = (int)$_GET['listing_id'];
            $directQuantity = (int)$_GET['quantity'];

            // ĐÃ SỬA: p.price as price_snapshot để không bị lỗi dòng 44 nữa
            $query = "SELECT p.id as listing_id, p.title, p.price as price_snapshot, p.stock_quantity, p.user_id as seller_id, 
                             u.full_name as seller_name, img.image_url
                      FROM product_listings p
                      JOIN users u ON p.user_id = u.id
                      LEFT JOIN listing_images img ON p.id = img.listing_id AND img.is_primary = 1
                      WHERE p.id = :listing_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':listing_id' => $directListingId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $checkoutItems[] = [
                    'listing_id' => $product['listing_id'],
                    'product_name' => $product['title'],
                    'image' => $product['image_url'] ?? 'https://via.placeholder.com/100',
                    'seller_id' => $product['seller_id'],
                    'seller_name' => $product['seller_name'],
                    'quantity' => $directQuantity,
                    'unit_price' => $product['price_snapshot'],
                    'stock' => $product['stock_quantity'] ?? 99
                ];
            }
        } 
        // Luồng 2: Thanh toán toàn bộ sản phẩm từ giỏ hàng
        else {
            $cartId = $this->cartModel->getCartId($userId);
            $rawCartItems = $this->cartModel->getCartItems($cartId);
            foreach ($rawCartItems as $item) {
                $checkoutItems[] = [
                    'listing_id' => $item['listing_id'],
                    'product_name' => $item['title'],
                    'image' => $item['image_url'] ?? 'https://via.placeholder.com/100',
                    'seller_id' => $item['seller_id'] ?? 1,
                    'seller_name' => $item['seller_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price_snapshot'],
                    'stock' => $item['stock_quantity'] ?? 99
                ];
            }
        }

        if (empty($checkoutItems)) {
            header("Location: index.php?controller=cart");
            exit;
        }

        $userQuery = "SELECT full_name, phone FROM users WHERE id = :id LIMIT 1";
        $userStmt = $this->db->prepare($userQuery);
        $userStmt->execute([':id' => $userId]);
        $userObj = $userStmt->fetch(PDO::FETCH_ASSOC);

        $buyerName = $userObj['full_name'] ?? ($_SESSION['full_name'] ?? 'Người mua');
        $buyerPhone = $userObj['phone'] ?? ($_SESSION['phone'] ?? '');

        $buyerProvince = 'Thành phố Hồ Chí Minh';
        $buyerDistrict = '';
        $buyerWard = '';
        $buyerStreet = '';
        $fullAddress = '';

        if (isset($_SESSION['temp_address'])) {
            $fullAddress   = $_SESSION['temp_address']['street_address'];
            $buyerProvince = $_SESSION['temp_address']['province'];
            $buyerDistrict = $_SESSION['temp_address']['district'];
            $buyerWard     = $_SESSION['temp_address']['ward'];
            $buyerStreet   = $_SESSION['temp_address']['street'];
        } else {
            $addrQuery = "SELECT ua.*, p.name as prov_name, d.name as dist_name, w.name as ward_name 
                          FROM user_addresses ua
                          LEFT JOIN provinces p ON ua.province_id = p.id
                          LEFT JOIN districts d ON ua.district_id = d.id
                          LEFT JOIN wards w ON ua.ward_id = w.id
                          WHERE ua.user_id = :user_id ORDER BY ua.is_default DESC LIMIT 1";
            $addrStmt = $this->db->prepare($addrQuery);
            $addrStmt->execute([':user_id' => $userId]);
            $defaultAddr = $addrStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($defaultAddr) {
                $buyerProvince = $defaultAddr['prov_name'] ?? '';
                $buyerDistrict = $defaultAddr['dist_name'] ?? '';
                $buyerWard     = $defaultAddr['ward_name'] ?? '';
                $buyerStreet   = $defaultAddr['street'] ?? '';
                $fullAddress   = implode(', ', array_filter([$buyerStreet, $buyerWard, $buyerDistrict, $buyerProvince]));
            } else {
                $fullAddress   = ''; 
                $buyerProvince = 'Thành phố Hồ Chí Minh';
            }
        }

        $hcmKw = ['hồ chí minh', 'ho chi minh', 'hcm', 'sài gòn', 'saigon'];
        $isHcm = false;
        foreach ($hcmKw as $kw) {
            if (strpos(strtolower($buyerProvince), $kw) !== false) {
                $isHcm = true;
                break;
            }
        }
        $shippingFee = $isHcm ? 30000 : 50000;

        require_once __DIR__ . '/../view/checkout.php';
    }

    // ===============================================
    // API: LẤY DỮ LIỆU ĐỊA CHỈ TỪ DATABASE
    // ===============================================
    public function getProvinces() {
        header('Content-Type: application/json');
        $stmt = $this->db->query("SELECT id, name FROM provinces ORDER BY name ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    public function getDistricts() {
        header('Content-Type: application/json');
        $provinceId = $_GET['province_id'] ?? 0;
        $stmt = $this->db->prepare("SELECT id, name FROM districts WHERE province_id = ? ORDER BY name ASC");
        $stmt->execute([$provinceId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    public function getWards() {
        header('Content-Type: application/json');
        $districtId = $_GET['district_id'] ?? 0;
        $stmt = $this->db->prepare("SELECT id, name FROM wards WHERE district_id = ? ORDER BY name ASC");
        $stmt->execute([$districtId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // ===============================================
    // AJAX: LƯU TRỮ TẠM THỜI ĐỊA CHỈ VÀO SESSION
    // ===============================================
    public function saveAddressSessionAjax() {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents("php://input"), true);
        
        if ($input) {
            $_SESSION['temp_address'] = [
                'street_address' => $input['streetAddress'],
                'province'       => $input['province'],
                'district'       => $input['district'],
                'ward'           => $input['ward'],
                'street'         => $input['street']
            ];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ']);
        }
        exit;
    }

    // ===============================================
    // TIẾN HÀNH TẠO ĐƠN HÀNG
    // ===============================================
    public function processOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $checkoutItems = [];
            $cartId = $this->cartModel->getCartId($userId);
            
            if (!empty($_POST['direct_listing_id']) && !empty($_POST['direct_quantity'])) {
                $listingId = (int)$_POST['direct_listing_id'];
                $quantity = (int)$_POST['direct_quantity'];
                
                // ĐÃ SỬA Ở ĐÂY: Đổi 'price_snapshot' thành 'price as price_snapshot' 
                // để khớp với bảng product_listings
                $query = "SELECT id as listing_id, price as price_snapshot, user_id as seller_id FROM product_listings WHERE id = :listing_id LIMIT 1";
                $stmt = $this->db->prepare($query);
                $stmt->execute([':listing_id' => $listingId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $checkoutItems[] = [
                        'listing_id' => $product['listing_id'],
                        'quantity' => $quantity,
                        'unit_price' => $product['price_snapshot'],
                        'seller_id' => $product['seller_id']
                    ];
                }
            } else {
                $rawCartItems = $this->cartModel->getCartItems($cartId);
                foreach ($rawCartItems as $item) {
                    $checkoutItems[] = [
                        'listing_id' => $item['listing_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price_snapshot'],
                        'seller_id' => $item['seller_id'] ?? 1
                    ];
                }
            }

            if (empty($checkoutItems)) {
                header("Location: index.php?controller=cart");
                exit;
            }

            $totalFinal = $_POST['totalFinal'] ?? 0;
            $paymentMethod = (int)($_POST['paymentMethod'] ?? 1); // 1 = COD, 2 = VNPay
            
            // Xử lý Voucher
            $voucherCode = $_POST['voucherCodeInput'] ?? '';
            $discountAmount = (float)($_POST['voucherDiscountInput'] ?? 0);
            $dbVoucherId = null;

            if (!empty($voucherCode)) {
                $vStmt = $this->db->prepare("SELECT id FROM vouchers WHERE code = :code LIMIT 1");
                $vStmt->execute([':code' => $voucherCode]);
                $vRes = $vStmt->fetch(PDO::FETCH_ASSOC);
                if ($vRes) {
                    $dbVoucherId = $vRes['id'];
                }
            }

            $orderData = [
                'buyer_id'          => $userId,
                'seller_id'         => $checkoutItems[0]['seller_id'], 
                'ward_id'           => 1, 
                'street_address'    => $_POST['streetAddress'] ?? '',
                'payment_method_id' => $paymentMethod,
                'total_amount'      => $totalFinal,
                'discount_amount'   => $discountAmount,
                'voucher_id'        => $dbVoucherId,
                'shipping_note'     => $_POST['shippingNote'] ?? '',
                'items'             => $checkoutItems
            ];

            $orderId = $this->orderModel->placeOrder($orderData);

            if ($orderId) {
                if (empty($_POST['direct_listing_id'])) {
                    foreach ($checkoutItems as $item) {
                        $this->cartModel->removeItem($cartId, $item['listing_id']);
                    }
                }
                
                // Rẽ nhánh thanh toán
                if ($paymentMethod === 2) {
                    $this->vnpayPayment($orderId, $totalFinal);
                } else {
                    // COD - Tiền mặt
                    echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php?controller=order';</script>";
                }
                exit;
            } else {
                echo "<script>alert('Lỗi hệ thống khi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }

    // ==========================================
    // KHỐI API VNPAY
    // ==========================================
    private function vnpayPayment($orderId, $amount) {
        date_default_timezone_set('Asia/Ho_Chi_Minh'); 

        $vnp_TmnCode = "FRCAG0XC"; // Thay bằng mã Sandbox của bạn
        $vnp_HashSecret = "2L1YRFYMFU0MKYMVJGTH1VJHKSGC97DK"; // Thay bằng mã Sandbox của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $vnp_Returnurl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?controller=checkout&action=vnpayReturn"; 

        $vnp_TxnRef = $orderId . "_" . time(); 
        $vnp_OrderInfo = "Thanh toan don hang 2Life ma: " . $orderId;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amount * 100; 
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $startTime = date("YmdHis");
        $expireTime = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expireTime 
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        exit;
    }

    public function vnpayReturn() {
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        
        if ($vnp_ResponseCode === '00') {
            // Tách lấy mã order_id từ chuỗi vnp_TxnRef (Định dạng lúc tạo: orderId_timestamp)
            $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
            $parts = explode('_', $vnp_TxnRef);
            $orderId = (int)$parts[0];

            if ($orderId > 0) {
                // CHỐT: Thanh toán xong tự động cập nhật sang trạng thái CHỜ CHUẨN BỊ (status_id = 2)
                $stmt = $this->db->prepare("UPDATE orders SET status_id = 2 WHERE id = :order_id");
                $stmt->execute([':order_id' => $orderId]);
            }

            // Đồng bộ với hộp thoại Toast xanh cam cậu vừa làm luôn nhe
            $_SESSION['toast_msg'] = 'Thanh toán đơn hàng qua VNPay thành công!';
            header("Location: index.php?controller=order");
            exit;
        } else {
            $_SESSION['toast_msg'] = 'Giao dịch thanh toán thất bại hoặc đã bị hủy bỏ!';
            header("Location: index.php?controller=cart");
            exit;
        }
    }
}
?>