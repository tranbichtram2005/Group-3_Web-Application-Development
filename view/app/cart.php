<?php
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
            <a href="index.php?controller=home" class="btn-2life-primary mt-3 px-4 py-2 fs-6" style="display:inline-block; text-decoration:none; width: auto; border-radius:8px;">Đi mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3" style="font-size:13px;color:var(--text-secondary)">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)" style="width:16px;height:16px;accent-color:var(--btn-primary)">
                        <span>Chọn tất cả (<span id="tongSoItem"><?= count($cartItems) ?></span> sản phẩm)</span>
                    </label>
                    <button onclick="removeSelected()" style="background:none;border:none;color:var(--error-color);font-size:13px;cursor:pointer;padding:0;font-weight:600">
                        <i class="bi bi-trash me-1"></i>Xóa đã chọn
                    </button>
                </div>

                <div id="cart-items-list">
                    <?php foreach($cartItems as $sp): ?>
                    <div class="cart-item" id="item-<?= $sp['listing_id'] ?>" style="transition: all 0.3s ease;">
                        <input type="checkbox" class="item-check" value="<?= $sp['listing_id'] ?>" 
                               data-stock="<?= (int)$sp['stock_quantity'] ?>"
                               onchange="updateSummary(); updateOrderBtn()">
                        
                        <img src="<?= htmlspecialchars($sp['image_url'] ?? 'https://via.placeholder.com/200') ?>" alt="Product Image">
                        
                        <div class="item-info">
                            <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 6px 0; color: var(--text-primary); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($sp['title']) ?></h3>
                            
                            <?php if(!empty($sp['offer_id'])): ?>
                                <div style="margin-bottom: 6px;">
                                    <span class="badge bg-danger px-2 py-1" style="font-size: 10px; border-radius: 4px; font-weight: 600;">
                                        <i class="bi bi-tags-fill me-1"></i> Giá Deal
                                    </span>
                                </div>
                            <?php endif; ?>
                            <p class="seller-name" style="font-size: 12px; color: var(--text-secondary); margin: 0 0 4px 0;"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($sp['seller_name']) ?></p>
                            <span style="font-size:11px;background:#E8F5E9;color:#388E3C;padding:2px 8px;border-radius:4px;display:inline-block">Kho: <?= $sp['stock_quantity'] ?></span>
                        </div>
                        
                        <div class="item-bottom-row">
                            <div class="item-qty-wrapper">
                                <div class="item-quantity">
                                    <?php if(!empty($sp['offer_id'])): ?>
                                        <button class="qty-btn" disabled>−</button>
                                        <input type="text" value="<?= $sp['quantity'] ?>" class="qty-input" 
                                               id="qty-<?= $sp['listing_id'] ?>" 
                                               data-dongia="<?= $sp['price_snapshot'] ?>"
                                               data-stock="<?= (int)$sp['stock_quantity'] ?>"
                                               readonly style="background:#fff3cd; color:#dc3545;">
                                        <button class="qty-btn" disabled>+</button>
                                    <?php else: ?>
                                        <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, -1)">−</button>
                                        <input type="number" value="<?= $sp['quantity'] ?>" class="qty-input" 
                                               id="qty-<?= $sp['listing_id'] ?>" 
                                               data-dongia="<?= $sp['price_snapshot'] ?>"
                                               data-stock="<?= (int)$sp['stock_quantity'] ?>"
                                               onchange="manualChangeQty(this, <?= $sp['listing_id'] ?>)"
                                               style="-moz-appearance: textfield;">
                                        <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, 1)">+</button>
                                    <?php endif; ?>
                                </div>
                                <?php if(!empty($sp['offer_id'])): ?>
                                    <div style="font-size: 11px; color: var(--error-color); margin-top: 4px; font-weight: 500;">
                                        <i class="bi bi-lock-fill"></i> Slg Deal cố định
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="item-price" id="price-<?= $sp['listing_id'] ?>">
                                <?= number_format($sp['price_snapshot'] * $sp['quantity'], 0, ',', '.') ?>đ
                            </div>
                        </div>
                        
                        <button class="btn-remove" onclick="removeItem(<?= $sp['listing_id'] ?>)"><i class="bi bi-trash fs-5"></i></button>
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

                    <div class="summary-row" id="voucher-discount-row" style="display: none; justify-content: space-between; margin-bottom: 14px; font-size: 14px; color: var(--text-secondary);">
                        <span>Giảm giá (<span id="voucher-code-label" style="color:var(--btn-primary);font-weight:600"></span>):</span>
                        <span id="voucher-discount-amount" style="color:#388E3C;font-weight:600">—</span>
                    </div>

                    <div class="summary-row summary-total" style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 18px; margin-top: 18px; font-weight: 700; font-size: 16px; color: var(--text-primary);">
                        <span>Tổng cộng:</span>
                        <span class="total-price" id="totalPrice" style="font-size: 22px; color: var(--btn-primary); font-weight: 700;">0đ</span>
                    </div>
                    <p style="font-size:11px;color:var(--text-secondary);text-align:right;margin-top:4px;margin-bottom:0">
                        <i class="bi bi-info-circle me-1"></i>Chưa bao gồm phí vận chuyển
                    </p>

                    <div class="mt-3 mb-3">
                        <div class="d-flex gap-2">
                            <input type="text" id="voucher-input" placeholder="Nhập mã giảm giá..." style="flex:1;padding:10px 13px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;outline:none;color:var(--text-primary);text-transform:uppercase">
                            <button onclick="applyVoucher()" id="voucher-btn" style="border-radius:8px;padding:10px 14px;font-size:13px;white-space:nowrap;background-color:var(--bg-main);color:var(--text-primary);border:1px solid var(--border-color);font-weight:600;cursor:pointer">Áp dụng</button>
                        </div>
                        <div id="voucher-msg" style="font-size:12px;margin-top:6px;min-height:18px;"></div>
                    </div>

                    <form id="checkoutForm" action="index.php?controller=checkout" method="POST">
                        <input type="hidden" name="selected_ids" id="selectedIdsInput" value="">
                        <button type="button" id="orderBtn" onclick="goToCheckout()" 
                                class="btn-2life-primary" 
                                disabled
                                style="display: block; background-color: #ccc; color: #fff; border: none; border-radius: 8px; padding: 14px; font-weight: 700; width: 100%; text-align: center; cursor: not-allowed; transition: 0.2s;">
                            <i class="bi bi-bag-check-fill me-2"></i>Đặt hàng
                        </button>
                    </form>
                    
                    <div id="noSelectMsg" style="font-size:12px;color:var(--error-color);text-align:center;margin-top:8px;min-height:16px;">
                        Vui lòng chọn ít nhất 1 sản phẩm để đặt hàng
                    </div>
                    
                    <a href="index.php?controller=home" style="display:block;text-align:center;margin-top:14px;font-size:13px;color:var(--btn-secondary);text-decoration:none">
                        <i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
let appliedDiscount  = 0;
let appliedVoucherId = null;

async function sendCartActionToDB(payload) {
    try {
        const response = await fetch('index.php?controller=cart&action=updateAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.status !== 'success') {
            Swal.fire({ icon: 'error', title: 'Úi, có lỗi!', text: result.msg, confirmButtonColor: '#FF7A3D' });
            return false;
        }
        return result;
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Lỗi kết nối', text: 'Vui lòng kiểm tra lại mạng!', confirmButtonColor: '#FF7A3D' });
        return false;
    }
}

async function changeQty(btn, listingId, delta) {
    let container = btn.closest('.item-quantity');
    container.style.pointerEvents = 'none'; 
    container.style.opacity = '0.5';

    const input  = document.getElementById('qty-' + listingId);
    let currentQty = parseInt(input.value);
    let newQty     = currentQty + delta;
    const stock    = parseInt(input.dataset.stock);

    if (newQty <= 0) {
        container.style.pointerEvents = 'auto'; container.style.opacity = '1';
        removeItem(listingId); return;
    }
    if (newQty > stock) {
        showToast(`Chỉ còn ${stock} sản phẩm trong kho!`, 'error');
        container.style.pointerEvents = 'auto'; container.style.opacity = '1'; return;
    }

    input.value = newQty;
    const priceEl  = document.getElementById('price-' + listingId);
    const unitPrice = parseInt(input.dataset.dongia);
    priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
    updateSummary();

    let success = await sendCartActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    if (success) {
        resetVoucher(); updateOrderBtn();
    } else { 
        input.value = currentQty;
        priceEl.textContent = (unitPrice * currentQty).toLocaleString('vi-VN') + 'đ'; updateSummary();
    }
    container.style.pointerEvents = 'auto'; container.style.opacity = '1';
}

async function manualChangeQty(input, listingId) {
    let container = input.closest('.item-quantity');
    container.style.pointerEvents = 'none';
    container.style.opacity = '0.5';

    let newQty = parseInt(input.value);
    const stock = parseInt(input.dataset.stock);

    if (isNaN(newQty) || newQty <= 0) {
        container.style.pointerEvents = 'auto'; container.style.opacity = '1';
        removeItem(listingId); return;
    }

    if (newQty > stock) {
        showToast(`Chỉ còn ${stock} sản phẩm trong kho!`, 'error');
        newQty = stock; 
        input.value = newQty;
    }

    const priceEl  = document.getElementById('price-' + listingId);
    const unitPrice = parseInt(input.dataset.dongia);
    priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
    updateSummary();

    let success = await sendCartActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    if (success) {
        resetVoucher(); updateOrderBtn();
    } else {
        window.location.reload(); 
    }
    container.style.pointerEvents = 'auto'; container.style.opacity = '1';
}

function removeItem(listingId) {
    Swal.fire({
        title: 'Xóa sản phẩm?',
        text: "Cậu có chắc chắn muốn bỏ sản phẩm này khỏi giỏ hàng không?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Giữ lại'
    }).then(async (result) => {
        if (result.isConfirmed) {
            let success = await sendCartActionToDB({ action: 'remove', listingId: listingId });
            if (success) {
                const item = document.getElementById('item-' + listingId);
                item.style.opacity = '0';
                item.style.transform = 'translateX(30px)';
                setTimeout(() => { 
                    item.remove();
                    resetVoucher();
                    updateSummary();
                    updateOrderBtn();
                    if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
                }, 300);
            }
        }
    });
}

function toggleAll(master) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = master.checked);
    updateSummary();
    updateOrderBtn();
}

function removeSelected() {
    const selectedChecks = document.querySelectorAll('.item-check:checked');
    if (selectedChecks.length === 0) { 
        Swal.fire({ icon: 'info', title: 'Ê khoan!', text: 'Cậu chưa chọn sản phẩm nào để xóa cả!', confirmButtonColor: '#FF7A3D' });
        return; 
    }
    
    Swal.fire({
        title: 'Xóa hàng loạt?',
        text: "Cậu chắc chắn muốn xóa toàn bộ các sản phẩm đã chọn chứ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý xóa hết',
        cancelButtonText: 'Hủy'
    }).then(async (result) => {
        if (result.isConfirmed) {
            for (let check of selectedChecks) {
                let listingId = check.value;
                await sendCartActionToDB({ action: 'remove', listingId: listingId });
                document.getElementById('item-' + listingId).remove();
            }
            resetVoucher();
            updateSummary();
            updateOrderBtn();
            if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
        }
    });
}

function updateSummary() {
    const items = document.querySelectorAll('.cart-item');
    let subtotal = 0; let count = 0;
    items.forEach(item => {
        const check = item.querySelector('.item-check');
        if (check && check.checked) {
            const input = item.querySelector('.qty-input');
            const qty   = parseInt(input.value);
            const price = parseInt(input.dataset.dongia);
            subtotal += qty * price;
            count++;
        }
    });

    document.getElementById('itemCount').textContent = count;
    document.getElementById('subtotal').textContent   = subtotal.toLocaleString('vi-VN') + 'đ';

    const finalTotal = Math.max(0, subtotal - appliedDiscount);
    document.getElementById('totalPrice').textContent  = finalTotal.toLocaleString('vi-VN') + 'đ';

    const tongSoItemEl = document.getElementById('tongSoItem');
    if (tongSoItemEl) tongSoItemEl.textContent = items.length;
}

function updateOrderBtn() {
    const btn     = document.getElementById('orderBtn');
    const msgEl   = document.getElementById('noSelectMsg');
    const checked = document.querySelectorAll('.item-check:checked');
    
    let subtotal = 0;
    checked.forEach(check => {
        const item  = check.closest('.cart-item');
        const input = item.querySelector('.qty-input');
        subtotal += parseInt(input.value) * parseInt(input.dataset.dongia);
    });

    if (checked.length > 0 && subtotal > 0) {
        btn.disabled = false;
        btn.style.backgroundColor  = 'var(--btn-primary)';
        btn.style.cursor           = 'pointer';
        msgEl.style.display        = 'none';
    } else {
        btn.disabled = true;
        btn.style.backgroundColor  = '#ccc';
        btn.style.cursor           = 'not-allowed';
        msgEl.style.display        = 'block';
        msgEl.textContent          = checked.length === 0
            ? 'Vui lòng chọn ít nhất 1 sản phẩm để đặt hàng'
            : 'Tổng đơn hàng không hợp lệ';
    }
}

function goToCheckout() {
    const checked = document.querySelectorAll('.item-check:checked');
    if (checked.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Khoan đã!', text: 'Cậu chưa chọn sản phẩm nào để đặt hàng cả!', confirmButtonColor: '#FF7A3D' });
        return;
    }
    const ids = Array.from(checked).map(c => c.value).join(',');
    document.getElementById('selectedIdsInput').value = ids;
    document.getElementById('checkoutForm').submit();
}

async function applyVoucher() {
    const code  = document.getElementById('voucher-input').value.trim();
    const btn   = document.getElementById('voucher-btn');

    if (!code) {
        showVoucherMsg('Vui lòng nhập mã voucher.', 'error');
        return;
    }

    let subtotal = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const check = item.querySelector('.item-check');
        if (check && check.checked) {
            const input = item.querySelector('.qty-input');
            subtotal += parseInt(input.value) * parseInt(input.dataset.dongia);
        }
    });

    if (subtotal === 0) {
        showVoucherMsg('Vui lòng chọn ít nhất 1 sản phẩm trước khi áp voucher.', 'error');
        return;
    }

    btn.disabled = true; btn.textContent = '...';

    try {
        const res  = await fetch('index.php?controller=cart&action=applyVoucher', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: code, orderTotal: subtotal })
        });
        const data = await res.json();

        if (data.status === 'success') {
            appliedDiscount  = data.discount;
            appliedVoucherId = data.voucherId;
            document.getElementById('voucher-discount-row').style.display = 'flex';
            document.getElementById('voucher-code-label').textContent     = code.toUpperCase();
            document.getElementById('voucher-discount-amount').textContent = data.discountFormat;
            btn.textContent   = 'Bỏ';
            btn.onclick       = resetVoucher;
            btn.style.color   = 'var(--error-color)';
            document.getElementById('voucher-input').disabled = true;
            showVoucherMsg(data.msg, 'success');
            updateSummary();
        } else {
            showVoucherMsg(data.msg, 'error');
        }
    } catch (e) {
        showVoucherMsg('Lỗi kết nối, vui lòng thử lại.', 'error');
    }

    btn.disabled = false;
    if (appliedDiscount === 0) btn.textContent = 'Áp dụng';
}

function showVoucherMsg(msg, type) {
    const el = document.getElementById('voucher-msg');
    el.textContent = msg;
    el.style.color = type === 'success' ? '#388E3C' : 'var(--error-color)';
}

function resetVoucher() {
    appliedDiscount = 0; appliedVoucherId = null;
    document.getElementById('voucher-discount-row').style.display  = 'none';
    document.getElementById('voucher-discount-amount').textContent  = '—';
    document.getElementById('voucher-code-label').textContent       = '';
    document.getElementById('voucher-input').disabled               = false;
    document.getElementById('voucher-input').value                  = '';
    document.getElementById('voucher-msg').textContent              = '';
    const btn = document.getElementById('voucher-btn');
    btn.textContent = 'Áp dụng'; btn.onclick = applyVoucher; btn.style.color = 'var(--text-primary)';
    updateSummary();
}

function showToast(msg, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;color:#fff;background:${type==='error'?'#e53935':'#43a047'};box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:opacity 0.3s`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Khởi tạo
updateSummary();
updateOrderBtn();
</script>

<?php if(isset($expiredDealsCount) && $expiredDealsCount > 0): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'warning',
            title: 'Deal đã hết hạn!',
            text: 'Có <?= $expiredDealsCount ?> sản phẩm trong giỏ hàng đã quá hạn Deal 24h nên hệ thống đã tự động khôi phục về giá gốc.',
            confirmButtonColor: '#FF7A3D'
        });
    });
</script>
<?php endif; ?>

<?php
require_once __DIR__ . '/../partials/user-footer.php';
?>