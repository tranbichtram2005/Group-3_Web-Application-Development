<?php
// Đảm bảo session luôn hoạt động để đọc thông tin tài khoản đăng nhập
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Đọc chính xác Họ và tên Admin từ mảng Session user khi đăng nhập thành công
$adminName = isset($_SESSION['user']['full_name']) ? $_SESSION['user']['full_name'] : 'Admin Account';

// Hiển thị nhãn vai trò Admin hệ thống
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
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .admin-navbar { background-color: #1e293b; padding: 12px 24px; }
        .admin-logo { font-size: 22px; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: 0.5px; }
        .admin-logo-sub { font-size: 13px; color: #94a3b8; margin-left: 10px; padding-left: 10px; border-left: 1px solid #475569; }
        .admin-navbar .nav-link { color: #cbd5e1; font-weight: 500; font-size: 14.5px; cursor: pointer; }
        .admin-navbar .nav-link:hover, .admin-navbar .nav-link:focus { color: #fff; }
        .dropdown-menu { border: none; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 1060; }
        .admin-footer { background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 16px 0; color: #64748b; font-size: 13.5px; text-align: center; margin-top: auto; }
        main { flex: 1; padding: 24px; }
    </style>
</head>
<body>

    <header class="admin-navbar shadow-sm sticky-top">
        <div class="container-fluid px-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                
                <div class="d-flex align-items-center me-4">
                    <a href="index.php?controller=admin&action=dashboard" class="admin-logo">
                        2Life <span style="color: #38bdf8;">Admin</span>
                    </a>
                    <span class="admin-logo-sub d-none d-md-block">Kênh Quản trị Viên</span>
                </div>

                <ul class="nav me-auto mb-2 mb-md-0 gap-1 d-none d-lg-flex">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-fill me-1"></i> Quản lý
                        </a>
                        <ul class="dropdown-menu shadow" aria-labelledby="managementDropdown">
                            <li><a class="dropdown-item py-2" href="index.php?controller=category&action=index"><i class="bi bi-tags me-2 text-secondary"></i>Quản lý danh mục</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=user&action=index"><i class="bi bi-people me-2 text-secondary"></i>Quản lý tài khoản người dùng</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=approveseller&action=index"><i class="bi bi-card-checklist me-2 text-secondary"></i>Phê duyệt người bán</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=approvelisting&action=index"><i class="bi bi-journal-check me-2 text-secondary"></i>Phê duyệt tin đăng bán</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=blog&action=index"><i class="bi bi-file-earmark-text me-2 text-secondary"></i>Quản lý blog</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=voucher&action=index"><i class="bi bi-ticket-perforated me-2 text-secondary"></i>Quản lý voucher</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=report&action=index"><i class="bi bi-bar-chart-line me-2 text-secondary"></i>Báo cáo thống kê</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?controller=support&action=index"><i class="bi bi-headset me-1"></i> Hỗ trợ</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-4">
                    
                    <div class="dropdown">
                        <a href="#" class="nav-link position-relative text-white" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">1</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" style="width: 320px;">
                            <li><h6 class="dropdown-header text-dark fw-bold py-2">Thông báo hệ thống</h6></li>
                            <li><hr class="dropdown-divider mb-1"></li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?controller=approveseller&action=index">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong class="d-block" style="font-size: 13.5px; color: #1e293b;">Hồ sơ người bán chờ duyệt</strong>
                                            <small class="text-muted d-block mt-0.5">Có yêu cầu mở Shop mới cần xác minh.</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($adminName) ?>&background=38bdf8&color=fff" alt="Avatar" width="35" height="35" class="rounded-circle me-2">
                            <div class="d-flex flex-column text-start me-1">
                                <strong style="font-size: 14px; line-height: 1.2; color: #ffffff;"><?= htmlspecialchars($adminName) ?></strong>
                                <small style="font-size: 11px; color: #94a3b8;"><?= htmlspecialchars($adminRole) ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow mt-2" aria-labelledby="accountDropdown">
                            <li><h6 class="dropdown-header text-dark fw-bold">Quản lý tài khoản</h6></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=admin_profile&action=index"><i class="bi bi-person me-2"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item py-2" href="index.php?controller=admin_setting&action=index"><i class="bi bi-gear me-2"></i>Cài đặt hệ thống</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="index.php?controller=auth&action=logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <main>