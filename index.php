<?php
session_start();

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if ($controller == 'cart') {
    require_once __DIR__ . '/control/CartController.php';
    $cartController = new CartController();
    if (method_exists($cartController, $action)) {
        $cartController->$action();
    } else {
        die("Lỗi: Không tìm thấy chức năng này!");
    }
} elseif ($controller == 'auth') {
    require_once __DIR__ . '/control/AuthController.php';
    $authController = new AuthController();
    // Tự động gọi hàm trùng tên với biến $action trên URL
    if (method_exists($authController, $action)) {
        $authController->$action();
    } else {
        die("Lỗi: Không tìm thấy hành động xác thực này!");
    }
} elseif ($controller == 'home') {
    require_once __DIR__ . '/view/app/home.php';
} 
// =========================================================
// NHÁNH XỬ LÝ CHO TIN ĐĂNG (LISTING) 
// =========================================================
elseif ($controller == 'listing') {
    require_once __DIR__ . '/control/ListingController.php';
    $listingController = new ListingController();
    
    if (method_exists($listingController, $action)) {
        $listingController->$action(); // Gọi hàm create()
    } else {
        die("Lỗi: Không tìm thấy chức năng này trong Listing!");
    }
} 
// =========================================================
elseif ($controller == 'home') {
    require_once __DIR__ . '/view/app/home.php';
}
elseif ($controller == 'manage_listing') {
    require_once __DIR__ . '/control/ManageListingController.php';
    $manageController = new ManageListingController();
    
    if (method_exists($manageController, $action)) {
        $manageController->$action(); // Gọi hàm index() hoặc changeStatus()
    } else {
        die("Lỗi: Không tìm thấy chức năng này trong trang quản lý!");
    }
} 
// =========================================================
elseif ($controller == 'home') {
    require_once __DIR__ . '/view/app/home.php';
}
// =========================================================
// PHÂN LUỒNG: ADMIN DUYỆT NGƯỜI BÁN (APPROVESELLER)
// =========================================================
elseif ($controller == 'approveseller') {
    require_once __DIR__ . '/control/ApproveSellerController.php';
    $approveSellerCtrl = new ApproveSellerController();
    if (method_exists($approveSellerCtrl, $action)) {
        $approveSellerCtrl->$action(); 
    } else {
        die("Lỗi: Không tìm thấy chức năng này trong hệ thống Admin!");
    }
} 
else {
    echo "<h1 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang!</h1>";
}

?>