<?php
require_once '../../control/AuthController.php';
$auth = new AuthController();
$error = $auth->handleLogin();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../layout/style.css">
    <style>
        .split-bg {
            background: linear-gradient(rgba(31, 60, 90, 0.7), rgba(31, 60, 90, 0.7)), url('https://images.unsplash.com/photo-1555529771-835f59fc5efe?q=80&w=1000') center/cover;
        }

        .form-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-flex split-bg text-white align-items-center justify-content-center flex-column p-5">
                <h1 class="display-4 fw-bold mb-3">2Life Marketplace</h1>
                <p class="fs-5 text-center">Nền tảng giao thương đồ cũ an toàn, tiện lợi nhất dành cho sinh viên UEH.</p>
            </div>

            <div class="col-lg-6 form-side bg-light">
                <div class="w-100 p-4 p-md-5 mx-auto" style="max-width: 500px;">
                    <a href="../../index.php" class="text-decoration-none mb-4 d-block" style="color: var(--nav-color); font-weight: bold; font-size: 1.5rem;">&larr; Về trang chủ</a>

                    <h2 class="fw-bold mb-2" style="color: var(--nav-color);">Chào mừng trở lại! 👋</h2>
                    <p class="text-secondary mb-4">Vui lòng đăng nhập để tiếp tục mua bán.</p>

                    <?php if (isset($_GET['registered'])): ?>
                        <div class="alert alert-success p-2 text-center" style="font-size: 14px;">
                            🎉 Đăng ký thành công! Mời bạn đăng nhập vào hệ thống.
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger p-2 text-center" style="font-size: 14px;"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger p-2 text-center" style="font-size: 14px;"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="card-white p-4 shadow-sm rounded-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary" style="font-size:13px;">Email hoặc Username</label>
                            <input type="text" name="identifier" class="form-control form-control-lg" placeholder="Nhập thông tin..." required style="border-radius:10px; font-size:15px;">
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label fw-bold text-secondary" style="font-size:13px;">Mật khẩu</label>
                                <a href="#" class="text-decoration-none" style="font-size:13px; color: var(--btn-primary);">Quên mật khẩu?</a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required style="border-radius:10px; font-size:15px;">
                        </div>
                        <button type="submit" name="login" class="btn-2life-primary w-100 py-3 rounded-3 fw-bold">Đăng nhập ngay</button>
                    </form>

                    <div class="text-center mt-4" style="font-size: 14px;">
                        <span class="text-secondary">Bạn chưa có tài khoản?</span>
                        <a href="register.php" style="color: var(--btn-primary); font-weight:600; text-decoration:none;">Đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>