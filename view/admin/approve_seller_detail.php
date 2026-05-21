<?php include 'layout/admin-header.php'; ?>

<div class="container-fluid" style="max-width: 900px; margin: 0 auto;">
    <div class="mb-4">
        <a href="index.php?controller=approveseller&action=index" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
        </a>
        <h2 class="h4 mt-2 fw-bold text-dark">Hồ Sơ Cửa Hàng: <?= htmlspecialchars($request['shop_name']) ?></h2>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white fw-bold py-3 text-primary">
            <i class="bi bi-info-circle me-1"></i> Thông tin Người đăng ký
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-secondary small">Họ và tên chủ tài khoản</p>
                    <h6 class="fw-bold text-dark"><?= htmlspecialchars($request['full_name']) ?></h6>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-secondary small">Email</p>
                    <h6 class="text-dark"><?= htmlspecialchars($request['email']) ?></h6>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-secondary small">Số điện thoại liên hệ</p>
                    <h6 class="text-dark"><?= !empty($request['phone']) ? htmlspecialchars($request['phone']) : 'Chưa cập nhật' ?></h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white fw-bold py-3 text-success">
            <i class="bi bi-shop me-1"></i> Thông tin Cửa hàng đề xuất
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-secondary small">Tên Shop</p>
                    <h6 class="fw-bold fs-5 text-primary"><?= htmlspecialchars($request['shop_name']) ?></h6>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="mb-1 text-secondary small">Mã số thuế (Tax Code)</p>
                    <h6 class="text-dark fw-bold"><?= !empty($request['tax_code']) ? htmlspecialchars($request['tax_code']) : 'Không có' ?></h6>
                </div>
                <div class="col-12 mb-2">
                    <p class="mb-1 text-secondary small">Mô tả cửa hàng</p>
                    <div class="p-3 bg-light rounded text-dark" style="font-size: 14.5px;">
                        <?= nl2br(htmlspecialchars($request['description'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($request['is_verified'] == 0): ?>
        <div class="card border-0 shadow-sm rounded-3 bg-light border">
            <div class="card-body p-4 text-center">
                <h5 class="fw-bold mb-4">Quyết định Phê duyệt</h5>
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php?controller=approveseller&action=reject&id=<?= $request['id'] ?>" 
                       class="btn btn-outline-danger px-4 py-2 fw-bold" 
                       onclick="return confirm('Bạn muốn TỪ CHỐI và XÓA hồ sơ đăng ký này?')">
                        <i class="bi bi-x-circle-fill me-1"></i> Từ chối hồ sơ
                    </a>
                    
                    <a href="index.php?controller=approveseller&action=approve&id=<?= $request['id'] ?>&user_id=<?= $request['user_id'] ?>" 
                       class="btn btn-success px-4 py-2 fw-bold" 
                       onclick="return confirm('Xác nhận CẤP QUYỀN Seller cho tài khoản này?')">
                        <i class="bi bi-check-circle-fill me-1"></i> Chấp thuận mở Shop
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success text-center py-3 shadow-sm border-0">
            <i class="bi bi-patch-check-fill fs-4 d-block mb-2 text-success"></i>
            <h6 class="fw-bold mb-0">Hồ sơ này đã được phê duyệt!</h6>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/admin-footer.php'; ?>