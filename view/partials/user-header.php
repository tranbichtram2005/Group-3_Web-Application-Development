<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);

// Hứng dữ liệu từ index.php truyền sang thông qua biến toàn cục GLOBALS
$cartCount = $GLOBALS['cartCount'] ?? 0;
$notiCount = $GLOBALS['notiCount'] ?? 0;
$msgCount  = $GLOBALS['msgCount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Life Marketplace</title>
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
                <form action="index.php" method="GET" class="d-flex align-items-center bg-white rounded-pill px-3 border custom-search-bar" style="height: 38px; max-width: 480px; margin: 0 auto;">
                    <input type="hidden" name="controller" value="product">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="keyword" class="form-control border-0 shadow-none bg-transparent p-0" placeholder="Tìm kiếm đồ cũ giá hời..." style="font-size: 14px;">
                    <button type="submit" class="border-0 bg-transparent text-secondary p-0 ms-2"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="col-8 col-md-5 order-2 order-md-3 d-flex justify-content-end align-items-center gap-3 gap-md-4">
                
                <?php if ($isLoggedIn): ?>
                    <a href="index.php?controller=listing&action=create" class="btn btn-2life-primary d-none d-sm-block rounded-pill py-1 px-3 fw-bold" style="font-size: 13px;">
                        <i class="bi bi-plus-circle me-1"></i> Đăng tin
                    </a>

                    <a href="index.php?controller=cart" class="position-relative text-white text-decoration-none nav-icon-hover" title="Giỏ hàng">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>

                    <a href="#" class="position-relative text-white text-decoration-none nav-icon-hover" title="Tin nhắn">
                        <i class="bi bi-chat-dots fs-5"></i>
                        <?php if ($msgCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;"><?= $msgCount ?></span>
                        <?php endif; ?>
                    </a>

                    <a href="#" class="position-relative text-white text-decoration-none nav-icon-hover" title="Thông báo">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($notiCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;"><?= $notiCount ?></span>
                        <?php endif; ?>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-1 nav-icon-hover dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-lg-inline fw-semibold" style="font-size: 14px;">Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Thành viên') ?></span>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 250px; background: #fff;">
                            <li>
    <a class="dropdown-item" href="index.php?controller=order&action=index">
        <i class="bi bi-bag-check me-2"></i>Đơn mua của tôi
    </a>
</li>
                            <li>
                                <a href="index.php?controller=manageorderseller&action=index" class="nav-dropdown-item dropdown-item"><i class="bi bi-receipt"></i> Đơn bán của tôi</a>
                            </li>
                            <li><a class="dropdown-item" href="index.php?controller=dashboard"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Dashboard</a></li>
                            <li>
                                <a href="index.php?controller=manage_listing&action=index" class="nav-dropdown-item dropdown-item"><i class="bi bi-card-list"></i> Quản lý tin đăng</a>
                            </li>
                            <li>
                                <a href="index.php?controller=profile" class="nav-dropdown-item dropdown-item"><i class="bi bi-person-gear"></i> Tài khoản của tôi</a>
                            </li>
                            
                            <li><hr class="dropdown-divider my-1 mx-2 text-secondary opacity-25"></li>
                            
                            <li>
                                <a href="index.php?controller=auth&action=logout" class="nav-dropdown-item dropdown-item fw-bold" style="color: #dc3545 !important;">
                                    <i class="bi bi-box-arrow-right" style="color: #dc3545 !important;"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="btn-2life-outline text-white border-white rounded-pill px-3 py-1 text-decoration-none" style="font-size: 13px;">Đăng nhập</a>
                    <a href="index.php?controller=auth&action=register" class="btn btn-2life-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 13px;">Đăng ký</a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</header>