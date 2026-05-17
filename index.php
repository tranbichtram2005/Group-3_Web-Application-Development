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
} else {
    echo "<h1 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang!</h1>";
}
?>