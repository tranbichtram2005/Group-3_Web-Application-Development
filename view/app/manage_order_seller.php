<?php include __DIR__ . '/../partials/user-header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4" style="min-height: 70vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="bi bi-box-seam text-warning me-2"></i>Quản lý đơn hàng bán
        </h3>
        <a href="index.php?controller=dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-speedometer2"></i> Kênh người bán
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-1" id="orderTabs">
                <li class="nav-item">
                    <a class="nav-link ajax-tab active" data-status="0" style="background-color: #FF7A3D; color: white;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Tất cả <span class="badge bg-secondary ms-1" id="count-0"><?= $orderCounts[0] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-tab" data-status="1" style="color: #555;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Chờ duyệt <span class="badge bg-danger ms-1" id="count-1"><?= $orderCounts[1] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-tab" data-status="3" style="color: #555;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Chuẩn bị hàng <span class="badge bg-warning text-dark ms-1" id="count-3"><?= $orderCounts[3] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-tab" data-status="4" style="color: #555;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Đang vận chuyển <span class="badge bg-info text-dark ms-1" id="count-4"><?= $orderCounts[4] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-tab" data-status="5" style="color: #555;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Hoàn thành <span class="badge bg-success ms-1" id="count-5"><?= $orderCounts[5] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-tab" data-status="6" style="color: #555;" href="#" onclick="manageOrderSellerList_switchTab(event, this)">
                        Đã hủy <span class="badge bg-light text-dark ms-1" id="count-6"><?= $orderCounts[6] ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div id="orderListContainer">
        <?php include __DIR__ . '/manage_order_seller_list.php'; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/user-footer.php'; ?>