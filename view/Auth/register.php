<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
    <style>
        body { background-color: #f4f8fb; }
        .split-bg {
            background-image: linear-gradient(rgba(255, 122, 61, 0.8), rgba(255, 122, 61, 0.8)), url('https://images.unsplash.com/photo-1472851294608-062f824d29cc?q=80&w=1000');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .form-control, .form-select { border-radius: 10px; font-size: 14px; }
        label { font-size: 13px; color: #6B6B6B; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12 col-lg-4 d-none d-lg-flex split-bg text-white align-items-center justify-content-center flex-column p-5 text-center">
                <h1 class="display-4 fw-bold">2Life</h1>
                <p class="fs-5 mt-3">Tham gia cùng cộng đồng trao đổi đồ cũ sinh viên UEH. Tiết kiệm chi phí, bảo vệ môi trường!</p>
            </div>
            
            <div class="col-12 col-lg-8 bg-white p-4 p-md-5 d-flex align-items-center" style="min-height: 100vh;">
                <div class="w-100 mx-auto" style="max-width: 600px;">
                    
                    <div class="mb-4">
                        <a href="index.php?controller=home" class="text-decoration-none text-secondary fw-medium" style="font-size: 14px;">
                            <i class="bi bi-arrow-left"></i> Quay lại Trang chủ
                        </a>
                    </div>

                    <div class="mb-4">
                        <h2 class="fw-bold mb-1" style="color: var(--nav-color);">Tạo tài khoản mới</h2>
                        <p class="text-secondary">Vui lòng điền đầy đủ các thông tin bên dưới</p>
                    </div>

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger p-2" style="font-size: 14px; border-radius: 8px;"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=auth&action=register">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">1. Thông tin cá nhân</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Ví dụ: Lê Thanh Vy" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="Ví dụ: 0901234567" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Ví dụ: vythanh99" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Ví dụ: vy@st.ueh.edu.vn" required>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 border-bottom pb-2">2. Địa chỉ nhận/giao hàng</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                <select name="province_id" id="province" class="form-select" required>
                                    <option value="">-- Chọn Tỉnh/Thành --</option>
                                    <?php if(isset($provinces)): foreach($provinces as $prov): ?>
                                        <option value="<?= $prov['id'] ?>"><?= htmlspecialchars($prov['name']) ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Quận / Huyện <span class="text-danger">*</span></label>
                                <select name="district_id" id="district" class="form-select" required disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Phường / Xã <span class="text-danger">*</span></label>
                                <select name="ward_id" id="ward" class="form-select" required disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
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
                    
                    <div class="text-center mt-4 pb-4">
                        <span class="text-secondary">Đã có tài khoản?</span> 
                        <a href="index.php?controller=auth&action=login" style="color: var(--btn-secondary); font-weight:600; text-decoration:none;">Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="layout/script.js?v=<?= time() ?>"></script>
</body>
</html>