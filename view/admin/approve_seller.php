<?php include __DIR__ . '/../partials/admin-header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4" style="min-height: 75vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">
            <i class="bi bi-shield-check text-primary me-2"></i>Phê duyệt yêu cầu mở gian hàng
        </h3>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold small">Bảng điều khiển Admin</span>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-1" id="approveTabs">
                <li class="nav-item">
                    <a class="nav-link tab-action active" data-status="0" href="#" style="background-color: #0d6efd; color: white;" onclick="approveSeller_switchTab(event, this)">
                        <i class="bi bi-clock-history me-2"></i>Hồ sơ chờ phê duyệt 
                        <span class="badge bg-danger ms-1" id="badge-pending"><?= $stats[0] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-action" data-status="1" href="#" style="color: #555;" onclick="approveSeller_switchTab(event, this)">
                        <i class="bi bi-check2-all me-2"></i>Gian hàng đã kích hoạt 
                        <span class="badge bg-secondary ms-1" id="badge-verified"><?= $stats[1] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-action" data-status="2" href="#" style="color: #555;" onclick="approveSeller_switchTab(event, this)">
                        <i class="bi bi-x-circle me-2"></i>Hồ sơ bị từ chối 
                        <span class="badge bg-secondary ms-1" id="badge-rejected"><?= $stats[2] ?? 0 ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div id="sellerListWrapper">
        <?php include __DIR__ . '/approve_seller_list.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/admin-footer.php'; ?>