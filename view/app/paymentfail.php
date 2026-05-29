<?php
$orderId = $orderId ?? ($_GET['order_id'] ?? 0);
$code    = $code    ?? ($_GET['code']     ?? '');
$codeMessages = [
    '24' => 'Giao dịch bị hủy.',
    '07' => 'Giao dịch bị nghi ngờ gian lận.',
    '09' => 'Tài khoản chưa đăng ký Internet Banking.',
    '10' => 'Xác thực thông tin thẻ sai quá 3 lần.',
    '11' => 'Đã hết hạn thanh toán.',
    '12' => 'Tài khoản bị khóa.',
    '75' => 'Ngân hàng đang bảo trì.',
];
$errMsg = $codeMessages[$code] ?? 'Thanh toán thất bại. Vui lòng thử lại.';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thất Bại - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>:root { --btn-primary: #FF7A3D; } body { background: #f5f5f5; font-family: 'Inter', sans-serif; }</style>
</head>
<body>
<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container"><h2 class="mb-0 fw-bold" style="color: var(--btn-primary);">2Life</h2></div>
</header>
<main class="container py-5">
    <div class="bg-white rounded-3 shadow-sm p-5 text-center mx-auto" style="max-width: 520px;">
        <div class="mb-3" style="font-size:5rem;color:#e53935;"><i class="bi bi-x-circle-fill"></i></div>
        <h3 class="fw-bold text-dark mb-2">Thanh toán thất bại</h3>
        <p class="text-secondary mb-4"><?= htmlspecialchars($errMsg) ?></p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <?php if($orderId): ?>
            <a href="index.php?controller=checkout&action=vnpay&order_id=<?= $orderId ?>" class="btn fw-bold text-white px-4 py-2" style="background:var(--btn-primary);border:none;border-radius:8px;">
                <i class="bi bi-arrow-clockwise me-1"></i>Thử lại
            </a>
            <?php endif; ?>
            <a href="index.php?controller=cart" class="btn btn-outline-secondary px-4 py-2" style="border-radius:8px;">
                <i class="bi bi-cart3 me-1"></i>Giỏ hàng
            </a>
        </div>
    </div>
</main>
</body>
</html>