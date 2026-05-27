<?php
session_start();

// 1. TỰ ĐỘNG LẤY SỐ LƯỢNG CHO HEADER 
$GLOBALS['cartCount'] = 0; 
$GLOBALS['notiCount'] = 0; 
$GLOBALS['msgCount']  = 0;

if (isset($_SESSION['user_id'])) {
    
    // Nạp file kết nối Database và file Model User của nhóm cậu
    require_once __DIR__ . '/model/Database.php'; 
    require_once __DIR__ . '/model/User.php';     

    try {
        // 1. Khởi tạo đối tượng Database và lấy kết nối PDO
        $database = new Database();
        $pdo = $database->getConnection();
        
        // 2. Chỉ tiếp tục nếu kết nối DB thành công (không bị null)
        if ($pdo != null) {
            // Truyền kết nối PDO vào Model User
            $userModel = new User($pdo);
            $stats = $userModel->getHeaderStats($_SESSION['user_id']);
            
            // Lưu vào biến toàn cục để file user-header.php lấy được
            $GLOBALS['cartCount'] = $stats['cartCount'];
            $GLOBALS['notiCount'] = $stats['notiCount'];
            $GLOBALS['msgCount']  = $stats['msgCount'];
        }
    } catch (Exception $e) {
        // Bỏ qua lỗi kết nối (nếu có) để không làm sập trang web
    }
}

// =========================================================
// 2. FRONT CONTROLLER (ROUTER) BẰNG SWITCH-CASE
// =========================================================
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($controller) {
    case 'home':
        require_once __DIR__ . '/control/HomeController.php';
        $homeCtrl = new HomeController();
        $homeCtrl->index();
        break;

    case 'cart':
        require_once __DIR__ . '/control/CartController.php';
        $cartCtrl = new CartController();
        method_exists($cartCtrl, $action) ? $cartCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

    case 'auth':
        require_once __DIR__ . '/control/AuthController.php';
        $authCtrl = new AuthController();
        method_exists($authCtrl, $action) ? $authCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

    case 'listing':
        require_once __DIR__ . '/control/ListingController.php';
        $listingCtrl = new ListingController();
        method_exists($listingCtrl, $action) ? $listingCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

    case 'manage_listing':
        require_once __DIR__ . '/control/ManageListingController.php';
        $manageCtrl = new ManageListingController();
        method_exists($manageCtrl, $action) ? $manageCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

    case 'approveseller':
        require_once __DIR__ . '/control/ApproveSellerController.php';
        $approveSellerCtrl = new ApproveSellerController();
        method_exists($approveSellerCtrl, $action) ? $approveSellerCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

    case 'profile':
        require_once __DIR__ . '/control/ProfileController.php';
        $profileCtrl = new ProfileController();
        method_exists($profileCtrl, $action) ? $profileCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;

        case 'admin-home':
    require_once __DIR__ . '/control/AdminController.php'; // Đường dẫn tới file Controller Admin
    $adminCtrl = new AdminController();
    $adminCtrl->index();
    break;

    case 'voucher':
        require_once __DIR__ . '/control/VoucherController.php';
        $voucherCtrl = new VoucherController();
        method_exists($voucherCtrl, $action) ? $voucherCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;
    case 'checkout':
        require_once __DIR__ . '/control/CheckoutController.php';
        $checkoutCtrl = new CheckoutController();
        // Kiểm tra xem action có tồn tại không, nếu không thì mặc định chạy hàm index()
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        method_exists($checkoutCtrl, $action) ? $checkoutCtrl->$action() : die("Lỗi: Không tìm thấy action $action trong CheckoutController!");
        break;
    
    case 'approvelisting': // <-- TÍNH NĂNG DUYỆT TIN ĐĂNG MỚI ĐƯỢC BỔ SUNG
        require_once __DIR__ . '/control/ApproveListingController.php';
        $approveListingCtrl = new ApproveListingController();
        method_exists($approveListingCtrl, $action) ? $approveListingCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;
    default:
        echo "<h1 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang!</h1>";
        break;
    case 'order':
        require_once __DIR__ . '/control/OrderController.php';
        $orderCtrl = new OrderController();
        method_exists($orderCtrl, $action) ? $orderCtrl->$action() : die("Lỗi: Không tìm thấy action!");
        break;
    case 'dashboard':
            require_once 'control/DashboardController.php';
            $controllerObj = new DashboardController();
            
            // Bắt action từ URL (nếu không có thì mặc định chạy hàm index)
            $action = isset($_GET['action']) ? $_GET['action'] : 'index';
            
            // Ra lệnh cho Controller chạy hàm
            if (method_exists($controllerObj, $action)) {
                $controllerObj->$action();
            } else {
                $controllerObj->index();
            }
            break;
}
?>