<?php
// 1. GỌI HEADER VÀO ĐÂY (Điều chỉnh lại đường dẫn '../layout/header.php' cho khớp với thư mục của cậu nhé)
require_once __DIR__ . '/../partials/user-header.php';
?>

<main class="container-fluid px-3 px-md-4" style="max-width:1140px;padding-top:28px;padding-bottom:40px">
    <nav style="font-size:13px;color:var(--text-secondary);margin-bottom:16px">
        <a href="index.php?controller=home" style="color:var(--text-secondary);text-decoration:none">Trang chủ</a>
        <span style="margin:0 6px">/</span>
        <span style="color: var(--text-primary); font-weight: 600;">Giỏ hàng</span>
    </nav>

    <h1 class="page-title">
        <i class="bi bi-cart3 me-2" style="color:var(--btn-primary)"></i>Giỏ hàng của bạn
    </h1>

    <?php if(empty($cartItems)): ?>
        <div class="text-center py-5 shadow-sm" style="background: #fff; border: 1px solid var(--border-color); border-radius:16px;">
            <i class="bi bi-cart-x text-muted" style="font-size:5rem;"></i>
            <h4 class="mt-3 fw-bold">Giỏ hàng của bạn đang trống</h4>
            <p class="text-secondary">Hãy tìm thêm những món đồ ưng ý và lấp đầy giỏ hàng nhé!</p>
            <a href="index.php?controller=home" class="btn-2life-primary mt-3 px-4 py-2 fs-6" style="display:inline-block; text-decoration:none; width: auto;">Đi mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3" style="font-size:13px;color:var(--text-secondary)">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)" style="width:15px;height:15px;accent-color:var(--btn-primary)">
                        <span>Chọn tất cả (<span id="tongSoItem"><?= count($cartItems) ?></span> sản phẩm)</span>
                    </label>
                    <button onclick="removeSelected()" style="background:none;border:none;color:var(--error-color);font-size:13px;cursor:pointer;padding:0">
                        <i class="bi bi-trash me-1"></i>Xóa đã chọn
                    </button>
                </div>

                <div id="cart-items-list">
                    <?php foreach($cartItems as $sp): ?>
                    <div class="cart-item" id="item-<?= $sp['listing_id'] ?>" style="transition: all 0.3s ease;">
                        <input type="checkbox" class="item-check" value="<?= $sp['listing_id'] ?>" style="width:15px;height:15px;accent-color:var(--btn-primary);flex-shrink:0" onchange="updateSummary()">
                        
                        <img src="<?= htmlspecialchars($sp['image_url'] ?? 'https://via.placeholder.com/200') ?>" alt="Product Image">
                        
                        <div class="item-info" style="flex: 1;">
                            <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 6px 0; color: var(--text-primary);"><?= htmlspecialchars($sp['title']) ?></h3>
                            <p class="seller-name" style="font-size: 13px; color: var(--text-secondary); margin: 0 0 4px 0;"><i class="bi bi-person-circle me-1"></i>Người bán: <?= htmlspecialchars($sp['seller_name']) ?></p>
                            <span style="font-size:11px;background:#E8F5E9;color:#388E3C;padding:2px 8px;border-radius:4px;margin-top:4px;display:inline-block">Kho: <?= $sp['stock_quantity'] ?></span>
                        </div>
                        
                        <div class="item-quantity">
                            <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, -1)">−</button>
                            <input type="text" value="<?= $sp['quantity'] ?>" class="qty-input" id="qty-<?= $sp['listing_id'] ?>" data-dongia="<?= $sp['price_snapshot'] ?>" readonly>
                            <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, 1)">+</button>
                        </div>
                        
                        <div class="item-price" id="price-<?= $sp['listing_id'] ?>">
                            <?= number_format($sp['price_snapshot'] * $sp['quantity'], 0, ',', '.') ?>đ
                        </div>
                        
                        <button class="btn-remove" style="background: none; border: none; color: var(--error-color); cursor: pointer; padding: 8px; border-radius: 8px; transition: 0.2s;" onclick="removeItem(<?= $sp['listing_id'] ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="cart-summary">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary);"><i class="bi bi-receipt me-2" style="color:var(--btn-secondary)"></i>Tóm tắt đơn hàng</h3>

                    <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 14px; color: var(--text-secondary);">
                        <span>Tạm tính (<span id="itemCount">0</span> sản phẩm):</span>
                        <span id="subtotal">0đ</span>
                    </div>
                    <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 14px; color: var(--text-secondary);">
                        <span>Phí vận chuyển:</span>
                        <span style="color:var(--btn-secondary);font-weight:600">Thỏa thuận</span>
                    </div>
                    <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 14px; color: var(--text-secondary);">
                        <span>Giảm giá:</span>
                        <span style="color:#388E3C;font-weight:600">—</span>
                    </div>

                    <div class="summary-row summary-total" style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 18px; margin-top: 18px; font-weight: 700; font-size: 16px; color: var(--text-primary);">
                        <span>Tổng cộng:</span>
                        <span class="total-price" id="totalPrice" style="font-size: 24px; color: var(--btn-primary); font-weight: 700;">0đ</span>
                    </div>

                    <div class="d-flex gap-2 mt-4 mb-3">
                        <input type="text" placeholder="Nhập mã giảm giá..." style="flex:1;padding:9px 13px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;outline:none;color:var(--text-primary)">
                        <button class="btn-2life-secondary" style="border-radius:8px;padding:9px 14px;font-size:13px;white-space:nowrap; background-color: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); font-weight: 600;">Áp dụng</button>
                    </div>

                    <a href="index.php?controller=checkout" class="btn-2life-primary" style="display: block; background-color: var(--btn-primary); color: #fff; border: none; border-radius: 12px; padding: 14px; font-weight: 700; width: 100%; text-align: center; text-decoration: none; cursor: pointer;">
                        <i class="bi bi-bag-check-fill me-2"></i>Đặt hàng
                    </a>
                    
                    <a href="index.php?controller=home" style="display:block;text-align:center;margin-top:14px;font-size:13px;color:var(--btn-secondary);text-decoration:none">
                        <i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm
                    </a>
                    
                    <p class="security-note" style="font-size: 12px; color: var(--text-secondary); text-align: center; margin-top: 14px;">
                        <i class="bi bi-shield-check me-1" style="color:var(--btn-primary)"></i>
                        Vui lòng kiểm tra kỹ trước khi nhấn Đặt hàng nhé!
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
async function sendCartActionToDB(payload) {
    try {
        const response = await fetch('index.php?controller=cart&action=updateAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.status !== 'success') {
            alert('Lỗi từ hệ thống: ' + result.msg);
            return false;
        }
        return result;
    } catch (error) {
        console.error('Lỗi API:', error);
        return false;
    }
}

async function changeQty(btn, listingId, delta) {
    const input = document.getElementById('qty-' + listingId);
    let currentQty = parseInt(input.value);
    let newQty = currentQty + delta;
    if (newQty <= 0) {
        removeItem(listingId);
        return;
    }
    let success = await sendCartActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    if (success) {
        input.value = newQty;
        const priceEl = document.getElementById('price-' + listingId);
        const unitPrice = parseInt(input.dataset.dongia);
        priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
        updateSummary();
    }
}

async function removeItem(listingId) {
    if (confirm("Cậu chắc chắn muốn bỏ sản phẩm này khỏi giỏ hàng?")) {
        let success = await sendCartActionToDB({ action: 'remove', listingId: listingId });
        if (success) {
            const item = document.getElementById('item-' + listingId);
            item.style.opacity = '0';
            item.style.transform = 'translateX(30px)';
            setTimeout(() => { 
                item.remove(); 
                updateSummary(); 
                if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
            }, 300);
        }
    }
}

function toggleAll(master) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = master.checked);
    updateSummary();
}

async function removeSelected() {
    const selectedChecks = document.querySelectorAll('.item-check:checked');
    if (selectedChecks.length === 0) { alert("Cậu chưa chọn sản phẩm nào để xóa!"); return; }
    if (confirm("Xóa toàn bộ các sản phẩm đã chọn?")) {
        for (let check of selectedChecks) {
            let listingId = check.value;
            await sendCartActionToDB({ action: 'remove', listingId: listingId });
            document.getElementById('item-' + listingId).remove();
        }
        updateSummary();
        if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
    }
}

function updateSummary() {
    const items = document.querySelectorAll('.cart-item');
    let total = 0; let count = 0;
    items.forEach(item => {
        const check = item.querySelector('.item-check');
        if (check && check.checked) {
            const input = item.querySelector('.qty-input');
            const qty = parseInt(input.value);
            const price = parseInt(input.dataset.dongia);
            total += qty * price;
            count++;
        }
    });
    document.getElementById('itemCount').textContent = count;
    document.getElementById('subtotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + 'đ';
    const tongSoItemEl = document.getElementById('tongSoItem');
    if (tongSoItemEl) tongSoItemEl.textContent = items.length;
}
</script>

<?php
// 2. GỌI FOOTER VÀO ĐÂY
require_once __DIR__ . '/../partials/user-footer.php';
?>