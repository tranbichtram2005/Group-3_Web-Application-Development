<?php
session_start();
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $db = (new Database())->getConnection();
    $orderModel = new OrderModel($db);

    // Giả sử dữ liệu giỏ hàng được lưu trong session hoặc gửi từ form
    // Trong thực tế, bạn nên lấy lại từ DB/Session để tránh người dùng sửa giá ở Frontend
    $items = $_SESSION['cart_items']; 
    
    $orderData = [
        'buyer_id'          => $_SESSION['user_id'],
        'seller_id'         => $items[0]['seller_id'], // Giả định 1 đơn hàng/1 shop
        'ward_id'           => $_POST['ward_id'],
        'street_address'    => $_POST['street_address'],
        'payment_method_id' => $_POST['payment_method'], // Ví dụ: 1-COD, 2-VNPay... [cite: 83]
        'total_amount'      => $_POST['total_final'],
        'shipping_note'     => $_POST['note'],
        'items'             => $items
    ];

    $orderId = $orderModel->placeOrder($orderData);

    if ($orderId) {
        // Nếu chọn thanh toán online (VNPay/MoMo), chuyển hướng sang trang thanh toán
        if ($orderData['payment_method_id'] != 1) { // 1 là COD [cite: 83]
            header("Location: payment_gateway.php?order_id=" . $orderId);
        } else {
            // Nếu COD thì về trang thành công luôn
            unset($_SESSION['cart_items']); // Xóa giỏ hàng sau khi đặt xong
            header("Location: ../view/buyer/order_success.php?id=" . $orderId);
        }
        exit;
    } else {
        echo "Có lỗi xảy ra khi đặt hàng!";
    }
}