<?php if (empty($orders)): ?>
    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
        <i class="bi bi-wallet2 text-muted fs-1"></i>
        <p class="text-muted mt-2">Không tìm thấy đơn hàng nào ở trạng thái này.</p>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($orders as $o): ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 border-start border-4 rounded-3" 
                     style="border-left-color: <?= $o['status_id'] == 1 ? '#dc3545' : ($o['status_id'] == 3 ? '#ffc107' : ($o['status_id'] == 5 ? '#198754' : '#6c757d')) ?> !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <span class="fw-bold text-secondary">Mã đơn: #ORD<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <span class="text-muted mx-2">|</span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></small>
                            </div>
                            <div>
                                <span class="badge p-2 rounded-3 
                                    <?= $o['status_id'] == 1 ? 'bg-danger-subtle text-danger' : '' ?>
                                    <?= $o['status_id'] == 3 ? 'bg-warning-subtle text-dark' : '' ?>
                                    <?= $o['status_id'] == 4 ? 'bg-info-subtle text-dark' : '' ?>
                                    <?= $o['status_id'] == 5 ? 'bg-success-subtle text-success' : '' ?>
                                    <?= $o['status_id'] == 6 ? 'bg-light text-secondary' : '' ?>
                                ">
                                    <?= mb_strtoupper($o['status_name']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <p class="mb-1 text-dark fw-semibold">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>Địa chỉ nhận: <?= htmlspecialchars($o['street_address']) ?>
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-credit-card me-2"></i>Hình thức: <span class="text-uppercase fw-bold"><?= $o['payment_method'] ?></span> 
                                    (<span class="small"><?= $o['payment_status'] == 'completed' ? 'Đã thanh toán' : 'Chưa thanh toán' ?></span>)
                                </p>
                            </div>
                            <div class="col-md-3 text-md-end mt-2 mt-md-0">
                                <span class="text-muted small">Tổng thu nhập:</span>
                                <div class="fs-5 fw-bold text-danger"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="col-md-2 text-end mt-2 mt-md-0">
                                <a href="index.php?controller=manageorderseller&action=detail&id=<?= $o['id'] ?>" class="btn btn-sm text-white w-100 fw-semibold rounded-3" style="background-color: #FF7A3D;">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>