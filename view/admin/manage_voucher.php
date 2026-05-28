<?php include __DIR__ . '/../partials/admin-header.php'; ?>

<?php
$hasError     = $hasError     ?? false;
$errorMessage = $errorMessage ?? '';
$vouchers     = $vouchers     ?? [];
?>

<div class="container-fluid py-4">

    <!-- Page header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold">Quản lý Voucher</h2>
            <p class="text-secondary small mb-0">Tạo và quản lý các mã giảm giá cho khách hàng.</p>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold" style="border-radius: 20px;">
            Tổng số: <?php echo count($vouchers); ?>
        </span>
    </div>

    <!-- Error alert (hiển thị khi có lỗi từ form) -->
    <?php if ($hasError): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ===== CỘT TRÁI: Form thêm voucher ===== -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill text-success"></i>
                    <h6 class="mb-0 fw-bold">Thêm Voucher Mới</h6>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=voucher&action=createVoucher" method="POST">

                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold text-secondary small">
                                Mã Voucher <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="code" name="code"
                                placeholder="Ví dụ: SALE2025"
                                style="text-transform: uppercase;"
                                required autocomplete="off">
                            <div class="form-text">Mã sẽ tự động chuyển thành chữ hoa.</div>
                        </div>

                        <div class="mb-3">
                            <label for="discountValue" class="form-label fw-semibold text-secondary small">
                                Giá trị giảm (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="discountValue" name="discountValue"
                                placeholder="Ví dụ: 25000" min="1000" step="1000" required>
                        </div>

                        <div class="mb-3">
                            <label for="minOrderValue" class="form-label fw-semibold text-secondary small">
                                Đơn hàng tối thiểu (VNĐ)
                            </label>
                            <input type="number" class="form-control" id="minOrderValue" name="minOrderValue"
                                placeholder="Mặc định: 0 (không giới hạn)" min="0" step="1000">
                        </div>

                        <div class="mb-4">
                            <label for="expiryDate" class="form-label fw-semibold text-secondary small">
                                Ngày hết hạn <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            <i class="bi bi-ticket-perforated-fill me-2"></i>Phát Hành Voucher
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ===== CỘT PHẢI: Bảng danh sách voucher ===== -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bi bi-list-stars text-primary"></i>
                    <h6 class="mb-0 fw-bold">Danh Sách Voucher</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Mã Voucher</th>
                                <th class="py-3">Mức Giảm</th>
                                <th class="py-3">Đơn Tối Thiểu</th>
                                <th class="py-3">Ngày Hết Hạn</th>
                                <th class="py-3 text-end pe-4">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vouchers)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">
                                        <i class="bi bi-ticket-detailed display-4 mb-3 d-block text-muted"></i>
                                        Chưa có voucher nào được tạo.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($vouchers as $voucher): ?>
                                    <?php
                                        $isExpired = !empty($voucher['expires_at'])
                                            && strtotime($voucher['expires_at']) < strtotime('today');
                                    ?>
                                    <tr class="<?= $isExpired ? 'opacity-75' : '' ?>">
                                        <td class="ps-4 py-3">
                                            <span class="badge bg-success-subtle text-success px-3 py-2 fw-bold font-monospace"
                                                  style="font-size: 0.85rem; border-radius: 6px;">
                                                <i class="bi bi-ticket-perforated me-1"></i>
                                                <?php echo htmlspecialchars($voucher['code']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 fw-bold text-dark">
                                            <?php echo number_format($voucher['discount_value'], 0, ',', '.'); ?>đ
                                        </td>
                                        <td class="py-3 text-secondary">
                                            <?php
                                                $minVal = intval($voucher['min_order_value'] ?? 0);
                                                echo $minVal > 0
                                                    ? number_format($minVal, 0, ',', '.') . 'đ'
                                                    : '<span class="text-muted fst-italic">Không giới hạn</span>';
                                            ?>
                                        </td>
                                        <td class="py-3">
                                            <?php
                                                $expiry = new DateTime($voucher['expires_at']);
                                                echo $expiry->format('d/m/Y');
                                                if ($isExpired) {
                                                    echo ' <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Hết hạn</span>';
                                                } else {
                                                    $today = new DateTime('today');
                                                    $diff  = $today->diff($expiry)->days;
                                                    if ($diff <= 7) {
                                                        echo ' <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Sắp hết hạn</span>';
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td class="py-3 text-end pe-4">
                                            <!-- Nút Xóa → mở modal, KHÔNG dùng confirm() -->
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm px-3"
                                                    onclick="openDeleteModal(
                                                        <?php echo (int)$voucher['id']; ?>,
                                                        '<?php echo htmlspecialchars(addslashes($voucher['code'])); ?>'
                                                    )">
                                                <i class="bi bi-trash3-fill me-1"></i>Xóa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- end .row -->
</div><!-- end .container-fluid -->


<!-- ===== MODAL XÁC NHẬN XÓA (đặt ngoài container, trước footer) ===== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-body text-center px-5 pt-5 pb-3">
                <!-- Icon -->
                <div class="mb-3 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 72px; height: 72px; background: #fff0f0;">
                        <i class="bi bi-trash3-fill text-danger" style="font-size: 1.8rem;"></i>
                    </div>
                </div>

                <h5 class="fw-bold mb-2" id="deleteModalLabel">Xóa voucher?</h5>
                <p class="text-secondary mb-1">
                    Bạn sắp xóa voucher
                    <strong id="modalVoucherCode" class="text-dark font-monospace"></strong>
                </p>
                <p class="text-secondary small mb-0">Hành động này không thể hoàn tác.</p>
            </div>

            <div class="modal-footer border-0 justify-content-center pb-4 pt-3 gap-2">
                <button type="button"
                        class="btn btn-light px-4 fw-semibold"
                        style="border-radius: 8px;"
                        data-bs-dismiss="modal">
                    Hủy
                </button>
                <a id="confirmDeleteBtn"
                   href="#"
                   class="btn btn-danger px-4 fw-semibold"
                   style="border-radius: 8px;">
                    <i class="bi bi-trash3-fill me-1"></i>Xóa
                </a>
            </div>

        </div>
    </div>
</div>


<script>
// Tự động chuyển mã voucher thành chữ hoa khi gõ
document.getElementById('code').addEventListener('input', function () {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

// Mở modal xác nhận xóa, gán tên voucher + link xóa
function openDeleteModal(id, code) {
    document.getElementById('modalVoucherCode').textContent = code;
    document.getElementById('confirmDeleteBtn').href =
        'index.php?controller=voucher&action=deleteVoucher&id=' + id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php include __DIR__ . '/../partials/admin-footer.php'; ?>