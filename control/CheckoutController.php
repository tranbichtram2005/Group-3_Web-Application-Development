<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/VoucherModel.php';
require_once __DIR__ . '/../model/ListingModel.php';

class CheckoutController {

    private $db;
    private $orderModel;
    private $cartModel;
    private $voucherModel;
    private $listingModel;

    public function __construct() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();

        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel();
        $this->voucherModel = new VoucherModel($this->db);
        $this->listingModel = new ListingModel();
    }

    // =========================
    // CHECKOUT PAGE
    // =========================
    public function index() {

        $userId = $_SESSION['user_id'];

        $selectedIds = [];
        $rawCartItems = [];

        // TÁCH LUỒNG 1: MUA NGAY TỪ TRANG CHI TIẾT
        if (isset($_GET['buy_now_id'])) {
            $productId = (int)$_GET['buy_now_id'];
            $product = $this->listingModel->getListingDetail($productId);
            
            if ($product) {
                $rawCartItems = [[
                    'listing_id' => $product['id'],
                    'title' => $product['title'],
                    'image_url' => $product['image_url'] ?? '',
                    'seller_id' => $product['user_id'],
                    'seller_name' => $product['username'],
                    'quantity' => 1,
                    'price_snapshot' => $product['price'],
                    'stock_quantity' => $product['stock_quantity']
                ]];
                $selectedIds = [$product['id']];
                $_SESSION['buy_now_item'] = $rawCartItems; 
            } else {
                header("Location: index.php?controller=home");
                exit;
            }
        } 
        // TÁCH LUỒNG 2: MUA TỪ GIỎ HÀNG (Code cũ của bạn cậu)
        else {
            unset($_SESSION['buy_now_item']); 
            if (!empty($_POST['selected_ids'])) {
                $selectedIds = array_map('intval', explode(',', $_POST['selected_ids']));
            } elseif (!empty($_SESSION['checkout_selected_ids'])) {
                $selectedIds = $_SESSION['checkout_selected_ids'];
            }
            $cartId = $this->cartModel->getCartId($userId);
            $rawCartItems = $this->cartModel->getCartItems($cartId);
            if (!empty($selectedIds)) {
                $rawCartItems = array_filter($rawCartItems, function ($item) use ($selectedIds) {
                    return in_array($item['listing_id'], $selectedIds);
                });
            }
        }

        if (empty($rawCartItems)) {
            header("Location: index.php?controller=cart");
            exit;
        }

        // Save selected ids into session
        $selectedIds = array_column(array_values($rawCartItems), 'listing_id');
        $_SESSION['checkout_selected_ids'] = $selectedIds;

        $checkoutItems = [];

        foreach ($rawCartItems as $item) {

            $checkoutItems[] = [
                'listing_id'   => $item['listing_id'],
                'product_name' => $item['title'],
                'image'        => $item['image_url'] ?? '',
                'seller_id'    => $item['seller_id'] ?? 1,
                'seller_name'  => $item['seller_name'] ?? '2Life Seller',
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['price_snapshot'],
                'stock'        => $item['stock_quantity']
            ];
        }

        // User info
        $defaultAddr = $this->cartModel->getDefaultAddress($userId);
        $addressId = $defaultAddr['id'] ?? null;

        $buyerName     = $defaultAddr['full_name'] ?? ($_SESSION['full_name'] ?? 'Người mua');
        $buyerPhone    = $defaultAddr['phone'] ?? ($_SESSION['phone'] ?? '');
        $buyerProvince = $defaultAddr['province'] ?? '';
        $buyerDistrict = $defaultAddr['district'] ?? '';
        $buyerWard     = $defaultAddr['ward'] ?? '';
        $buyerStreet   = $defaultAddr['street'] ?? '';

        $fullAddress = trim(implode(', ', array_filter([
            $buyerStreet,
            $buyerWard,
            $buyerDistrict,
            $buyerProvince
        ])));

        require_once __DIR__ . '/../view/checkout.php';
    }

    // =========================
    // GET USER ADDRESSES
    // =========================
    public function getAddresses() {

        header('Content-Type: application/json; charset=utf-8');

        $addresses = $this->cartModel->getUserAddresses($_SESSION['user_id']);

        echo json_encode([
            'status' => 'success',
            'addresses' => $addresses
        ]);
    }

    // =========================
    // ADD NEW ADDRESS
    // =========================
    public function addAddress() {

        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents("php://input"), true);

        $fullName = trim($input['full_name'] ?? '');
        $phone    = trim($input['phone'] ?? '');
        $province = trim($input['province'] ?? '');
        $district = trim($input['district'] ?? '');
        $ward     = trim($input['ward'] ?? '');
        $street   = trim($input['street'] ?? '');

        if (
            !$fullName ||
            !$phone ||
            !$province ||
            !$district ||
            !$ward ||
            !$street
        ) {

            echo json_encode([
                'status' => 'error',
                'msg' => 'Vui lòng nhập đầy đủ thông tin.'
            ]);

            return;
        }

        $id = $this->cartModel->addUserAddress(
            $_SESSION['user_id'],
            $fullName,
            $phone,
            $province,
            $district,
            $ward,
            $street
        );

        if ($id) {

            echo json_encode([
                'status' => 'success',
                'msg' => 'Thêm địa chỉ thành công!',
                'id' => $id
            ]);

        } else {

            echo json_encode([
                'status' => 'error',
                'msg' => 'Không thể thêm địa chỉ.'
            ]);
        }
    }

    // =========================
    // UPDATE QTY AJAX
    // =========================
    public function updateQtyAjax() {

        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents("php://input"), true);

        $listingId = intval($input['listingId'] ?? 0);
        $newQty    = intval($input['quantity'] ?? 1);

        $userId = $_SESSION['user_id'];

        $stock = $this->cartModel->getStock($listingId);

        if ($newQty > $stock) {

            echo json_encode([
                'status' => 'error',
                'msg' => "Chỉ còn $stock sản phẩm trong kho!",
                'maxQty' => $stock
            ]);

            return;
        }

        if ($newQty < 1) {

            echo json_encode([
                'status' => 'error',
                'msg' => 'Số lượng tối thiểu là 1.'
            ]);

            return;
        }

        $cartId = $this->cartModel->getCartId($userId);

        $this->cartModel->updateQuantity(
            $cartId,
            $listingId,
            $newQty
        );

        echo json_encode([
            'status' => 'success',
            'qty' => $newQty
        ]);
    }

    // =========================
    // PROCESS ORDER
    // =========================
    public function processOrder() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=cart");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $rawCartItems = [];
        $cartId = $this->cartModel->getCartId($userId);

        // TÁCH LUỒNG 1: ĐANG CHỐT ĐƠN CHO "MUA NGAY"
        if (!empty($_SESSION['buy_now_item'])) {
            $rawCartItems = $_SESSION['buy_now_item'];
        } 
        // TÁCH LUỒNG 2: CHỐT ĐƠN TỪ GIỎ HÀNG (Code cũ)
        else {
            $selectedIds = $_SESSION['checkout_selected_ids'] ?? [];
            $rawCartItems = $this->cartModel->getCartItems($cartId);
            if (!empty($selectedIds)) {
                $rawCartItems = array_filter($rawCartItems, function ($item) use ($selectedIds) {
                    return in_array($item['listing_id'], $selectedIds);
                });
            }
        }

        if (empty($rawCartItems)) {

            echo "<script>
                alert('Không có sản phẩm để đặt hàng!');
                window.location.href='index.php?controller=cart';
            </script>";

            exit;
        }

        // Check stock
        foreach ($rawCartItems as $item) {

            $stock = $this->cartModel->getStock($item['listing_id']);

            if ($item['quantity'] > $stock) {

                echo "<script>
                    alert('Sản phẩm chỉ còn $stock trong kho!');
                    history.back();
                </script>";

                exit;
            }
        }

        // Build checkout items
        $checkoutItems = [];

        $subtotal = 0;
        $firstSellerId = null;

        foreach ($rawCartItems as $item) {

            if (!$firstSellerId) {
                $firstSellerId = $item['seller_id'] ?? 1;
            }

            $price = $item['price_snapshot'];
            $qty   = $item['quantity'];

            $subtotal += ($price * $qty);

            $checkoutItems[] = [
                'listing_id' => $item['listing_id'],
                'quantity'   => $qty,
                'unit_price' => $price,
                'seller_id'  => $item['seller_id'] ?? 1
            ];
        }

        // =========================
        // APPLY VOUCHER
        // =========================
        $voucherId   = null;
        $discountAmt = 0;

        $voucherCode = trim($_POST['voucherCode'] ?? '');

        if ($voucherCode) {

            $alreadyUsed = $this->voucherModel
                ->isVoucherUsedByUser($voucherCode, $userId);

            if (!$alreadyUsed) {

                $voucherData = $this->voucherModel
                    ->getVoucherByCode($voucherCode, $subtotal);

                if (!isset($voucherData['error'])) {

                    $voucherId = $voucherData['id'];

                    $discountAmt = min(
                        (int)$voucherData['discount_value'],
                        $subtotal
                    );
                }
            }
        }

        // Backend calculate total
        $totalFinal = max(0, $subtotal - $discountAmt);

        $paymentMethod = intval($_POST['paymentMethod'] ?? 1);

        $streetAddress = trim($_POST['streetAddress'] ?? '');

        $shippingNote = trim($_POST['shippingNote'] ?? '');

        $orderData = [

            'buyer_id' => $userId,

            'seller_id' => $firstSellerId ?? 1,

            'voucher_id' => $voucherId,

            'ward_id' => 1,

            'street_address' => $streetAddress,

            'payment_method_id' => $paymentMethod,

            'total_amount' => $totalFinal,

            'discount_amount' => $discountAmt,

            'shipping_note' => $shippingNote,

            'items' => $checkoutItems
        ];

        $orderId = $this->orderModel->placeOrder($orderData);

        if ($orderId) {

           // Remove purchased items (CHỈ XÓA NẾU KHÔNG PHẢI MUA NGAY)
            if (empty($_SESSION['buy_now_item'])) {
                foreach ($checkoutItems as $item) {
                    $this->cartModel->removeItem(
                        $cartId,
                        $item['listing_id']
                    );
                }
            }

            unset($_SESSION['checkout_selected_ids']);
            unset($_SESSION['buy_now_item']); // Xóa rác session mua ngay

            // =========================
            // PAYMENT FLOW
            // =========================

            // COD
            if ($paymentMethod == 1) {

                $this->orderModel->updateOrderStatus(
                    $orderId,
                    'pending'
                );

                header("Location: index.php?controller=checkout&action=success&order_id=$orderId");

            }
            // VNPay
            elseif ($paymentMethod == 2) {

                header("Location: index.php?controller=checkout&action=vnpay&order_id=$orderId&amount=$totalFinal");

            }
            // MoMo
            elseif ($paymentMethod == 3) {

                $momoUrl = $this->createMoMoUrl(
                    $orderId,
                    $totalFinal
                );

                header("Location: " . $momoUrl);
            }

            exit;

        } else {

            echo "<script>
                alert('Lỗi tạo đơn hàng!');
                history.back();
            </script>";
        }
    }

    // =========================
    // ORDER SUCCESS
    // =========================
    public function success() {

        $orderId = intval($_GET['order_id'] ?? 0);

        require_once __DIR__ . '/../view/order_success.php';
    }

    // =========================
    // VNPAY
    // =========================
    public function vnpay() {

        $orderId = intval($_GET['order_id'] ?? 0);
        $amount  = intval($_GET['amount'] ?? 0);

        $vnp_TmnCode = "FRCAG0XC";
        $vnp_HashSecret = "2L1YRFYMFU0MKYMVJGTH1VJHKSGC97DK";

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        $vnp_Returnurl =
            (isset($_SERVER['HTTPS']) ? 'https' : 'http')
            . "://$_SERVER[HTTP_HOST]"
            . dirname($_SERVER['REQUEST_URI'])
            . "/index.php?controller=checkout&action=vnpayReturn";

        $vnp_TxnRef = $orderId . '_' . time();

        $inputData = [

            "vnp_Version" => "2.1.0",

            "vnp_TmnCode" => $vnp_TmnCode,

            "vnp_Amount" => $amount * 100,

            "vnp_Command" => "pay",

            "vnp_CreateDate" => date('YmdHis'),

            "vnp_CurrCode" => "VND",

            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],

            "vnp_Locale" => "vn",

            "vnp_OrderInfo" => "Thanh toan don hang #$orderId",

            "vnp_OrderType" => "billpayment",

            "vnp_ReturnUrl" => $vnp_Returnurl,

            "vnp_TxnRef" => $vnp_TxnRef
        ];

        ksort($inputData);

        $query = http_build_query($inputData);

        $vnpSecureHash = hash_hmac(
            'sha512',
            $query,
            $vnp_HashSecret
        );

        $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

        $_SESSION['vnp_OrderId'] = $orderId;

        header("Location: $vnp_Url");

        exit;
    }

    // =========================
    // VNPAY RETURN
    // =========================
    public function vnpayReturn() {

        if (
            isset($_GET['vnp_ResponseCode']) &&
            $_GET['vnp_ResponseCode'] == '00'
        ) {

            $orderId = $_SESSION['vnp_OrderId'] ?? 0;

            $txnRef = $_GET['vnp_TxnRef'] ?? '';

            $this->orderModel->markAsPaid(
                $orderId,
                $txnRef
            );

            $this->orderModel->updateOrderStatus(
                $orderId,
                'confirmed'
            );

            unset($_SESSION['vnp_OrderId']);

            header("Location: index.php?controller=checkout&action=success&order_id=$orderId");

        } else {

            header("Location: index.php?controller=checkout&action=paymentFailed");
        }

        exit;
    }

    // =========================
    // MOMO API
    // =========================
    private function createMoMoUrl($orderId, $amount) {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = "MOMOBKUN20180529";

        $accessKey = "klm05TvNCjpQcgSy";

        $secretKey = "at67qH6mk8w5Y1nAwMovdPTlcjTA21k";

        $orderInfo = "Thanh toan don hang 2Life ma " . $orderId;

        $amount = (string)$amount;

        $requestId = time() . "";

        $momoOrderId = $orderId . "_" . time();

        $redirectUrl =
            "http://localhost/Group-3_Web-Application-Development/index.php?controller=checkout&action=momoReturn";

        $ipnUrl = $redirectUrl;

        $extraData = "";

        $requestType = "captureWallet";

        $rawHash =
            "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $momoOrderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac(
            "sha256",
            $rawHash,
            $secretKey
        );

        $data = [

            'partnerCode' => $partnerCode,

            'partnerName' => "2Life Store",

            'storeId' => "2Life",

            'requestId' => $requestId,

            'amount' => $amount,

            'orderId' => $momoOrderId,

            'orderInfo' => $orderInfo,

            'redirectUrl' => $redirectUrl,

            'ipnUrl' => $ipnUrl,

            'lang' => 'vi',

            'extraData' => $extraData,

            'requestType' => $requestType,

            'signature' => $signature
        ];

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);

        $result = curl_exec($ch);

        curl_close($ch);

        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            return $jsonResult['payUrl'];
        }

        return "index.php?controller=cart";
    }

    // =========================
    // MOMO RETURN
    // =========================
    public function momoReturn() {

        if (
            isset($_GET['resultCode']) &&
            $_GET['resultCode'] == '0'
        ) {

            echo "<script>
                alert('Thanh toán MoMo thành công!');
                window.location.href='index.php?controller=home';
            </script>";

        } else {

            echo "<script>
                alert('Thanh toán MoMo thất bại!');
                window.location.href='index.php?controller=cart';
            </script>";
        }
    }

    // =========================
    // PAYMENT FAILED
    // =========================
    public function paymentFailed() {

        require_once __DIR__ . '/../view/payment_failed.php';
    }
}
?>