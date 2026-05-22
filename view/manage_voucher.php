<?php
require_once __DIR__ . '/../partials/admin-header.php';

// Khai báo mặc định để VS Code Intelephense hoàn toàn không báo lỗi (Undefined variable)
$hasError = $hasError ?? false;
$errorMessage = $errorMessage ?? '';
$vouchers = $vouchers ?? [];
?>

<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i>Thêm Voucher Mới
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($hasError): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?controller=voucher&action=createVoucher" method="POST">
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold text-secondary">Mã Voucher</label>
                            <input type="text" class="form-control form-control-lg" id="code" name="code" placeholder="Ví dụ: 2LIFEMARKETING" style="text-transform: uppercase; border-radius: 8px;" required>
                        </div>
                        <div class="mb-3">
                            <label for="discountValue" class="form-label fw-semibold text-secondary">Giá trị giảm (VNĐ)</label>
                            <input type="number" class="form-control form-control-lg" id="discountValue" name="discountValue" placeholder="Ví dụ: 25000" min="1000" style="border-radius: 8px;" required>
                        </div>
                        <div class="mb-3">
                            <label for="minOrderValue" class="form-label fw-semibold text-secondary">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" class="form-control form-control-lg" id="minOrderValue" name="minOrderValue" placeholder="Mặc định: 0" min="0" style="border-radius: 8px;" required>
                        </div>
                        <div class="mb-4">
                            <label for="expiryDate" class="form-label fw-semibold text-secondary">Ngày hết hạn</label>
                            <input type="date" class="form-control form-control-lg" id="expiryDate" name="expiryDate" style="border-radius: 8px;" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm" style="border-radius: 8px; background-color: #28a745; border: none;">
                            <i class="bi bi-ticket-perforated-fill me-2"></i>Phát Hành Voucher
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-list-stars text-primary me-2"></i>Danh Sách Chiến Dịch Voucher
                    </h5>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold" style="border-radius: 20px;">
                        Tổng số: <?php echo count($vouchers); ?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                            <thead class="table-light text-secondary fw-semibold" style="font-size: 0.9rem;">
                                <tr>
                                    <th class="ps-4 py-3">MÃ VOUCHER</th>
                                    <th class="py-3">MỨC GIẢM</th>
                                    <th class="py-3">ĐƠN TỐI THIỂU</th>
                                    <th class="py-3">NGÀY HẾT HẠN</th>
                                    <th class="py-3 text-center pe-4">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($vouchers)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-ticket-detailed text-secondary d-block mb-2" style="font-size: 2.5rem;"></i>
                                            Chưa có chiến dịch tiếp thị voucher nào được khởi tạo.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($vouchers as $voucher): ?>
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <span class="badge bg-success-subtle text-success px-3 py-2 fw-bold font-monospace" style="font-size: 0.95rem; border-radius: 6px;">
                                                    <?php echo htmlspecialchars($voucher['code']); ?>
                                                </span>
                                            </td>
                                            <td class="py-3 fw-bold text-dark">
                                                <?php echo number_format($voucher['discount_value']); ?>đ
                                            </td>
                                            <td class="py-3 text-secondary">
                                                <?php echo number_format($voucher['min_order_value']); ?>đ
                                            </td>
                                            <td class="py-3 text-secondary">
                                                <?php 
                                                    $expiry = new DateTime($voucher['expiry_date']);
                                                    echo $expiry->format('d/m/Y');
                                                    if ($expiry < new DateTime('today')) {
                                                        echo ' <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Đã hết hạn</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="py-3 text-center pe-4">
                                                <a href="index.php?controller=voucher&action=deleteVoucher&id=<?php echo $voucher['id']; ?>" 
                                                   class="btn btn-outline-danger btn-sm px-3" 
                                                   style="border-radius: 6px;"
                                                   onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn voucher này khỏi chiến dịch tiếp thị?');">
                                                    <i class="bi bi-trash3-fill me-1"></i>Xóa
                                                </a>
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
</div>

<?php
require_once __DIR__ . '/../partials/admin-footer.php';
?>