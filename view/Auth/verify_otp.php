<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực OTP - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; background-color: #f4f8fb;">
    <header class="navbar-2life text-center py-3">
        <a href="index.php?controller=home" class="logo text-decoration-none" style="font-size:28px; color: #fff; font-weight: 700;">2Life</a>
    </header>

    <main class="container py-5" style="max-width: 450px; flex-grow: 1; display: flex; align-items: center;">
        <div class="card-white p-4 p-md-5 shadow-sm text-center w-100" style="background:#fff; border-radius:15px;">
            <h3 class="fw-bold mb-3" style="color: var(--nav-color);">Xác thực Email</h3>
            
            <?php if(isset($result) && $result['status'] == 'success'): ?>
                <div class="alert alert-success p-3"><strong><?= $result['msg'] ?></strong></div>
                <a href="index.php?controller=auth&action=login" class="btn-2life-primary d-inline-block mt-2 w-100 py-2 text-decoration-none">Đến trang Đăng Nhập</a>
            <?php else: ?>
                <p class="text-secondary mb-4" style="font-size:13px;">Mã OTP 6 số đã được gửi đến email đăng ký của cậu.</p>
                
                <?php if(isset($result) && $result['status'] == 'error'): ?>
                    <div class="alert alert-danger p-2" style="font-size: 14px;"><?= $result['msg'] ?></div>
                <?php endif; ?>
                
                <form method="POST" action="index.php?controller=auth&action=verify_otp">
                    <div class="mb-4">
                        <input type="text" name="otp" class="form-control text-center fw-bold" placeholder="Nhập 6 số OTP" required style="font-size:24px; letter-spacing: 5px; height: 60px; border-radius: 12px; border-color:var(--btn-primary);">
                    </div>
                    <button type="submit" class="btn-2life-primary w-100 py-2.5 fw-bold rounded-3">Xác thực mã</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>