<?php
// Đảm bảo session luôn hoạt động
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Đọc trực tiếp từ $_SESSION['full_name']
$adminName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin Account';
$adminRole = 'Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : '2Life Admin Panel' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        
        /* Navbar Style */
        .admin-navbar { background-color: #0f172a; padding: 12px 0; }
        .admin-logo { font-size: 24px; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: 0.5px; padding: 0; }
        .admin-navbar .nav-link { color: #cbd5e1; font-weight: 500; font-size: 15px; padding: 8px 16px; border-radius: 6px; transition: all 0.2s; }
        .admin-navbar .nav-link:hover, .admin-navbar .nav-link:focus { color: #fff; background-color: rgba(255,255,255,0.05); }
        
        /* Dropdown Style PC */
        .dropdown-menu { border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); z-index: 1060; }
        
        .admin-footer { background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px 0; color: #64748b; font-size: 13.5px; text-align: center; margin-top: auto; }
        main { flex: 1; padding: 24px; }
        
        /* Mobile UI improvements & Bug Fixes */
        @media (max-width: 991px) {
            /* FIX LỖI MENU TÀI KHOẢN: Ép dropdown hiển thị kiểu đẩy khối (static) thay vì trôi nổi */
            #adminNavbarContent .dropdown-menu { 
                position: static !important; 
                float: none;
                background-color: transparent; 
                border: none; 
                box-shadow: none; 
                margin-top: 4px; 
                padding-left: 12px; 
            }
            #adminNavbarContent .dropdown-item { color: #94a3b8; padding: 10px 16px; border-radius: 6px; }
            #adminNavbarContent .dropdown-item:hover { background-color: rgba(255,255,255,0.05); color: #fff; }
            #adminNavbarContent .dropdown-header { color: #cbd5e1; font-size: 14px; padding-left: 0; }
            #adminNavbarContent .dropdown-divider { display: none; }
            .mobile-separator { border-top: 1px solid #1e293b; margin: 16px 0 8px 0; padding-top: 16px; }

            /* FIX BOX THÔNG BÁO MOBILE: Nằm ngoài Collapse nên cần nổi lơ lửng đè lên trang */
            .noti-dropdown-mobile {
                position: absolute !important;
                right: -10px !important;
                left: auto !important;
                background-color: #fff;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar shadow-sm sticky-top">
        <div class="container-fluid px-3 px-md-4">
            
            <a href="index.php?controller=admin&action=dashboard" class="navbar-brand admin-logo d-flex align-items-center">
                2Life <span style="color: #38bdf8; margin-left: 6px;">Admin</span>
            </a>

            <div class="d-flex align-items-center gap-3 d-lg-none">
                
                <div class="dropdown">
                    <a href="#" class="nav-link d-flex align-items-center position-relative text-white p-0" id="mobileNotiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.4em;">1</span>
                    </a>
                    <ul class="dropdown-menu shadow-sm mt-3 noti-dropdown-mobile" aria-labelledby="mobileNotiDropdown" style="min-width: 300px; z-index: 1060;">
                        <li><h6 class="dropdown-header text-dark fw-bold bg-light py-2 border-bottom">Thông báo hệ thống</h6></li>
                        <li>
                            <a class="dropdown-item py-3 text-wrap text-dark" href="index.php?controller=approveseller&action=index" style="background-color: #fff;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                                    <div>
                                        <strong class="d-block text-dark" style="font-size: 14px;">Hồ sơ người bán chờ duyệt</strong>
                                        <small class="text-muted d-block mt-1">Có yêu cầu mở Shop mới cần xác minh.</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <button class="navbar-toggler border-0 shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarContent" aria-controls="adminNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="adminNavbarContent">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-fill me-2 opacity-75"></i> Quản lý
                        </a>
                        <ul class="dropdown-menu shadow-sm mt-2" aria-labelledby="managementDropdown">
                            <li><a class="dropdown-item py-2" href="index.php?controller=category&action=index"><i class="bi bi-tags me-2 opacity-50"></i>Quản lý danh mục</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=user&action=index"><i class="bi bi-people me-2 opacity-50"></i>Quản lý người dùng</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=approveseller"><i class="bi bi-card-checklist me-2 opacity-50"></i>Phê duyệt người bán</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=approvelisting&action=index"><i class="bi bi-journal-check me-2 opacity-50"></i>Phê duyệt tin đăng</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=blog&action=index"><i class="bi bi-file-earmark-text me-2 opacity-50"></i>Quản lý blog</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=voucher&action=index"><i class="bi bi-ticket-perforated me-2 opacity-50"></i>Quản lý voucher</a></li>
                            <li><hr class="dropdown-divider d-none d-lg-block"></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=report&action=index"><i class="bi bi-bar-chart-line me-2 opacity-50"></i>Báo cáo thống kê</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="index.php?controller=admin_chat"><i class="bi bi-headset me-2 opacity-75"></i> Hỗ trợ</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 gap-lg-3 mobile-separator ms-lg-auto">

                    <div class="dropdown d-none d-lg-block">
                        <a href="#" class="nav-link d-flex align-items-center position-relative text-white px-2 rounded" id="desktopNotiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.4em;">1</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2" aria-labelledby="desktopNotiDropdown" style="min-width: 320px;">
                            <li><h6 class="dropdown-header text-dark fw-bold bg-light py-2 border-bottom">Thông báo hệ thống</h6></li>
                            <li>
                                <a class="dropdown-item py-3 text-wrap text-dark" href="index.php?controller=approveseller&action=index">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 14px;">Hồ sơ người bán chờ duyệt</strong>
                                            <small class="text-muted d-block mt-1">Có yêu cầu mở Shop mới cần xác minh.</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown w-100 w-lg-auto">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2 py-1 rounded" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(255,255,255,0.05); min-width: max-content;">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminName) ?>&background=38bdf8&color=fff&rounded=true" alt="Avatar" width="36" height="36" class="me-2 shadow-sm">
                            <div class="d-flex flex-column text-start me-2">
                                <strong style="font-size: 13.5px; line-height: 1.2;"><?= htmlspecialchars($adminName) ?></strong>
                                <small style="font-size: 11px; color: #94a3b8;"><?= htmlspecialchars($adminRole) ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg-end shadow-sm mt-2 w-100" aria-labelledby="accountDropdown">
                            <li><h6 class="dropdown-header text-dark fw-bold bg-light py-2 d-none d-lg-block border-bottom">Tài khoản</h6></li>
                            <li><a class="dropdown-item py-2 mt-1" href="index.php?controller=admin_profile&action=index"><i class="bi bi-person me-2 opacity-50"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=admin_setting&action=index"><i class="bi bi-gear me-2 opacity-50"></i>Cài đặt hệ thống</a></li>
                            <li><hr class="dropdown-divider d-none d-lg-block"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-medium" href="index.php?controller=auth&action=logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </nav>

    <main>