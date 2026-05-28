<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
    <style>
        .split-bg {
            /* Tách rõ thuộc tính để 100% laptop load được ảnh */
            background-image: linear-gradient(rgba(31, 60, 90, 0.7), rgba(31, 60, 90, 0.7)), url('https://images.unsplash.com/photo-1555529771-835f59fc5efe?q=80&w=1000');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .form-side { min-height: 100vh; display: flex; align-items: center; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12 col-lg-6 d-none d-lg-flex split-bg text-white align-items-center justify-content-center flex-column p-5">
                <h1 class="display-4 fw-bold">2Life</h1>
                <p class="fs-5 text-center mt-3">Chào mừng cậu trở lại! Tiếp tục hành trình trao đổi và tái sử dụng đồ dùng cũ tiện lợi cùng cộng đồng sinh viên.</p>
            </div>
            <div class="col-12 col-lg-6 form-side bg-white px-4 px-md-5 d-flex align-items-center">
                <div class="w-100 mx-auto" style="max-width: 450px;">
                    
                    <div class="mb-4">
                        <a href="index.php?controller=home" class="text-decoration-none text-secondary fw-medium" style="font-size: 14px;">
                            <i class="bi bi-arrow-left"></i> Quay lại Trang chủ
                        </a>
                    </div>

                    <div class="mb-5">
                        <h2 class="fw-bold mb-2" style="color: var(--nav-color);">Đăng nhập</h2>
                        <p class="text-secondary">Vui lòng điền thông tin tài khoản của cậu</p>
                    </div>

                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger p-2" style="font-size: 14px; border-radius: 8px;"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=auth&action=login">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary" style="font-size:13px;">Email hoặc Username</label>
                            <input type="text" name="identifier" class="form-control form-control-lg" placeholder="Nhập thông tin..." required style="border-radius:10px; font-size:15px;">
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-bold text-secondary" style="font-size:13px;">Mật khẩu</label>
                                </div>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required style="border-radius:10px; font-size:15px;">
                        </div>
                        <button type="submit" name="login" class="btn-2life-primary w-100 py-3 rounded-3 fw-bold">Đăng nhập ngay</button>
                    </form>

                    <div class="text-center mt-4" style="font-size: 14px;">
                        <span class="text-secondary">Bạn chưa có tài khoản?</span>
                        <a href="index.php?controller=auth&action=register" style="color: var(--btn-primary); font-weight:600; text-decoration:none;">Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>