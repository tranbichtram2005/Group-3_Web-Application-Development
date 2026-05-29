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

        // Luồng 1: Mua ngay 1 sản phẩm
        if (isset($_GET['listing_id']) && isset($_GET['quantity'])) {
            $isDirectCheckout = true;
            $directListingId = (int)$_GET['listing_id'];
            $directQuantity = (int)$_GET['quantity'];

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
                $isDeal = false; $dealQty = 0;
                $validDeal = $this->cartModel->checkValidDeal($userId, $product['listing_id']);
                if ($validDeal) {
                    $dealQty = (int)$validDeal['quantity'];
                    if ($directQuantity != $dealQty) {
                        $_SESSION['checkout_error'] = ['title' => 'Cảnh báo!', 'msg' => 'Sai số lượng chốt deal!'];
                        header("Location: index.php?controller=cart"); exit;
                    }
                    $product['price_snapshot'] = $validDeal['proposed_price']; $isDeal = true;
                }
                $checkoutItems[] = [
                    'listing_id' => $product['listing_id'], 'product_name' => $product['title'],
                    'image' => $product['image_url'] ?? 'https://via.placeholder.com/100',
                    'seller_id' => $product['seller_id'], 'seller_name' => $product['seller_name'],
                    'quantity' => $directQuantity, 'unit_price' => $product['price_snapshot'],
                    'stock' => $product['stock_quantity'] ?? 99, 'is_deal' => $isDeal, 'deal_qty' => $dealQty
                ];
            }
        } 
        // Luồng 2: Thanh toán từ giỏ hàng
        else {
            $cartId = $this->cartModel->getCartId($userId);
            $this->cartModel->cleanExpiredDealsInCart($cartId);
            $rawCartItems = $this->cartModel->getCartItems($cartId);
            $selectedIds = $_POST['selected_ids'] ?? $_GET['selected_ids'] ?? ($_SESSION['checkout_selected_ids'] ?? '');
            
            if (empty($selectedIds)) {
                echo "<script>alert('Vui lòng chọn ít nhất 1 sản phẩm!'); window.location.href='index.php?controller=cart';</script>"; exit;
            }
            $_SESSION['checkout_selected_ids'] = $selectedIds; 
            $idArray = explode(',', $selectedIds);
            
            $rawCartItems = array_filter($rawCartItems, function($item) use ($idArray) {
                return in_array($item['listing_id'], $idArray);
            });

            if (empty($rawCartItems)) { header("Location: index.php?controller=cart"); exit; }

            foreach ($rawCartItems as $item) {
                $isDeal = false; $dealQty = 0;
                $validDeal = $this->cartModel->checkValidDeal($userId, $item['listing_id']);
                if ($validDeal && isset($item['offer_id']) && $item['offer_id'] == $validDeal['id']) {
                    $dealQty = (int)$validDeal['quantity'];
                    if ((int)$item['quantity'] != $dealQty) {
                        $_SESSION['checkout_error'] = ['title' => 'Cảnh báo!', 'msg' => 'Sai số lượng chốt deal!'];
                        header("Location: index.php?controller=cart"); exit;
                    }
                    $isDeal = true;
                }
                $checkoutItems[] = [
                    'listing_id' => $item['listing_id'], 'product_name' => $item['title'],
                    'image' => $item['image_url'] ?? 'https://via.placeholder.com/100',
                    'seller_id' => $item['seller_id'] ?? 1, 'seller_name' => $item['seller_name'],
                    'quantity' => $item['quantity'], 'unit_price' => $item['price_snapshot'],
                    'stock' => $item['stock_quantity'] ?? 99, 'is_deal' => $isDeal, 'deal_qty' => $dealQty
                ];
            }
        }

        if (empty($checkoutItems)) { header("Location: index.php?controller=cart"); exit; }

        // ==========================================
        // KHÔI PHỤC LẠI LOGIC ĐỔ DỮ LIỆU ĐỊA CHỈ TỪ DB
        // ==========================================
        $userQuery = "SELECT full_name, phone FROM users WHERE id = :id LIMIT 1";
        $userStmt = $this->db->prepare($userQuery);
        $userStmt->execute([':id' => $userId]);
        $userObj = $userStmt->fetch(PDO::FETCH_ASSOC);

        // Các biến này sẽ được đẩy ra ngoài giao diện (View)
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
            // Lấy địa chỉ mặc định từ Database
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
                // Nối thành 1 chuỗi dài để in ra màn hình
                $fullAddress   = implode(', ', array_filter([$buyerStreet, $buyerWard, $buyerDistrict, $buyerProvince]));
            } else {
                $fullAddress   = ''; 
                $buyerProvince = 'Thành phố Hồ Chí Minh';
            }
        }

        $hcmKw = ['hồ chí minh', 'ho chi minh', 'hcm', 'sài gòn', 'saigon'];
        $isHcm = false;
        foreach ($hcmKw as $kw) {
            if (strpos(strtolower($buyerProvince), $kw) !== false) { $isHcm = true; break; }
        }
        $shippingFeePerShop = $isHcm ? 30000 : 50000;

        // BỔ SUNG NHÓM SẢN PHẨM THEO SHOP ĐỂ VIEW HIỂN THỊ CHUẨN XÁC
        $groupedItems = [];
        foreach ($checkoutItems as $item) {
            $groupedItems[$item['seller_id']][] = $item;
        }
        $totalShops = count($groupedItems);
        $shippingFee = $totalShops * $shippingFeePerShop; // Tiền ship nhân lên theo số Shop

        require_once __DIR__ . '/../view/checkout.php';
    }

    public function getProvinces() {
        header('Content-Type: application/json');
        echo json_encode($this->db->query("SELECT id, name FROM provinces ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC)); exit;
    }

    public function getDistricts() {
        header('Content-Type: application/json');
        $stmt = $this->db->prepare("SELECT id, name FROM districts WHERE province_id = ? ORDER BY name ASC");
        $stmt->execute([$_GET['province_id'] ?? 0]); echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); exit;
    }

    public function getWards() {
        header('Content-Type: application/json');
        $stmt = $this->db->prepare("SELECT id, name FROM wards WHERE district_id = ? ORDER BY name ASC");
        $stmt->execute([$_GET['district_id'] ?? 0]); echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); exit;
    }

    public function saveAddressSessionAjax() {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input) {
            $_SESSION['temp_address'] = ['street_address' => $input['streetAddress'], 'province' => $input['province'], 'district' => $input['district'], 'ward' => $input['ward'], 'street' => $input['street']];
            echo json_encode(['status' => 'success']);
        } else { echo json_encode(['status' => 'error', 'msg' => 'Dữ liệu không hợp lệ']); }
        exit;
    }

    // =============================================================
    // TIẾN HÀNH TẠO ĐƠN HÀNG (ĐÃ FIX TÁCH ĐƠN THEO TỪNG SELLER)
    // =============================================================
    public function processOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $checkoutItems = [];
            $cartId = $this->cartModel->getCartId($userId);
            
            // Xử lý lấy item
            if (!empty($_POST['direct_listing_id']) && !empty($_POST['direct_quantity'])) {
                $listingId = (int)$_POST['direct_listing_id'];
                $quantity = (int)$_POST['direct_quantity'];
                
                $stmt = $this->db->prepare("SELECT id as listing_id, price as price_snapshot, user_id as seller_id FROM product_listings WHERE id = :listing_id LIMIT 1");
                $stmt->execute([':listing_id' => $listingId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $validDeal = $this->cartModel->checkValidDeal($userId, $listingId);
                    if ($validDeal) {
                        if ($quantity != $validDeal['quantity']) {
                            header("Location: index.php?controller=cart"); exit;
                        }
                        $product['price_snapshot'] = $validDeal['proposed_price'];
                    }
                    $checkoutItems[] = [
                        'listing_id' => $product['listing_id'], 'quantity' => $quantity,
                        'unit_price' => $product['price_snapshot'], 'seller_id' => $product['seller_id']
                    ];
                }
            } else {
                $this->cartModel->cleanExpiredDealsInCart($cartId);
                $rawCartItems = $this->cartModel->getCartItems($cartId);
                
                if (empty($_SESSION['checkout_selected_ids'])) { header("Location: index.php?controller=cart"); exit; }
                $idArray = explode(',', $_SESSION['checkout_selected_ids']);
                $rawCartItems = array_filter($rawCartItems, function($item) use ($idArray) { return in_array($item['listing_id'], $idArray); });

                foreach ($rawCartItems as $item) {
                    $validDeal = $this->cartModel->checkValidDeal($userId, $item['listing_id']);
                    if ($validDeal && isset($item['offer_id']) && $item['offer_id'] == $validDeal['id']) {
                        if ((int)$item['quantity'] != (int)$validDeal['quantity']) { header("Location: index.php?controller=cart"); exit; }
                    }
                    $checkoutItems[] = [
                        'listing_id' => $item['listing_id'], 'quantity' => $item['quantity'],
                        'unit_price' => $item['price_snapshot'], 'seller_id' => $item['seller_id'] ?? 1
                    ];
                }
            }

            if (empty($checkoutItems)) { header("Location: index.php?controller=cart"); exit; }

            // Lấy voucher
            $voucherCode = $_POST['voucherCodeInput'] ?? '';
            $discountAmount = (float)($_POST['voucherDiscountInput'] ?? 0);
            $dbVoucherId = null;
            if (!empty($voucherCode)) {
                $vStmt = $this->db->prepare("SELECT id FROM vouchers WHERE code = :code LIMIT 1");
                $vStmt->execute([':code' => $voucherCode]);
                $vRes = $vStmt->fetch(PDO::FETCH_ASSOC);
                if ($vRes) { $dbVoucherId = $vRes['id']; }
            }

            // Tính tiền ship cho từng Shop
            $streetAddress = $_POST['streetAddress'] ?? '';
            $isHcm = (strpos(strtolower($streetAddress), 'hồ chí minh') !== false || strpos(strtolower($streetAddress), 'hcm') !== false);
            $shippingFeePerShop = $isHcm ? 30000 : 50000;
            $paymentMethod = (int)($_POST['paymentMethod'] ?? 1); // 1 = COD, 2 = VNPay

            // ==========================================================
            // LOGIC MỚI: TÁCH ITEMS THÀNH TỪNG NHÓM THEO SELLER
            // ==========================================================
            $groupedItems = [];
            foreach ($checkoutItems as $item) {
                $groupedItems[$item['seller_id']][] = $item;
            }

            $orderIdsCreated = [];
            $totalAmountForAllOrders = 0;
            $isFirstOrder = true; // Chỉ áp dụng Voucher cho đơn hàng đầu tiên

            foreach ($groupedItems as $sellerId => $itemsByShop) {
                
                // 1. Tính tổng tiền hàng cho Shop này
                $shopSubtotal = 0;
                foreach ($itemsByShop as $i) {
                    $shopSubtotal += ($i['unit_price'] * $i['quantity']);
                }

                // 2. Tính tiền giảm giá Voucher (chỉ trừ vào 1 Shop đầu tiên)
                $shopDiscount = 0;
                $shopVoucherId = null;
                if ($isFirstOrder && !empty($dbVoucherId)) {
                    $shopDiscount = $discountAmount;
                    $shopVoucherId = $dbVoucherId;
                    $isFirstOrder = false;
                }

                // 3. Tổng tiền cuối cùng của Đơn hàng Shop này (Tiền hàng + Ship - Giảm giá)
                $shopTotalFinal = $shopSubtotal + $shippingFeePerShop - $shopDiscount;
                if ($shopTotalFinal < 0) $shopTotalFinal = 0;
                
                $totalAmountForAllOrders += $shopTotalFinal;

                // 4. Tạo Data Order cho riêng Seller này
                $orderData = [
                    'buyer_id'          => $userId,
                    'seller_id'         => $sellerId, 
                    'ward_id'           => 1, 
                    'street_address'    => $streetAddress,
                    'payment_method_id' => $paymentMethod,
                    'total_amount'      => $shopTotalFinal,
                    'discount_amount'   => $shopDiscount,
                    'voucher_id'        => $shopVoucherId,
                    'shipping_note'     => $_POST['shippingNote'] ?? '',
                    'items'             => $itemsByShop // Các sản phẩm thuộc về shop này
                ];

                // 5. Lưu vào Database
                $orderId = $this->orderModel->placeOrder($orderData);
                if ($orderId) {
                    $orderIdsCreated[] = $orderId;
                }
            }

            // ==========================================================
            // SAU KHI TẠO ĐƠN THÀNH CÔNG CHO TẤT CẢ CÁC SHOP
            // ==========================================================
            if (!empty($orderIdsCreated)) {
                
                // Trừ lượt dùng Voucher (1 lần)
                if (!empty($dbVoucherId)) {
                    require_once __DIR__ . '/../model/VoucherModel.php';
                    $voucherModel = new VoucherModel();
                    $voucherModel->incrementUsedQuantity($dbVoucherId);
                }

                // Xóa giỏ hàng
                if (empty($_POST['direct_listing_id'])) {
                    foreach ($checkoutItems as $item) {
                        $this->cartModel->removeItem($cartId, $item['listing_id']);
                    }
                }
                
                // THANH TOÁN VNPAY HOẶC COD
                if ($paymentMethod === 2) {
                    $orderIdsString = implode('-', $orderIdsCreated);
                    $this->vnpayPayment($orderIdsString, $totalAmountForAllOrders);
                } else {
                    echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background-color: #f5f5f5;'><script>document.addEventListener('DOMContentLoaded', function() {Swal.fire({icon: 'success',title: 'Đặt hàng thành công!',text: 'Đã tách thành " . count($orderIdsCreated) . " đơn hàng riêng biệt.',confirmButtonColor: '#FF7A3D',confirmButtonText: 'Xem đơn hàng',allowOutsideClick: false}).then((result) => {window.location.href = 'index.php?controller=order';});});</script></body></html>";
                }
                exit;
            } else {
                echo "<script>alert('Lỗi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }

    private function vnpayPayment($orderIdsString, $amount) {
        date_default_timezone_set('Asia/Ho_Chi_Minh'); 
        $vnp_TmnCode = "FRCAG0XC"; 
        $vnp_HashSecret = "2L1YRFYMFU0MKYMVJGTH1VJHKSGC97DK"; 
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $vnp_Returnurl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?controller=checkout&action=vnpayReturn"; 

        $vnp_TxnRef = $orderIdsString . "_" . time(); 
        $vnp_OrderInfo = "Thanh toan don hang gom: " . $orderIdsString;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amount * 100; 
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $startTime = date("YmdHis");
        $expireTime = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0", "vnp_TmnCode" => $vnp_TmnCode, "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay", "vnp_CreateDate" => $startTime, "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr, "vnp_Locale" => $vnp_Locale, "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType, "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef, "vnp_ExpireDate" => $expireTime 
        );

        ksort($inputData);
        $query = ""; $i = 0; $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) { $hashdata .= '&' . urlencode($key) . "=" . urlencode($value); } else { $hashdata .= urlencode($key) . "=" . urlencode($value); $i = 1; }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        header('Location: ' . $vnp_Url); exit;
    }

    public function vnpayReturn() {
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        
        if ($vnp_ResponseCode === '00') {
            $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
            
            $parts = explode('_', $vnp_TxnRef);
            $orderIdsString = $parts[0];
            $orderIdArray = explode('-', $orderIdsString);

            foreach ($orderIdArray as $oid) {
                $orderId = (int)$oid;
                if ($orderId > 0) {
                    $stmt = $this->db->prepare("UPDATE orders SET status_id = 2 WHERE id = :order_id");
                    $stmt->execute([':order_id' => $orderId]);
                }
            }

            $_SESSION['toast_msg'] = 'Thanh toán ' . count($orderIdArray) . ' đơn hàng qua VNPay thành công!';
            header("Location: index.php?controller=order"); exit;
        } else {
            $_SESSION['toast_msg'] = 'Giao dịch thanh toán thất bại hoặc bị hủy!';
            header("Location: index.php?controller=cart"); exit;
        }
    }
}
?>