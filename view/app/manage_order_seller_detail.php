<?php include __DIR__ . '/../partials/user-header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .text-2life { color: #FF7A3D !important; }
    .bg-2life { background-color: #FF7A3D !important; }
    .btn-2life { background-color: #FF7A3D; color: white; font-weight: 600; border-radius: 8px; }
    .btn-2life:hover { background-color: #e0632b; color: white; }

    /* KHU VỰC ĐỊNH NGHĨA PHIẾU GIAO HÀNG (MẶC ĐỊNH ẨN TRÊN MÀN HÌNH WEB THƯỜNG) */
    #print-shipping-label {
        display: none;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #000;
        background: #fff;
    }

    /* CSS KHI TRÌNH DUYỆT BẮT ĐẦU IN (LỆNH WINDOW.PRINT) */
    @media print {
        /* Ẩn hoàn toàn tất cả các thành phần giao diện web của hệ thống 2life */
        body * {
            visibility: hidden;
        }
        /* Chỉ hiển thị duy nhất block phiếu giao hàng */
        #print-shipping-label, #print-shipping-label * {
            visibility: visible;
        }
        #print-shipping-label {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 15px;
            border: 2px dashed #FF7A3D;
            border-radius: 12px;
        }
        @page {
            size: A5 portrait;
            margin: 10mm;
        }
    }

    /* Định dạng tem nhãn */
    .label-header { border-bottom: 2px solid #FF7A3D; padding-bottom: 8px; margin-bottom: 12px; }
    .label-section { border-bottom: 1px dashed #ccc; padding-bottom: 8px; margin-bottom: 8px; }
    .cod-box { border: 2px solid #FF7A3D; background-color: #fff3cd; padding: 10px; text-align: center; border-radius: 8px; }
    .barcode-placeholder { font-family: 'Courier New', Courier, monospace; letter-spacing: 5px; font-weight: bold; background: #eee; padding: 6px; display: inline-block; text-align: center; }
</style>

<div class="container py-4" style="min-height: 70vh;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="index.php?controller=manageorderseller" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn bán
        </a>
        
        <?php if (isset($order['status_id']) && in_array($order['status_id'], [2, 3, 4, 5])): ?>
            <button type="button" onclick="window.print();" class="btn btn-2life btn-sm shadow-sm px-3">
                <i class="bi bi-printer-fill me-2"></i>In phiếu giao hàng
            </button>
        <?php endif; ?>
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
                                <?php 
                                $subtotal = 0; // Tính toán tổng tiền hàng gốc
                                foreach ($items as $item): 
                                    // Tự động kiểm tra tên cột để tránh lỗi Null * Int trên các phiên bản PHP
                                    $itemPrice = $item['price'] ?? $item['unit_price'] ?? 0;
                                    $itemTotal = $itemPrice * $item['quantity'];
                                    $subtotal += $itemTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['image_url'] ?? 'layout/images/no-image.jpg') ?>" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 250px;"><?= htmlspecialchars($item['title']) ?></h6>
                                                    <small class="text-muted">Mã sản phẩm: #LP<?= $item['listing_id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold text-dark"><?= $item['quantity'] ?></td>
                                        <td class="text-end text-dark"><?= number_format($itemPrice, 0, ',', '.') ?> đ</td>
                                        <td class="text-end fw-bold text-dark"><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php 
                                endforeach; 

                                // THUẬT TOÁN TỰ ĐỘNG SUY LUẬN TIỀN SHIP AN TOÀN TUYỆT ĐỐI
                                $discountAmount = isset($order['discount_amount']) ? (int)$order['discount_amount'] : 0;
                                $shippingFee = isset($order['shipping_fee']) && (int)$order['shipping_fee'] > 0 
                                               ? (int)$order['shipping_fee'] 
                                               : ((int)($order['total_amount'] ?? 0) - $subtotal + $discountAmount);
                                if ($shippingFee < 0) $shippingFee = 0;
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top pt-3 mt-3 float-end" style="width: 350px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="text-dark fw-medium"><?= number_format($subtotal, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="text-dark fw-medium">+<?= number_format($shippingFee, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Giảm giá voucher:</span>
                            <span class="text-success fw-semibold">-<?= number_format($discountAmount, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mb-3">
                            <span class="fw-bold text-dark">Tổng giá trị đơn hàng:</span>
                            <span class="fs-5 fw-bold text-2life"><?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?> đ</span>
                        </div>
                        
                        <div class="p-3 bg-light rounded-3 border">
                          <small class="text-muted d-block small mb-1">Phương thức thanh toán của khách:</small>
                            <span class="fw-bold text-dark text-uppercase small d-block mb-2">
                                <i class="bi bi-credit-card me-1"></i><?= htmlspecialchars($order['payment_method_name'] ?? 'Chưa xác định') ?>
                            </span>
                            <small class="text-muted d-block small mb-1">Trạng thái thu hộ tiền mặt (COD):</small>
                            <?php 
                                // KIỂM TRA LINH HOẠT: Nếu tên phương thức chứa chữ "COD" (không phân biệt hoa thường)
                                $methodName = strtolower($order['payment_method_name'] ?? '');
                                $isCOD = strpos($methodName, 'cod') !== false;
                            
                                if (!$isCOD): // Khách đã thanh toán trước qua các cổng (MoMo, VNPay, Bank...)
                            ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle w-100 p-2 text-start fs-7">
                                    <i class="bi bi-shield-check me-1"></i>0 đ (Khách đã trả qua <?= htmlspecialchars($order['payment_method_name']) ?>)
                                </span>
                            <?php else: // Thanh toán tiền mặt COD khi nhận hàng ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle w-100 p-2 text-start fs-7">
                                    <i class="bi bi-cash-coin me-1"></i>Thu hộ COD: <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?> đ
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <?php if (!empty($order['shipping_note'])): ?>
                <div class="card shadow-sm border-0 rounded-4 bg-warning-subtle text-dark mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold"><i class="bi bi-chat-left-dots-fill me-2"></i>Ghi chú giao hàng từ khách hàng:</h6>
                        <p class="mb-0 text-secondary italic">"<?= htmlspecialchars($order['shipping_note']) ?>"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thông tin giao nhận</h5>
                    <p class="mb-2 text-dark"><strong>Khách hàng:</strong> <?= htmlspecialchars($order['buyer_name'] ?? 'N/A') ?></p>
                    <p class="mb-2 text-dark"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['buyer_phone'] ?? 'N/A') ?></p>
                    <p class="mb-0 text-muted small"><strong>Địa chỉ gửi đến:</strong><br><?= htmlspecialchars($order['street_address'] ?? 'N/A') ?></p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thao tác xử lý</h5>
                    <div class="mb-4">
                        <label class="text-muted small d-block mb-1">Trạng thái hiện tại:</label>
                        <span id="ui-badge-status" class="fs-6 fw-bold px-3 py-1 bg-light border rounded-pill d-inline-block text-dark">
                            <i class="bi bi-info-circle me-1 text-warning"></i><?= mb_strtoupper($order['status_name'] ?? 'N/A') ?>
                        </span>
                    </div>

                    <div id="action-wrapper">
                        <?php if (isset($order['status_id']) && $order['status_id'] == 1): ?>
                            <form id="formAccept" onsubmit="handleAjaxSubmit(event, 'accept', this)" class="mb-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn btn-2life w-100 fw-bold p-2 mb-2">
                                    <i class="bi bi-check-circle me-2"></i>Xác nhận & Chuẩn bị hàng
                                </button>
                            </form>
                            <button class="btn btn-outline-danger w-100 rounded-3 p-2 btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Hủy đơn hàng này
                            </button>

                        <?php elseif (isset($order['status_id']) && $order['status_id'] == 3): ?>
                            <form id="formShip" onsubmit="handleAjaxSubmit(event, 'ship', this)" class="mb-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn btn-success w-100 fw-bold rounded-3 p-2 mb-2">
                                    <i class="bi bi-truck me-2"></i>Đã giao cho đơn vị vận chuyển
                                </button>
                            </form>
                            <button class="btn btn-outline-danger w-100 rounded-3 p-2 btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Hủy đơn hàng
                            </button>

                        <?php elseif (isset($order['status_id']) && $order['status_id'] == 4): ?>
                            <div class="alert alert-info text-dark small mb-0 rounded-3">
                                <i class="bi bi-info-circle-fill me-2 text-info"></i>Đơn hàng đã giao cho vận chuyển. Đang chờ người mua bấm xác nhận nhận hàng.
                            </div>
                        <?php elseif (isset($order['status_id']) && $order['status_id'] == 5): ?>
                            <div class="alert alert-success text-dark small mb-0 rounded-3">
                                <i class="bi bi-check-all me-2 text-success"></i>Đơn hàng giao dịch thành công.
                            </div>
                        <?php elseif (isset($order['status_id']) && $order['status_id'] == 6): ?>
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

<div id="print-shipping-label">
    <div class="label-header d-flex justify-content-between align-items-center">
        <div>
            <h3 style="color: #FF7A3D; margin: 0; font-weight: 800;">2LIFE.VN</h3>
            <small style="font-size: 11px; text-transform: uppercase; color: #555;">Sàn TMĐT đồ cũ C2C an toàn</small>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 13px; font-weight: bold;">Mã đơn hàng:</span>
            <div class="barcode-placeholder">#ODR<?= str_pad($order['id'], 5, "0", STR_PAD_LEFT) ?></div>
            <br><small style="font-size: 11px; color: #666;">Ngày đặt: <?= isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '' ?></small>
        </div>
    </div>

    <div class="row label-section" style="margin: 0; padding-bottom: 12px;">
        <div class="col-6" style="padding-left: 0; border-right: 1px dashed #ccc;">
            <strong style="font-size: 12px; color: #FF7A3D; text-transform: uppercase; display: block; margin-bottom: 4px;">Từ (Người gửi):</strong>
            <span style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($order['shop_name'] ?? 'Gian hàng thành viên') ?></span><br>
            <span style="font-size: 13px;">SĐT: <?= htmlspecialchars($order['shop_phone'] ?? 'N/A') ?></span>
        </div>
        <div class="col-6" style="padding-right: 0; padding-left: 15px;">
            <strong style="font-size: 12px; color: #FF7A3D; text-transform: uppercase; display: block; margin-bottom: 4px;">Đến (Người nhận):</strong>
            <span style="font-weight: 700; font-size: 15px;"><?= htmlspecialchars($order['buyer_name'] ?? 'N/A') ?></span><br>
            <span style="font-weight: 600; font-size: 14px;">SĐT: <?= htmlspecialchars($order['buyer_phone'] ?? 'N/A') ?></span><br>
            <span style="font-size: 13px; line-height: 1.3; display: inline-block; margin-top: 2px;"><?= htmlspecialchars($order['street_address'] ?? 'N/A') ?></span>
        </div>
    </div>

    <div class="label-section">
        <strong style="font-size: 12px; color: #555; display: block; margin-bottom: 5px;">Nội dung hàng hóa (Tổng SL: <?= count($items) ?>):</strong>
        <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 1px solid #ddd; text-align: left;">
                    <th style="padding: 4px;">Tên sản phẩm</th>
                    <th style="text-align: center; padding: 4px; width: 15%;">SL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 5px; font-weight: 500;">• <?= htmlspecialchars($item['title']) ?></td>
                        <td style="text-align: center; padding: 5px; font-weight: bold;"><?= $item['quantity'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row align-items-center" style="margin: 10px 0 0 0;">
        <div class="col-7" style="padding-left: 0; font-size: 11px; color: #444; line-height: 1.4;">
            <strong>Chỉ thị của Sàn:</strong><br>
            - Không cho xem hàng (Tránh tráo đổi linh kiện đồ cũ).<br>
            - Chuyển hoàn sau 3 lần phát thất bại.<br>
            <?php if (!empty($order['shipping_note'])): ?>
                <strong style="color: #000;">Lưu ý của khách:</strong> "<?= htmlspecialchars($order['shipping_note']) ?>"
            <?php endif; ?>
        </div>
        
        <div class="col-5" style="padding-right: 0;">
            <div class="cod-box">
              <small style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; display: block; margin-bottom: 2px;">Số tiền cần thu hộ (COD):</small>
                <?php 
                    // Tái sử dụng logic kiểm tra tên phương thức
                    if (!$isCOD): // Nếu không phải là COD
                ?>
                    <span style="font-size: 22px; font-weight: 800; color: #198754; display: block; line-height: 1;">0 ĐỒNG</span>
                    <small style="font-size: 9px; font-weight: bold; color: #198754; display: block; margin-top: 4px;">ĐÃ TT QUA <?= mb_strtoupper($order['payment_method_name']) ?></small>
                <?php else: // Nếu là COD ?>
                    <span style="font-size: 20px; font-weight: 800; color: #dc3545; display: block; line-height: 1;"><?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?> đ</span>
                    <small style="font-size: 9px; font-weight: bold; color: #dc3545; display: block; margin-top: 4px;">THU TIỀN MẶT KHI GIAO</small>
                <?php endif; ?>
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
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?? 0 ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Vui lòng cung cấp lý do hủy để thông báo đến người mua:</label>
                        <select name="cancel_reason" class="form-select mb-2" required>
                            <option value="Sản phẩm đã hết hàng đột xuất">Sản phẩm đã hết hàng / Gặp sự cố hư hỏng</option>
                            <option value="Không liên hệ được với người mua để giao dịch">Không thỏa thuận được phương thức vận chuyển với khách</option>
                            <option value="Hủy đơn theo yêu cầu trao đổi của người mua">Hủy đơn theo mong muốn của người mua</option>
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
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
        }
    })
    .catch(err => {
        Swal.fire('Lỗi', 'Không thể kết nối với máy chủ!', 'error');
    });
}
</script>