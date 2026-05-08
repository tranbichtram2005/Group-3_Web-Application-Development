<?php
require_once '../../control/AuthController.php';
$auth = new AuthController();
$error = $auth->handleRegister();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../layout/style.css">
    <style>
        body { background-color: #f4f8fb; }
        .split-bg {
            background: linear-gradient(rgba(255, 122, 61, 0.8), rgba(255, 122, 61, 0.8)), url('https://images.unsplash.com/photo-1472851294608-062f824d29cc?q=80&w=1000') center/cover;
        }
        .form-control, .form-select { border-radius: 10px; font-size: 14px; }
        label { font-size: 13px; color: #6B6B6B; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-lg-4 d-none d-lg-flex split-bg text-white align-items-center justify-content-center p-5 position-fixed h-100">
                <div class="text-center">
                    <h1 class="fw-bold mb-3">Tham gia 2Life</h1>
                    <p class="fs-6">Khởi tạo cửa hàng hoặc tìm kiếm hàng ngàn món đồ thanh lý chất lượng.</p>
                </div>
            </div>
            
            <div class="col-lg-8 offset-lg-4 p-4 p-md-5">
                <div class="max-w-700 mx-auto" style="max-width: 650px;">
                    <h2 class="fw-bold mb-4" style="color: var(--nav-color);">Tạo Tài Khoản Mới</h2>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger p-2 text-center" style="font-size: 14px;"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="card-white p-4 p-md-5 shadow-sm rounded-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">1. Thông tin cá nhân</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Username (Tên hiển thị) <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Ví dụ: 0912345678" required>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2">2. Địa chỉ giao nhận (Mặc định)</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                <select name="province_id" class="form-select" required>
                                    <option value="">Chọn Tỉnh</option>
                                    <option value="1">TP. Hồ Chí Minh</option>
                                    <option value="2">Hà Nội</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Quận / Huyện <span class="text-danger">*</span></label>
                                <select name="district_id" class="form-select" required>
                                    <option value="">Chọn Quận</option>
                                    <option value="1">Quận 1 (HCM)</option>
                                    <option value="3">Bình Thạnh (HCM)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Phường / Xã <span class="text-danger">*</span></label>
                                <select name="ward_id" class="form-select" required>
                                    <option value="">Chọn Phường</option>
                                    <option value="1">Bến Nghé (Q1)</option>
                                    <option value="4">Phường 25 (BT)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Số nhà, Tên đường <span class="text-danger">*</span></label>
                            <input type="text" name="street" class="form-control" placeholder="Ví dụ: 279 Nguyễn Tri Phương" required>
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2">3. Bảo mật</h5>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                        </div>

                        <button type="submit" name="register" class="btn-2life-primary w-100 py-3 rounded-3 fw-bold mt-2">Xác nhận Đăng ký & Nhận OTP</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <span class="text-secondary">Đã có tài khoản?</span> 
                        <a href="login.php" style="color: var(--btn-secondary); font-weight:600; text-decoration:none;">Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>