<?php include __DIR__ . '/../partials/user-header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4" style="min-height: 70vh;">
    <div class="mb-3">
        <a href="index.php?controller=manageorderseller" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn bán
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Sản phẩm trong đơn hàng</h5>
                    <div class="table-responsive">
                        <table class="table align-middle border-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th style="width: 50%;">Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Tạm tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['image_url'] ?? 'layout/images/no-image.jpg') ?>" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 250px;"><?= htmlspecialchars($item['title']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold text-dark"><?= $item['quantity'] ?></td>
                                        <td class="text-end text-dark"><?= number_format($item['unit_price'], 0, ',', '.') ?> đ</td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top pt-3 mt-3 float-end style-none" style="width: 300px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Giảm giá voucher:</span>
                            <span class="text-success fw-semibold">-<?= number_format($order['discount_amount'], 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="fw-bold text-dark">Thực thu hộ:</span>
                            <span class="fs-5 fw-bold text-danger"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            
            <?php if (!empty($order['shipping_note'])): ?>
                <div class="card shadow-sm border-0 rounded-4 bg-warning-subtle text-dark mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold"><i class="bi bi-chat-left-dots-fill me-2"></i>Ghi chú giao hàng:</h6>
                        <p class="mb-0 text-secondary italic">"<?= htmlspecialchars($order['shipping_note']) ?>"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thông tin giao nhận</h5>
                    <p class="mb-2 text-dark"><strong>Khách hàng:</strong> <?= htmlspecialchars($order['buyer_name']) ?></p>
                    <p class="mb-2 text-dark"><strong>SĐT:</strong> <?= htmlspecialchars($order['buyer_phone']) ?></p>
                    <p class="mb-0 text-muted small"><strong>Địa chỉ:</strong><br><?= htmlspecialchars($order['street_address']) ?></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thao tác xử lý</h5>
                    <div class="mb-4">
                        <label class="text-muted small d-block mb-1">Trạng thái hiện tại:</label>
                        <span id="ui-badge-status" class="fs-6 fw-bold px-3 py-1 bg-light border rounded-pill d-inline-block text-dark">
                            <i class="bi bi-info-circle me-1 text-warning"></i><?= mb_strtoupper($order['status_name']) ?>
                        </span>
                    </div>

                    <div id="action-wrapper">
                        <?php if ($order['status_id'] == 1): ?>
                            <form id="formAccept" onsubmit="handleAjaxSubmit(event, 'accept', this)" class="mb-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn text-white w-100 fw-bold rounded-3 p-2" style="background-color: #FF7A3D;">
                                    <i class="bi bi-check-circle me-2"></i>Xác nhận & Chuẩn bị
                                </button>
                            </form>
                            <button class="btn btn-outline-danger w-100 rounded-3 p-2 btn-sm cancel-btn" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Hủy đơn hàng này
                            </button>

                        <?php elseif ($order['status_id'] == 3): ?>
                            <form id="formShip" onsubmit="handleAjaxSubmit(event, 'ship', this)" class="mb-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn btn-success w-100 fw-bold rounded-3 p-2">
                                    <i class="bi bi-truck me-2"></i>Đã giao cho đơn vị vận chuyển
                                </button>
                            </form>
                            <button class="btn btn-outline-danger w-100 rounded-3 p-2 btn-sm cancel-btn" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Hủy đơn hàng
                            </button>

                        <?php elseif ($order['status_id'] == 4): ?>
                            <div class="alert alert-info text-dark small mb-0 rounded-3">
                                <i class="bi bi-info-circle-fill me-2 text-info"></i>Đơn hàng đã được giao đi. Chờ người mua xác nhận.
                            </div>
                        <?php elseif ($order['status_id'] == 5): ?>
                            <div class="alert alert-success text-dark small mb-0 rounded-3">
                                <i class="bi bi-check-all me-2 text-success"></i>Đơn hàng giao dịch thành công.
                            </div>
                        <?php elseif ($order['status_id'] == 6): ?>
                            <div class="alert alert-secondary small text-dark mb-0 rounded-3">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Đơn hàng này đã bị hủy bỏ.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form onsubmit="handleAjaxSubmit(event, 'cancel', this)">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Lý do hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Lý do hủy gửi người mua:</label>
                        <select name="cancel_reason" class="form-select mb-2" required>
                            <option value="Sản phẩm đã hết hàng / Gặp sự cố hư hỏng">Sản phẩm đã hết hàng / Gặp sự cố hư hỏng</option>
                            <option value="Không thỏa thuận được phương thức vận chuyển với khách">Không liên lạc/thỏa thuận được với khách</option>
                            <option value="Hủy đơn theo mong muốn của người mua">Hủy đơn theo yêu cầu của người mua</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold">Xác nhận hủy đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/user-footer.php'; ?>

<script>
function handleAjaxSubmit(event, actionName, formElement) {
    event.preventDefault();
    const formData = new FormData(formElement);
    const wrapper = document.getElementById('action-wrapper');
    const badge = document.getElementById('ui-badge-status');
    const orderId = formData.get('order_id');

    Swal.fire({
        title: 'Đang xử lý...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch(`index.php?controller=manageorderseller&action=${actionName}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });

            // Ẩn modal hủy nếu đang mở
            const cancelModalEl = document.getElementById('cancelModal');
            if(cancelModalEl) {
                const modal = bootstrap.Modal.getInstance(cancelModalEl);
                if(modal) modal.hide();
            }

            // Xử lý cập nhật giao diện (DOM) dựa trên hành động
            if (actionName === 'accept') {
                badge.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>CHUẨN BỊ HÀNG`;
                wrapper.innerHTML = `
                    <form id="formShip" onsubmit="handleAjaxSubmit(event, 'ship', this)" class="mb-2">
                        <input type="hidden" name="order_id" value="${orderId}">
                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-3 p-2">
                            <i class="bi bi-truck me-2"></i>Đã giao cho đơn vị vận chuyển
                        </button>
                    </form>
                    <button class="btn btn-outline-danger w-100 rounded-3 p-2 btn-sm cancel-btn" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-2"></i>Hủy đơn hàng
                    </button>
                `;
            } 
            else if (actionName === 'ship') {
                badge.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>ĐANG VẬN CHUYỂN`;
                wrapper.innerHTML = `
                    <div class="alert alert-info text-dark small mb-0 rounded-3">
                        <i class="bi bi-info-circle-fill me-2 text-info"></i>Đơn hàng đã được giao đi. Chờ người mua xác nhận.
                    </div>
                `;
            } 
            else if (actionName === 'cancel') {
                badge.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>ĐÃ HỦY`;
                wrapper.innerHTML = `
                    <div class="alert alert-secondary small text-dark mb-0 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Đơn hàng này đã bị hủy bỏ.
                    </div>
                `;
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: data.message
            });
        }
    })
    .catch(err => {
        Swal.fire('Lỗi', 'Không thể kết nối với máy chủ!', 'error');
    });
}
</script>