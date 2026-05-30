<?php include __DIR__ . '/../partials/admin-header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4" style="min-height: 75vh;">
    <div class="mb-3">
        <a href="index.php?controller=approveseller" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách phê duyệt
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Hồ sơ đăng ký gian hàng</h5>
                
                <div class="d-flex align-items-start gap-4 flex-wrap flex-sm-nowrap mb-4">
                    <img src="<?= htmlspecialchars($seller['avatar_url'] ?? 'uploads/avatars/no-avatar.png') ?>" class="rounded-circle border shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($seller['shop_name']) ?></h4>
                        <p class="text-muted mb-2 font-monospace small">Mã hồ sơ hệ thống: #ID-SP<?= $seller['id'] ?></p>
                        
                        <span id="status-badge" class="badge p-2 rounded-3 <?= $seller['is_verified'] == 1 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning text-dark' ?>">
                            <i class="bi bi-info-circle me-1"></i><?= $seller['is_verified'] == 1 ? 'ĐÃ KÍCH HOẠT' : 'ĐANG CHỜ XÉT DUYỆT' ?>
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 bg-light p-3 rounded-3 border-light border">
                        <small class="text-muted d-block small mb-1 fw-medium">Họ và tên người đăng ký</small>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($seller['full_name']) ?></span>
                    </div>
                    <div class="col-md-6 bg-light p-3 rounded-3 border-light border">
                        <small class="text-muted d-block small mb-1 fw-medium">Tên tài khoản (Username)</small>
                        <span class="fw-bold text-dark font-monospace">@<?= htmlspecialchars($seller['username']) ?></span>
                    </div>
                    <div class="col-md-6 bg-light p-3 rounded-3 border-light border">
                        <small class="text-muted d-block small mb-1 fw-medium">Địa chỉ thư điện tử Email</small>
                        <span class="fw-semibold text-secondary"><?= htmlspecialchars($seller['email']) ?></span>
                    </div>
                    <div class="col-md-6 bg-light p-3 rounded-3 border-light border">
                        <small class="text-muted d-block small mb-1 fw-medium">Số điện thoại liên hệ</small>
                        <span class="fw-semibold text-secondary"><?= htmlspecialchars($seller['phone'] ?? 'Chưa cung cấp') ?></span>
                    </div>
                    <div class="col-12 bg-light p-3 rounded-3 border-light border">
                        <small class="text-muted d-block small mb-1 fw-medium">Mô tả định hướng kinh doanh của Shop</small>
                        <p class="mb-0 text-dark" style="white-space: pre-line;"><?= htmlspecialchars($seller['description'] ?? 'Không có thông tin mô tả ngắn.') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Thông tin pháp lý</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block mb-1 fw-medium">Mã Số Thuế Doanh Nghiệp / Hộ Kinh Doanh:</label>
                    <div class="fs-4 fw-bold font-monospace text-danger bg-danger-subtle px-3 py-2 rounded-3 border border-danger-subtle text-center">
                        <?= htmlspecialchars($seller['tax_code'] ?? 'KHÔNG CÓ') ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Quyết định phê duyệt</h5>
                <div id="panel-action-wrapper">
                    <?php if ($seller['is_verified'] == 0): ?>
                        <button type="button" id="btnApprove" data-profile="<?= $seller['id'] ?>" data-user="<?= $seller['user_id'] ?>" class="btn btn-success w-100 fw-bold rounded-3 p-2 mb-2" onclick="approveSellerDetail_handleApprove(this)">
                            <i class="bi bi-check-circle me-2"></i>Phê duyệt kích hoạt
                        </button>
                        <button type="button" class="btn btn-outline-danger w-100 fw-medium rounded-3 p-2 btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-2"></i>Từ chối yêu cầu
                        </button>
                    <?php else: ?>
                        <div class="alert alert-success m-0 rounded-3 text-dark small">
                            <i class="bi bi-patch-check-fill me-2 text-success"></i>Hồ sơ này đã được duyệt và cấp quyền hoạt động bán hàng chính thức vào lúc: <br>
                            <strong class="small"><?= date('d/m/Y H:i', strtotime($seller['verified_at'])) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Lý do từ chối hồ sơ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Nhập lý do từ chối để hệ thống gửi thông báo phản hồi đến ứng viên:</label>
                    <textarea id="rejectReasonInput" class="form-control" rows="3" placeholder="Mã số thuế không hợp lệ..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 fw-semibold small" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="btnConfirmReject" data-profile="<?= $seller['id'] ?>" data-user="<?= $seller['user_id'] ?>" class="btn btn-danger rounded-3 fw-bold small" onclick="approveSellerDetail_handleReject(this)">Xác nhận từ chối</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/admin-footer.php'; ?>