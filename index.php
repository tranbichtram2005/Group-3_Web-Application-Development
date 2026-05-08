<?php
session_start();
// Biến kiểm tra trạng thái đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - 2Life Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Gọi file css (Vì index.php nằm ngoài cùng nên chỉ cần gọi 1 cấp) -->
    <link rel="stylesheet" href="layout/style.css">
</head>
<body>
    <!-- HEADER -->
    <header class="navbar-2life">
        <div class="container-fluid px-4">
            <div class="row align-items-center g-2">
                <div class="col-2"><a href="index.php" class="logo">2Life</a></div>
                <div class="col-5">
                    <div class="d-flex align-items-center" style="background:#fff;border-radius:25px;padding:4px 4px 4px 16px;">
                        <input type="text" placeholder="Tìm kiếm đồ cũ giá hời..." style="flex:1;border:none;outline:none;font-size:14px;">
                        <button class="btn-2life-primary" style="border-radius:20px;padding:7px 18px;"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="col-5 d-flex justify-content-end align-items-center gap-3">
                    <?php if ($isLoggedIn): ?>
                        <a href="view/public/cart.html" class="nav-link-text"><i class="bi bi-cart3"></i> Giỏ hàng</a>
                        <a href="view/public/post-product.html" class="btn-2life-primary" style="padding: 8px 15px;"><i class="bi bi-plus-circle"></i> Đăng tin</a>
                        <span class="text-white fw-bold ms-2"><i class="bi bi-person-circle"></i> Chào, <?= $_SESSION['full_name'] ?></span>
                        <a href="view/Auth/logout.php" class="text-danger ms-2" style="font-size:13px; text-decoration:none;">(Đăng xuất)</a>
                    <?php else: ?>
                        <!-- Nếu CHƯA đăng nhập thì hiện 2 nút này -->
                        <a href="view/Auth/login.php" class="btn-2life-outline" style="color: white; border-color: white;">Đăng nhập</a>
                        <a href="view/Auth/register.php" class="btn-2life-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <section class="hero-section text-center text-white" style="background-image: linear-gradient(rgba(31, 60, 90, 0.7), rgba(31, 60, 90, 0.7)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1000&q=80'); padding: 100px 0;">
        <h1 class="fw-bold display-5 mb-3">Tái sử dụng - Tiết kiệm - Bền vững</h1>
        <p class="fs-5 mb-4">Nền tảng trao đổi đồ cũ uy tín nhất dành cho sinh viên</p>
        <a href="#explore" class="btn-2life-primary px-4 py-2 fs-5">Khám phá ngay</a>
    </section>

    <!-- DANH SÁCH SẢN PHẨM MỚI -->
    <main id="explore" class="container py-5">
        <h3 class="fw-bold mb-4" style="color: var(--nav-color);"><i class="bi bi-stars" style="color: var(--btn-primary);"></i> Tin đăng mới nhất</h3>
        <div class="row g-4">
            <!-- Sản phẩm 1 -->
            <div class="col-md-3">
                <a href="view/public/product detail.html" class="text-decoration-none">
                    <div class="card-white overflow-hidden" style="transition: 0.3s;">
                        <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=400&q=80" style="width: 100%; height: 200px; object-fit: cover;">
                        <div class="p-3">
                            <span class="tag-2life mb-2">Thời trang</span>
                            <h6 class="text-dark fw-bold text-truncate">Áo khoác da bò Vintage</h6>
                            <div class="fs-5 fw-bold" style="color: var(--btn-primary);">550.000đ</div>
                            <small class="text-secondary"><i class="bi bi-geo-alt"></i> Hà Nội</small>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Sản phẩm 2 -->
            <div class="col-md-3">
                <div class="card-white overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&q=80" style="width: 100%; height: 200px; object-fit: cover;">
                    <div class="p-3">
                        <span class="tag-2life tag-blue mb-2">Đồ nữ</span>
                        <h6 class="text-dark fw-bold text-truncate">Áo khoác jean Unisex</h6>
                        <div class="fs-5 fw-bold" style="color: var(--btn-primary);">320.000đ</div>
                        <small class="text-secondary"><i class="bi bi-geo-alt"></i> TP. HCM</small>
                    </div>
                </div>
            </div>
            <!-- Sản phẩm 3 & 4 Cậu có thể nhân bản thêm -->
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-white" style="background-color: var(--nav-color);">
        <h5>2Life Marketplace</h5>
        <small>© 2025 - Thiết kế bởi Nhóm 3 (Phát triển ứng dụng Web)</small>
    </footer>
</body>
</html>