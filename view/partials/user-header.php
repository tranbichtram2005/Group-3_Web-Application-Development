<?php
// Đảm bảo session đã được khởi tạo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Life Marketplace - Chợ đồ cũ sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
</head>
<body>

<header class="navbar-2life py-2">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center g-2">
            
            <div class="col-4 col-md-2">
                <a href="index.php?controller=home" class="logo text-decoration-none">2Life</a>
            </div>

            <div class="col-12 col-md-5 order-3 order-md-2 mt-2 mt-md-0">
                <form action="index.php" method="GET" class="d-flex align-items-center bg-white rounded-pill px-2 py-1 border">
                    <input type="hidden" name="controller" value="product">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="keyword" class="form-control border-0 shadow-none bg-transparent px-3" placeholder="Tìm kiếm đồ cũ giá hời..." style="font-size: 14px;">
                    <button type="submit" class="btn btn-2life-primary rounded-pill px-3 py-1"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="col-8 col-md-5 order-2 order-md-3 d-flex justify-content-end align-items-center gap-3 gap-md-4">
                
                <?php if ($isLoggedIn): ?>
                    <a href="index.php?controller=listing&action=create" class="btn btn-2life-primary d-none d-sm-block rounded-pill py-1 px-3 fw-bold" style="font-size: 13px;">
                        <i class="bi bi-plus-circle me-1"></i> Đăng tin
                    </a>

                    <a href="#" class="position-relative text-white text-decoration-none nav-icon-hover" title="Thông báo">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 5px;">5</span>
                    </a>

                    <a href="#" class="position-relative text-white text-decoration-none nav-icon-hover" title="Tin nhắn">
                        <i class="bi bi-chat-dots fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 5px;">2</span>
                    </a>

                    <a href="index.php?controller=cart" class="position-relative text-white text-decoration-none nav-icon-hover" title="Giỏ hàng">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 5px;">3</span>
                    </a>

                    <div class="nav-dropdown position-relative">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-1 nav-icon-hover">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-lg-inline fw-semibold ms-1 text-truncate" style="max-width: 120px; font-size: 14px;">
                                <?= htmlspecialchars($_SESSION['username'] ?? 'Tài khoản') ?>
                            </span>
                        </a>
                        
                        <div class="nav-dropdown-menu">
                            <div class="dropdown-group-title">Người mua</div>
                            <a href="#" class="nav-dropdown-item"><i class="bi bi-bag-check"></i> Đơn mua của tôi</a>
                            
                            <div class="dropdown-group-title border-top mt-1">Người bán</div>
                            <a href="#" class="nav-dropdown-item"><i class="bi bi-card-list"></i> Quản lý tin đăng</a>
                            <a href="#" class="nav-dropdown-item"><i class="bi bi-receipt"></i> Đơn bán của tôi</a>
                            <a href="#" class="nav-dropdown-item"><i class="bi bi-bar-chart"></i> Báo cáo & Thống kê</a>
                            
                            <div class="dropdown-group-title border-top mt-1">Cá nhân</div>
                            <a href="#" class="nav-dropdown-item"><i class="bi bi-person-gear"></i> Tài khoản của tôi</a>
                            <a href="index.php?controller=auth&action=logout" class="nav-dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right text-danger"></i> Đăng xuất
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="btn-2life-outline text-white border-white rounded-pill px-3 py-1 text-decoration-none" style="font-size: 13px;">Đăng nhập</a>
                    <a href="index.php?controller=auth&action=register" class="btn btn-2life-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 13px;">Đăng ký</a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</header>