<?php
// Không cần session ở đây vì đã redirect qua
$orderId = $orderId ?? ($_GET['order_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hàng Thành Công - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --btn-primary: #FF7A3D; }
        body { background: #f5f5f5; font-family: 'Inter', sans-serif; }
        .success-icon { font-size: 5rem; color: #43a047; animation: pop 0.5s ease; }
        @keyframes pop { 0%{transform:scale(0)} 80%{transform:scale(1.1)} 100%{transform:scale(1)} }
    </style>
</head>
<body>
<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container">
        <h2 class="mb-0 fw-bold" style="color: var(--btn-primary);">2Life</h2>
    </div>
</header>

<main class="container py-5">
    <div class="bg-white rounded-3 shadow-sm p-5 text-center mx-auto" style="max-width: 520px;">
        <div class="success-icon mb-3"><i class="bi bi-check-circle-fill"></i></div>
        <h3 class="fw-bold text-dark mb-2">Đặt hàng thành công! 🎉</h3>
        <?php if($orderId): ?>
        <p class="text-secondary mb-1">Mã đơn hàng của bạn: <strong class="text-dark">#<?= htmlspecialchars($orderId) ?></strong></p>
        <?php endif; ?>
        <p class="text-secondary mb-4">Cảm ơn bạn đã tin tưởng mua sắm tại 2Life.<br>Đơn hàng của bạn đang được xử lý và sẽ sớm được giao đến!</p>
        
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="index.php?controller=home" class="btn fw-bold text-white px-4 py-2" style="background:var(--btn-primary);border:none;border-radius:8px;">
                <i class="bi bi-house me-1"></i>Về trang chủ
            </a>
            <a href="index.php?controller=home" class="btn btn-outline-secondary px-4 py-2" style="border-radius:8px;">
                <i class="bi bi-bag me-1"></i>Tiếp tục mua sắm
            </a>
        </div>
    </div>
</main>
</body>
</html>