<?php include __DIR__ . '/../partials/admin-header.php'; ?>

<?php
$hasError     = $hasError     ?? false;
$errorMessage = $errorMessage ?? '';
$vouchers     = $vouchers     ?? [];
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .card-modern { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); transition: 0.3s; }
    .card-modern:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
    .card-header-modern { background-color: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 12px 12px 0 0 !important; padding: 1.25rem 1.5rem; }
    .form-control, .form-select { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #e0e0e0; }
    .form-control:focus, .form-select:focus { border-color: #FF7A3D; box-shadow: 0 0 0 0.25rem rgba(255, 122, 61, 0.15); }
    .btn-brand { background-color: #FF7A3D; color: #fff; border-radius: 8px; border: none; padding: 0.6rem 1.5rem; font-weight: 600; }
    .btn-brand:hover { background-color: #e66a35; color: #fff; }
    .table-hover tbody tr:hover { background-color: #fff9f6; }
</style>

<div class="container-fluid py-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark"><i class="bi bi-ticket-perforated me-2" style="color: #FF7A3D;"></i>Quản lý Voucher</h3>
            <p class="text-secondary mb-0">Thiết lập các chương trình khuyến mãi cho khách hàng.</p>
        </div>
        <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill">Tổng: <?php echo count($vouchers); ?> mã</span>
    </div>

    <?php if ($hasError): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-modern">
                <div class="card-header-modern d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-success fs-5"></i>
                    <h6 class="mb-0 fw-bold fs-5">Thêm Voucher Mới</h6>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=voucher&action=createVoucher" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Mã Voucher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace fw-bold text-uppercase text-primary" id="code" name="code" placeholder="VD: 2LIFESALE" required autocomplete="off">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Loại giảm <span class="text-danger">*</span></label>
                                <select class="form-select" name="typeId" id="typeId" onchange="window.adminToggleMaxDiscount()">
                                    <option value="2">Tiền mặt (VNĐ)</option>
                                    <option value="1">Phần trăm (%)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small" id="discountLabel">Mức giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="discountValue" placeholder="VD: 20000" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3" id="maxDiscountWrapper" style="display: none;">
                            <label class="form-label fw-semibold text-dark small">Giảm tối đa (VNĐ)</label>
                            <input type="number" class="form-control" name="maxDiscount" placeholder="VD: 50000 (Để trống nếu ko giới hạn)" min="0">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Đơn tối thiểu</label>
                                <input type="number" class="form-control" name="minOrderValue" placeholder="Mặc định: 0" min="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="totalQuantity" placeholder="VD: 100" min="1" required value="100">
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="startsAt" required value="<?= date('Y-m-d\TH:i') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="expiryDate" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand w-100 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Phát Hành
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-modern h-100">
                <div class="card-header-modern d-flex align-items-center gap-2">
                    <i class="bi bi-list-stars text-primary fs-5" style="color: #FF7A3D !important;"></i>
                    <h6 class="mb-0 fw-bold fs-5">Kho Voucher Hệ Thống</h6>
                </div>

                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary fw-semibold">Mã Voucher</th>
                                <th class="text-secondary fw-semibold">Ưu Đãi</th>
                                <th class="text-secondary fw-semibold">Sử Dụng</th>
                                <th class="text-secondary fw-semibold">Thời Gian</th>
                                <th class="text-secondary fw-semibold text-end">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vouchers)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">
                                        <i class="bi bi-ticket-detailed display-4 mb-3 d-block text-muted opacity-50"></i>
                                        Chưa có voucher nào được tạo.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($vouchers as $v): 
                                    $isExpired = !empty($v['expires_at']) && strtotime($v['expires_at']) < time();
                                    $isDepleted = $v['used_quantity'] >= $v['total_quantity'];
                                    $isUsed = $v['used_quantity'] > 0;
                                    
                                    $discountStr = '';
                                    if ($v['type_id'] == 1) { 
                                        $discountStr = "{$v['discount_value']}%";
                                        if (!empty($v['max_discount_amount'])) {
                                            $discountStr .= "<br><small class='text-muted'>(Tối đa " . number_format($v['max_discount_amount'], 0, ',', '.') . "đ)</small>";
                                        }
                                    } else { 
                                        $discountStr = number_format($v['discount_value'], 0, ',', '.') . "đ";
                                    }
                                ?>
                                    <tr class="<?= ($isExpired || $isDepleted) ? 'opacity-75 bg-light' : '' ?>">
                                        <td>
                                            <span class="badge bg-dark px-3 py-2 fw-bold font-monospace fs-6 shadow-sm">
                                                <?= htmlspecialchars($v['code']); ?>
                                            </span>
                                            <?php if ($v['min_order_value'] > 0): ?>
                                                <div class="small text-muted mt-1 fw-medium">Đơn tối thiểu: <?= number_format($v['min_order_value'], 0, ',', '.') ?>đ</div>
                                            <?php else: ?>
                                                <div class="small text-muted mt-1 fw-medium">Không giới hạn đơn</div>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="fw-bold text-success">
                                            <?= $discountStr ?>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <?php $percent = ($v['total_quantity'] > 0) ? ($v['used_quantity'] / $v['total_quantity']) * 100 : 0; ?>
                                                    <div class="progress-bar <?= $percent >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $percent ?>%"></div>
                                                </div>
                                                <span class="small fw-semibold text-secondary"><?= $v['used_quantity'] ?>/<?= $v['total_quantity'] ?></span>
                                            </div>
                                            <?php if ($isDepleted): ?>
                                                <span class="badge bg-danger mt-1" style="font-size: 0.7rem;">Hết lượt</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td>
                                            <div class="small fw-medium text-dark">
                                                <i class="bi bi-clock me-1 text-secondary"></i><?= date('d/m/Y H:i', strtotime($v['expires_at'])) ?>
                                            </div>
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1">Đã hết hạn</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="text-end">
                                            <?php if ($isUsed): ?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3" disabled title="Không thể xóa vì đã có khách hàng sử dụng">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3" 
                                                        onclick="window.adminOpenDeleteModal(<?= (int)$v['id'] ?>, '<?= htmlspecialchars(addslashes($v['code'])) ?>')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body text-center px-5 pt-5 pb-3">
                <div class="mb-3 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background: #fff0f0;">
                        <i class="bi bi-trash3-fill text-danger" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Xóa voucher?</h5>
                <p class="text-secondary mb-1">Bạn sắp xóa voucher <strong id="modalVoucherCode" class="text-dark font-monospace"></strong></p>
                <p class="text-secondary small mb-0">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-3 gap-2">
                <button type="button" class="btn btn-light px-4 fw-semibold rounded-3" data-bs-dismiss="modal">Hủy</button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger px-4 fw-semibold rounded-3"><i class="bi bi-trash3-fill me-1"></i>Xóa</a>
            </div>
        </div>
    </div>
</div>

<script src="layout/script.js?v=<?= time() ?>"></script>

<?php include __DIR__ . '/../partials/admin-footer.php'; ?>