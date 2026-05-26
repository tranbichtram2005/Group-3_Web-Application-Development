<?php
// Guard tránh lỗi IDE
$orderCounts = $orderCounts ?? [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
$orders = $orders ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .nav-tabs-shopee { display: flex; list-style: none; padding: 0; margin-bottom: 20px; background: #fff; border-radius: 8px; overflow-x: auto; }
        .nav-tabs-shopee li { flex: 1; text-align: center; }
        .nav-tabs-shopee a { display: block; padding: 15px 10px; color: #555; text-decoration: none; font-weight: 500; border-bottom: 3px solid transparent; white-space: nowrap; transition: 0.3s; }
        .nav-tabs-shopee a:hover { color: #FF7A3D; }
        .nav-tabs-shopee a.active { color: #FF7A3D; border-bottom-color: #FF7A3D; }
        .order-card { background: #fff; border-radius: 8px; margin-bottom: 20px; padding: 20px; transition: transform 0.2s; }
        .order-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .order-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .status-badge { font-weight: 600; text-transform: uppercase; font-size: 14px; }
        .product-item { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        .order-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px; }
        
        /* TOAST THÔNG BÁO XANH CAM */
        .toast-2life { position: fixed; top: 30px; right: 30px; z-index: 10000; background-color: #4CAF50; border-left: 6px solid #FF7A3D; color: #fff; padding: 15px 25px; border-radius: 6px; font-weight: 600; font-size: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; transform: translateX(120%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease; }
        .toast-2life.show-toast { transform: translateX(0); }
    </style>
</head>
<body class="bg-light">
<?php include __DIR__ . '/../partials/user-header.php'; ?>

<?php if (isset($_SESSION['toast_msg'])): ?>
    <div id="toastMessage" class="toast-2life">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span><?= htmlspecialchars($_SESSION['toast_msg']) ?></span>
    </div>
    <?php unset($_SESSION['toast_msg']); ?>
<?php endif; ?>

<div class="container py-4">
    <h3 class="mb-4 fw-bold text-dark">Đơn mua của tôi</h3>

    <?php $currentStatus = isset($_GET['status']) ? (int)$_GET['status'] : 0; ?>
    <ul class="nav-tabs-shopee shadow-sm">
        <li><a href="index.php?controller=order" class="<?= $currentStatus === 0 ? 'active' : '' ?>">Tất cả (<?= $orderCounts[0] ?>)</a></li>
        <li><a href="index.php?controller=order&status=1" class="<?= $currentStatus === 1 ? 'active' : '' ?>">Chờ duyệt (<?= $orderCounts[1] ?>)</a></li>
        <li><a href="index.php?controller=order&status=2" class="<?= $currentStatus === 2 ? 'active' : '' ?>">Chờ chuẩn bị (<?= $orderCounts[2] ?>)</a></li>
        <li><a href="index.php?controller=order&status=3" class="<?= $currentStatus === 3 ? 'active' : '' ?>">Đang giao (<?= $orderCounts[3] ?>)</a></li>
        <li><a href="index.php?controller=order&status=4" class="<?= $currentStatus === 4 ? 'active' : '' ?>">Hoàn thành (<?= $orderCounts[4] ?>)</a></li>
        <li><a href="index.php?controller=order&status=5" class="<?= $currentStatus === 5 ? 'active' : '' ?>">Trả hàng (<?= $orderCounts[5] ?>)</a></li>
        <li><a href="index.php?controller=order&status=6" class="<?= $currentStatus === 6 ? 'active' : '' ?>">Đã hủy (<?= $orderCounts[6] ?>)</a></li>
    </ul>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm border">
            <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
            <p class="mt-3 text-muted fs-5">Chưa có đơn hàng nào!</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $statusUI = ['icon' => 'bi-info-circle', 'text' => $order['status_name'], 'color' => '#6c757d'];
            switch ($order['status_id']) {
                case 1: $statusUI = ['icon' => 'bi-hourglass-split', 'text' => 'CHỜ PHÊ DUYỆT', 'color' => '#ffc107']; break;
                case 2: $statusUI = ['icon' => 'bi-box-seam', 'text' => 'CHỜ CHUẨN BỊ', 'color' => '#0dcaf0']; break;
                case 3: $statusUI = ['icon' => 'bi-truck', 'text' => 'ĐANG GIAO HÀNG', 'color' => '#0d6efd']; break;
                case 4: $statusUI = ['icon' => 'bi-check-circle-fill', 'text' => 'HOÀN THÀNH', 'color' => '#198754']; break;
                case 5: $statusUI = ['icon' => 'bi-arrow-return-left', 'text' => 'TRẢ HÀNG / HOÀN TIỀN', 'color' => '#fd7e14']; break;
                case 6: $statusUI = ['icon' => 'bi-x-circle-fill', 'text' => 'ĐÃ HỦY', 'color' => '#dc3545']; break;
            }
        ?>
            <div class="order-card shadow-sm border">
                <div class="order-header">
                    <span class="text-secondary fw-semibold">Mã đơn hàng: #2L<?= $order['id'] ?></span>
                    <span class="status-badge" style="color: <?= $statusUI['color'] ?>;">
                        <i class="bi <?= $statusUI['icon'] ?> me-1"></i><?= $statusUI['text'] ?>
                    </span>
                </div>

                <?php foreach ($order['items'] as $item): ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/80') ?>" class="product-img" alt="Product Image">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-dark"><?= htmlspecialchars($item['title']) ?></h6>
                            <span class="text-muted small">Số lượng: x<?= $item['quantity'] ?></span>
                        </div>
                        <div class="text-end">
                            <span class="text-dark fw-medium"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</span>
                            
                            <?php if ($order['status_id'] == 4): ?>
                                <br>
                                <?php if ($item['is_reviewed'] == 0): ?>
                                    <button class="btn btn-warning btn-sm mt-2" onclick="openReviewModal(<?= $item['listing_id'] ?>, '<?= htmlspecialchars($item['title']) ?>', <?= $order['id'] ?>)">
                                        <i class="bi bi-star-fill me-1"></i>Đánh giá sản phẩm
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm mt-2" disabled>
                                        <i class="bi bi-check-all me-1"></i>Đã đánh giá
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="order-footer">
                    <div>
                        <span class="text-muted d-block small mb-1">Địa chỉ giao: <?= htmlspecialchars($order['street_address']) ?></span>
                        <span class="text-muted d-block small">Thành tiền: <strong class="text-danger fs-5"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong></span>
                    </div>
                    <div>
                        <?php if (in_array($order['status_id'], [1, 2])): ?>
                            <?php if ($order['status_id'] == 1): ?>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="openUpdateModal(<?= $order['id'] ?>, '<?= htmlspecialchars($order['street_address']) ?>', '<?= htmlspecialchars($order['shipping_note']) ?>')">Cập nhật thông tin</button>
                            <?php endif; ?>
                            <button class="btn btn-outline-danger btn-sm" onclick="openCancelModal(<?= $order['id'] ?>)">Hủy đơn hàng</button>
                        <?php elseif ($order['status_id'] == 3): ?>
                            <form action="index.php?controller=order&action=confirmReceived" method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Đã nhận được hàng</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?controller=order&action=updateShippingInfo" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật thông tin nhận hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng mới</label>
                        <input type="text" name="street_address" id="updateAddress" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú giao hàng</label>
                        <textarea name="shipping_note" id="updateNote" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?controller=order&action=cancelOrder" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="mb-3">
                        <label class="form-label">Lý do hủy đơn</label>
                        <select name="cancel_reason" class="form-select" required>
                            <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
                            <option value="Tìm thấy giá rẻ hơn chỗ khác">Tìm thấy giá rẻ hơn chỗ khác</option>
                            <option value="Đổi ý, không muốn mua nữa">Đổi ý, không muốn mua nữa</option>
                            <option value="Lý do khác">Lý do khác...</option>
                        </select>
                    </div>
                    <p class="text-danger small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Lưu ý: Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?controller=order&action=submitReview" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Đánh giá sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="reviewProdTitle" class="fw-bold text-success mb-3"></p>
                    
                    <input type="hidden" name="listing_id" id="reviewListingId">
                    <input type="hidden" name="order_id" id="reviewOrderId"> 
                    
                    <div class="mb-3">
                        <label class="form-label">Chất lượng (1-5 sao)</label>
                        <input type="number" name="rating" class="form-control" min="1" max="5" value="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhận xét của bạn</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Sản phẩm dùng tốt không cậu?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Gửi đánh giá</button>
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
        uModal = new bootstrap.Modal(document.getElementById('updateModal'));
        cModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        rModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    });

    function openUpdateModal(id, addr, note) {
        document.getElementById('updateOrderId').value = id;
        document.getElementById('updateAddress').value = addr;
        document.getElementById('updateNote').value = note;
        uModal.show();
    }

    function openCancelModal(id) {
        document.getElementById('cancelOrderId').value = id;
        cModal.show();
    }

    function openReviewModal(listingId, prodTitle, orderId) {
        document.getElementById('reviewListingId').value = listingId;
        document.getElementById('reviewOrderId').value = orderId; 
        document.getElementById('reviewProdTitle').textContent = 'Đánh giá: ' + prodTitle;
        rModal.show();
    }

    // Xử lý Hộp thoại thông báo nổi (Toast)
    const toastEl = document.getElementById('toastMessage');
    if (toastEl) {
        setTimeout(() => toastEl.classList.add('show-toast'), 100);
        setTimeout(() => {
            toastEl.style.opacity = '0';
            setTimeout(() => toastEl.remove(), 400); 
        }, 2500);
    }
</script>
</body>
</html>