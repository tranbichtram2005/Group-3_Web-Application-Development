<?php
// Guard tránh lỗi IDE
$orderCounts = $orderCounts ?? [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$orders = $orders ?? [];
$currentStatus = isset($_GET['status']) ? (int)$_GET['status'] : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        
        /* Card Đơn Hàng */
        .order-card {
            background: #fff; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 20px; transition: 0.2s;
            border-left: 5px solid transparent; 
        }
        .order-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        
        .border-status-1 { border-color: #dc3545; }
        .border-status-2 { border-color: #fd7e14; }
        .border-status-3 { border-color: #ffc107; }
        .border-status-4 { border-color: #0d6efd; }
        .border-status-5 { border-color: #198754; }
        .border-status-6 { border-color: #6c757d; }

        /* Toast Message */
        .toast-2life { position: fixed; top: 30px; right: 30px; z-index: 10000; background-color: #4CAF50; border-left: 6px solid #FF7A3D; color: #fff; padding: 15px 25px; border-radius: 6px; font-weight: 600; font-size: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; transform: translateX(120%); transition: transform 0.4s ease; }
        .toast-2life.show-toast { transform: translateX(0); }
    </style>
</head>
<body>
<?php include __DIR__ . '/../partials/user-header.php'; ?>

<?php if (isset($_SESSION['toast_msg'])): ?>
    <div id="toastMessage" class="toast-2life">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span><?= htmlspecialchars($_SESSION['toast_msg']) ?></span>
    </div>
    <?php unset($_SESSION['toast_msg']); ?>
<?php endif; ?>

<div class="container py-4" style="min-height: 70vh; max-width: 1100px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="bi bi-bag-check-fill text-warning me-2" style="color: #FF7A3D !important;"></i>Đơn mua của tôi
        </h3>
        <a href="index.php?controller=home" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-shop"></i> Tiếp tục mua sắm
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-1" id="orderTabs">
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 0 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 0 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order">
                        Tất cả <span class="badge bg-secondary ms-1"><?= $orderCounts[0] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 1 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 1 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=1">
                        Chờ xác nhận <span class="badge bg-danger ms-1"><?= $orderCounts[1] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 2 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 2 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=2">
                        Đã xác nhận <span class="badge bg-info ms-1"><?= $orderCounts[2] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 3 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 3 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=3">
                        Đang chuẩn bị <span class="badge bg-warning text-dark ms-1"><?= $orderCounts[3] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 4 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 4 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=4">
                        Đang giao <span class="badge bg-primary ms-1"><?= $orderCounts[4] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 5 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 5 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=5">
                        Hoàn thành <span class="badge bg-success ms-1"><?= $orderCounts[5] ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentStatus === 6 ? 'active fw-bold' : '' ?>" style="<?= $currentStatus === 6 ? 'background-color: #FF7A3D; color: white;' : 'color: #555;' ?>" href="index.php?controller=order&status=6">
                        Đã hủy <span class="badge bg-light text-dark border ms-1"><?= $orderCounts[6] ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div id="orderListContainer">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light mt-4">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                <h5 class="mt-3 text-dark fw-bold">Trống trơn!</h5>
                <p class="text-muted">Bạn chưa có đơn hàng nào ở trạng thái này.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $badgeBg = 'bg-secondary'; $statusCode = 'UNKNOWN';
                
                // GIỮ NGUYÊN CHỮ TIẾNG ANH CHUẨN DB
                switch ($order['status_id']) {
                    case 1: $badgeBg = 'bg-danger text-white'; $statusCode = 'PENDING'; break;
                    case 2: $badgeBg = 'bg-info text-dark'; $statusCode = 'CONFIRMED'; break;
                    case 3: $badgeBg = 'bg-warning text-dark'; $statusCode = 'PREPARING'; break;
                    case 4: $badgeBg = 'bg-primary text-white'; $statusCode = 'SHIPPED'; break;
                    case 5: $badgeBg = 'bg-success text-white'; $statusCode = 'DELIVERED'; break;
                    case 6: $badgeBg = 'bg-secondary text-white'; $statusCode = 'CANCELLED'; break;
                }
            ?>
                <div class="order-card border-status-<?= $order['status_id'] ?> p-4">
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                        <div>
                            <span class="text-dark fw-bold" style="font-size: 1.1rem;">#ORD<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            <span class="text-muted small ms-2"><i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <span class="badge <?= $badgeBg ?> px-3 py-2 rounded-pill"><?= $statusCode ?></span>
                    </div>

                    <div class="row">
                        <div class="col-md-8 border-end-md pe-md-4 mb-3 mb-md-0">
                            <div class="mb-3 small text-secondary">
                                <div class="mb-1"><i class="bi bi-geo-alt me-1"></i> Giao đến: <strong class="text-dark"><?= htmlspecialchars($order['street_address']) ?></strong></div>
                                <div><i class="bi bi-credit-card me-1"></i> Thanh toán: <strong class="text-dark"><?= ($order['payment_method_id'] ?? 1) == 2 ? 'VNPAY' : 'COD' ?></strong></div>
                            </div>

                            <div class="border-top pt-3">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/50') ?>" class="border rounded object-fit-cover" style="width: 50px; height: 50px;">
                                        <div class="flex-grow-1">
                                            <a href="index.php?controller=listing&action=detail&id=<?= $item['listing_id'] ?>" class="text-dark text-decoration-none fw-semibold">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </a>
                                            <div class="text-muted small">SL: <?= $item['quantity'] ?> x <?= number_format($item['unit_price'], 0, ',', '.') ?>đ</div>
                                        </div>
                                        
                                        <?php if ($order['status_id'] == 5): ?>
                                            <?php if ($item['is_reviewed'] == 0): ?>
                                                <button class="btn btn-outline-warning btn-sm py-1 px-2 text-dark fw-bold" style="font-size: 0.75rem;" onclick="openReviewModal(<?= $item['listing_id'] ?>, '<?= htmlspecialchars($item['title']) ?>', <?= $order['id'] ?>)">Đánh giá</button>
                                            <?php else: ?>
                                                <span class="badge bg-light text-success border"><i class="bi bi-check-all"></i> Đã Đánh Giá</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                       <div class="col-md-4 ps-md-4 text-md-end d-flex flex-column justify-content-center">
                            <div class="text-muted small">Thành tiền:</div>
                            <div class="fw-bold mb-3" style="font-size: 1.5rem; color: #FF7A3D;">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?> đ
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if (in_array($order['status_id'], [1, 2])): ?>
                                    <?php if ($order['status_id'] == 1): ?>
                                        <button class="btn btn-outline-primary btn-sm rounded-3 px-3" onclick="openUpdateModal(<?= $order['id'] ?>, '<?= htmlspecialchars($order['street_address']) ?>', '<?= htmlspecialchars($order['shipping_note'] ?? '') ?>')">Cập nhật thông tin đơn hàng</button>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-danger btn-sm rounded-3 px-3" onclick="openCancelModal(<?= $order['id'] ?>)">Hủy</button>
                                <?php elseif ($order['status_id'] == 4): ?>
                                    <form action="index.php?controller=order&action=confirmReceived" method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3 fw-bold" onclick="return confirm('Xác nhận đã nhận hàng thành công?');">Đã nhận hàng</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="updateAddressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background-color: #FF7A3D; color: #fff;">
                <h5 class="modal-title fw-bold">Cập nhật thông tin nhận hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?controller=order&action=updateShippingInfo" method="POST" id="updateOrderForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="order_id" id="updateOrderId" value="">
                    <input type="hidden" name="street_address" id="fullAddressInput">

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Tỉnh / Thành phố *</label>
                            <select id="updProvince" class="form-select" onchange="loadUpdDistricts()" required>
                                <option value="">-- Chọn Tỉnh/Thành phố --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Quận / Huyện *</label>
                            <select id="updDistrict" class="form-select" onchange="loadUpdWards()" disabled required>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Phường / Xã *</label>
                            <select id="updWard" class="form-select" disabled required>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Số nhà, tên đường *</label>
                            <input type="text" id="updStreet" class="form-control" placeholder="Ví dụ: 730 Sư Vạn Hạnh" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Ghi chú giao hàng</label>
                        <textarea name="shipping_note" id="updateOrderNote" class="form-control" rows="2" placeholder="Lời nhắn cho người bán..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-sm text-white fw-bold rounded-pill px-4" style="background-color: #FF7A3D;">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="index.php?controller=order&action=cancelOrder" method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Xác nhận hủy đơn hàng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do hủy đơn</label>
                        <select name="cancel_reason" class="form-select" required>
                            <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
                            <option value="Tìm thấy giá rẻ hơn chỗ khác">Tìm thấy giá rẻ hơn chỗ khác</option>
                            <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                            <option value="Lý do khác">Lý do khác...</option>
                        </select>
                    </div>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> Lưu ý: Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Không</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="index.php?controller=order&action=submitReview" method="POST">
                <div class="modal-header" style="background-color: #FF7A3D; color: white;">
                    <h5 class="modal-title fw-bold">Đánh giá sản phẩm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p id="reviewProdTitle" class="fw-bold text-dark mb-3 border-bottom pb-2"></p>
                    
                    <input type="hidden" name="listing_id" id="reviewListingId">
                    <input type="hidden" name="order_id" id="reviewOrderId"> 
                    
                    <div class="mb-3 text-center">
                        <label class="form-label fw-semibold d-block">Chất lượng (1-5 sao)</label>
                        <input type="number" name="rating" class="form-control text-center mx-auto fs-4 fw-bold text-warning" style="max-width: 100px;" min="1" max="5" value="5" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Nhận xét của bạn</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Sản phẩm dùng tốt không cậu?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/user-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let uModal, cModal, rModal;
    document.addEventListener('DOMContentLoaded', () => {
        uModal = new bootstrap.Modal(document.getElementById('updateAddressModal'));
        cModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        rModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    });

    async function openUpdateModal(id, addr, note) {
        document.getElementById('updateOrderId').value = id;
        document.getElementById('updateOrderNote').value = note; 
        uModal.show(); 

        let provSelect = document.getElementById('updProvince');
        if (provSelect.options.length <= 1) { 
            try {
                let res = await fetch('index.php?controller=checkout&action=getProvinces');
                let data = await res.json();
                let html = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
                data.forEach(p => html += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`);
                provSelect.innerHTML = html;
            } catch (e) { console.error('Lỗi load tỉnh thành'); }
        }
    }

    async function loadUpdDistricts() {
        let provId = document.getElementById('updProvince').value;
        let distSelect = document.getElementById('updDistrict');
        let wardSelect = document.getElementById('updWard');
        
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>'; 
        wardSelect.disabled = true;
        
        if(!provId) { 
            distSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>'; 
            distSelect.disabled = true; 
            return; 
        }
        
        let res = await fetch(`index.php?controller=checkout&action=getDistricts&province_id=${provId}`);
        let data = await res.json();
        let html = '<option value="">-- Chọn Quận/Huyện --</option>';
        data.forEach(d => html += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`);
        distSelect.innerHTML = html; distSelect.disabled = false;
    }

    async function loadUpdWards() {
        let distId = document.getElementById('updDistrict').value;
        let wardSelect = document.getElementById('updWard');
        
        if(!distId) { 
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>'; 
            wardSelect.disabled = true; 
            return; 
        }
        
        let res = await fetch(`index.php?controller=checkout&action=getWards&district_id=${distId}`);
        let data = await res.json();
        let html = '<option value="">-- Chọn Phường/Xã --</option>';
        data.forEach(w => html += `<option value="${w.id}" data-name="${w.name}">${w.name}</option>`);
        wardSelect.innerHTML = html; wardSelect.disabled = false;
    }

    document.getElementById('updateOrderForm').addEventListener('submit', function(e) {
        let provSel = document.getElementById('updProvince');
        let distSel = document.getElementById('updDistrict');
        let wardSel = document.getElementById('updWard');
        let street  = document.getElementById('updStreet').value.trim();

        if (provSel.selectedIndex <= 0 || distSel.selectedIndex <= 0 || wardSel.selectedIndex <= 0 || !street) {
            e.preventDefault(); alert("Vui lòng chọn đầy đủ thông tin địa chỉ!"); return false;
        }

        let fullAddr = street + ', ' + wardSel.options[wardSel.selectedIndex].dataset.name + ', ' + distSel.options[distSel.selectedIndex].dataset.name + ', ' + provSel.options[provSel.selectedIndex].dataset.name;
        document.getElementById('fullAddressInput').value = fullAddr;
    });

    function openCancelModal(id) { document.getElementById('cancelOrderId').value = id; cModal.show(); }

    function openReviewModal(listingId, prodTitle, orderId) {
        document.getElementById('reviewListingId').value = listingId;
        document.getElementById('reviewOrderId').value = orderId; 
        document.getElementById('reviewProdTitle').textContent = prodTitle;
        rModal.show();
    }

    const toastEl = document.getElementById('toastMessage');
    if (toastEl) {
        setTimeout(() => toastEl.classList.add('show-toast'), 100);
        setTimeout(() => { toastEl.style.opacity = '0'; setTimeout(() => toastEl.remove(), 400); }, 2500);
    }
</script>
</body>
</html>