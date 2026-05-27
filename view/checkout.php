<?php
$checkoutItems    = $checkoutItems  ?? [];
$buyerName        = $buyerName      ?? 'Người mua';
$buyerPhone       = $buyerPhone     ?? '';
$buyerProvince    = $buyerProvince  ?? 'Thành phố Hồ Chí Minh';
$buyerDistrict    = $buyerDistrict  ?? '';
$buyerWard        = $buyerWard      ?? '';
$buyerStreet      = $buyerStreet    ?? '';
$fullAddress      = $fullAddress    ?? '';
$shippingFee      = $shippingFee    ?? 30000;

$isDirectCheckout = $isDirectCheckout ?? false;
$directListingId  = $directListingId ?? 0;
$directQuantity   = $directQuantity ?? 0;

$totalMerchandise = 0;
foreach ($checkoutItems as $item) {
    $totalMerchandise += ($item['unit_price'] * $item['quantity']);
}
$totalFinal = $totalMerchandise + $shippingFee;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --bg-main: #f5f5f5; --btn-primary: #FF7A3D; --btn-secondary: #4DA8DA; --text-primary: #2B2B2B; }
        body { background-color: var(--bg-main); color: var(--text-primary); font-family: 'Inter', sans-serif; }
        .checkout-block { background-color: #fff; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,.05); padding: 24px 30px; margin-bottom: 16px; }
        .shopee-border { height: 3px; width: 100%; background-image: repeating-linear-gradient(45deg,#6fa6d6,#6fa6d6 33px,transparent 0,transparent 41px,#f18d9b 0,#f18d9b 74px,transparent 0,transparent 82px); margin-bottom: 10px; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e0e0e0; }
        .payment-option input:checked + label { border-color: var(--btn-primary); background-color: #fffaf8; color: var(--btn-primary); }
        .payment-option label { border: 2px solid #e0e0e0; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-weight: 600; color: #555; transition: 0.2s; }
        .qty-ctrl { display: flex; align-items: center; gap: 4px; }
        .qty-ctrl button { width: 26px; height: 26px; border: 1px solid #ddd; background: #f8f8f8; border-radius: 4px; cursor: pointer; }
        .qty-ctrl input { width: 44px; height: 26px; text-align: center; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>

<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container d-flex align-items-center">
        <a href="index.php?controller=home" onclick="return confirmLeave(event)" class="text-decoration-none">
            <h2 class="mb-0 fw-bold me-3" style="color: var(--btn-primary); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">2Life</h2>
        </a>
        <h4 class="mb-0 text-secondary border-start ps-3 py-1" style="font-size: 1.25rem;">Thanh Toán</h4>
        
        <a href="index.php?controller=cart" onclick="return confirmLeave(event)" 
           class="ms-auto btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-4 py-2 d-flex align-items-center"
           style="border-color: #dcdcdc; color: #555; transition: all 0.2s;">
            <i class="bi bi-arrow-left me-2"></i> Quay lại Giỏ Hàng
        </a>
    </div>
</header>
    </div>
</header>

<main class="container mb-5">
    <form id="checkoutForm" action="index.php?controller=checkout&action=processOrder" method="POST">
        
        <?php if ($isDirectCheckout): ?>
            <input type="hidden" name="direct_listing_id" value="<?= htmlspecialchars((string)$directListingId) ?>">
            <input type="hidden" name="direct_quantity" value="<?= htmlspecialchars((string)$directQuantity) ?>">
        <?php endif; ?>

        <div class="checkout-block pt-0 px-0 overflow-hidden">
            <div class="shopee-border"></div>
            <div class="px-4 py-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                    <h5 class="mb-0 fw-bold" style="color: var(--btn-primary); font-size: 1.1rem;">Địa Chỉ Nhận Hàng</h5>
                </div>
                <div id="selectedAddrDisplay" class="d-flex align-items-center flex-wrap gap-2">
                    <div>
                        <span class="fw-bold text-dark me-2" style="font-size:15px;"><?= htmlspecialchars($buyerName) ?></span>
                        <span class="fw-bold text-dark me-3" style="font-size:15px;"><?= htmlspecialchars($buyerPhone) ?></span>
                        <span class="text-secondary" id="addrText"><?= htmlspecialchars($fullAddress ? $fullAddress : 'Chưa thiết lập địa chỉ giao hàng') ?></span>
                    </div>
                    <button type="button" class="ms-auto btn btn-sm btn-outline-primary" style="color:var(--btn-secondary);border-color:var(--btn-secondary);" onclick="openAddrModal()">
                        <i class="bi bi-pencil me-1"></i>Thay đổi
                    </button>
                </div>
                <input type="hidden" name="streetAddress" id="streetAddressInput" value="<?= htmlspecialchars($fullAddress) ?>">
            </div>
        </div>

        <div class="checkout-block">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr class="text-secondary border-bottom">
                        <th style="width: 45%;">Sản phẩm</th>
                        <th class="text-center">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentSeller = null;
                    foreach ($checkoutItems as $item): 
                        if ($currentSeller !== $item['seller_id']):
                            $currentSeller = $item['seller_id'];
                    ?>
                        <tr>
                            <td colspan="4" class="pt-4 pb-2">
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <i class="bi bi-shop me-2 text-secondary"></i><span><?= htmlspecialchars($item['seller_name']) ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                        
                    <tr class="border-bottom-subtle" id="corow-<?= $item['listing_id'] ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($item['image']) ?>" class="product-img me-3">
                                <span class="fw-semibold text-dark" style="font-size:14px;"><?= htmlspecialchars($item['product_name']) ?></span>
                            </div>
                        </td>
                        <td class="text-center text-dark"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</td>
                        <td class="text-center">
                            <div class="qty-ctrl justify-content-center">
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, -1)">−</button>
                                <input type="number" id="coqty-<?= $item['listing_id'] ?>" 
                                       value="<?= (int)$item['quantity'] ?>" 
                                       min="1"
                                       data-price="<?= (int)$item['unit_price'] ?>"
                                       data-stock="<?= (int)($item['stock'] ?? 99) ?>" 
                                       onchange="syncQty(<?= $item['listing_id'] ?>)" readonly>
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark" id="cosubtotal-<?= $item['listing_id'] ?>"><?= number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="checkout-block">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-ticket-perforated-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">2Life Voucher</span>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" id="voucherInput" placeholder="Nhập mã voucher..." style="padding:9px 13px;border:1px solid #ddd;border-radius:6px;flex:1;max-width:280px;text-transform:uppercase;">
                <button type="button" id="voucherBtn" onclick="applyVoucher()" style="padding:9px 18px;border:1px solid var(--btn-secondary);border-radius:6px;color:var(--btn-secondary);background:#fff;font-weight:600;font-size:13px;cursor:pointer;">Áp dụng</button>
                <span id="voucherMsg" style="font-size:13px;"></span>
            </div>
            <div id="voucherDiscountRow" style="display:none;margin-top:10px;font-size:14px;color:#388E3C;font-weight:600;">
                <i class="bi bi-check-circle me-1"></i>Đã áp dụng: <span id="voucherDiscountLabel"></span>
            </div>
            <input type="hidden" name="voucherCodeInput" id="voucherCodeInput" value="">
            <input type="hidden" name="voucherDiscountInput" id="voucherDiscountInput" value="0">
        </div>

        <div class="checkout-block">
            <h6 class="fw-bold text-dark mb-3" style="font-size: 1rem;">Phương thức thanh toán</h6>
            <div class="d-flex gap-3 flex-wrap">
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payCOD" value="1" class="d-none" checked>
                    <label for="payCOD"><i class="bi bi-cash-coin me-2"></i>Thanh toán tiền mặt (COD)</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payVNPAY" value="2" class="d-none">
                    <label for="payVNPAY"><img src="https://sandbox.vnpayment.vn/paymentv2/Assets/Images/icon/vnpay.svg" height="20" class="me-2"> VNPay (Thẻ ATM/QR)</label>
                </div>
            </div>
        </div>

        <div class="checkout-block" style="background-color: #fffaf8; border: 1px solid #ffe3d5;">
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tổng tiền hàng</span>
                        <span class="text-dark" id="summaryMerchandise"><?= number_format($totalMerchandise, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-dark" id="summaryShipping"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                    </div>
                    <div id="summaryDiscountRow" class="d-flex justify-content-between mb-2" style="display:none !important;">
                        <span class="text-secondary">Giảm giá voucher</span>
                        <span style="color:#388E3C;font-weight:600;" id="summaryDiscount">-0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 mt-3 border-top pt-3">
                        <span class="text-dark fs-5 fw-semibold">Tổng thanh toán</span>
                        <span class="fs-4 fw-bold" style="color: var(--btn-primary);" id="summaryTotal"><?= number_format($totalFinal, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <input type="hidden" name="totalFinal" id="totalFinalInput" value="<?= $totalFinal ?>">

                    <button type="submit" id="submitBtn" class="btn w-100 fw-bold py-2 fs-5 text-white border-0 shadow-sm" style="background-color: var(--btn-primary); border-radius: 4px;">
                        <i class="bi bi-cart-check-fill me-2"></i>Đặt Hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<div class="modal fade" id="addrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--btn-primary);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt me-2"></i>Thay đổi địa chỉ nhận hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tỉnh / Thành phố *</label>
                        <select id="sessProvince" class="form-select form-select-sm" onchange="loadDistricts()">
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Quận / Huyện *</label>
                        <select id="sessDistrict" class="form-select form-select-sm" onchange="loadWards()" disabled>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Phường / Xã *</label>
                        <select id="sessWard" class="form-select form-select-sm" disabled>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Số nhà, tên đường *</label>
                        <input type="text" id="sessStreet" class="form-control form-control-sm" placeholder="Ví dụ: 730 Sư Vạn Hạnh">
                    </div>
                </div>
                <div id="addrMsg" class="mt-2" style="font-size:13px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-sm text-white fw-bold" onclick="submitSessAddr()" style="background:var(--btn-primary);border:none;">Cập nhật địa chỉ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentShippingFee = <?= (int)$shippingFee ?>;
let currentMerchandise = <?= (int)$totalMerchandise ?>;
let currentDiscount = 0;
let addrModal;

document.addEventListener('DOMContentLoaded', () => {
    addrModal = new bootstrap.Modal(document.getElementById('addrModal'));
    recalcTotal();
});

function recalcTotal() {
    let merch = 0;
    document.querySelectorAll('[id^="coqty-"]').forEach(inp => {
        merch += parseInt(inp.value) * parseInt(inp.dataset.price);
    });
    currentMerchandise = merch;

    const total = merch + currentShippingFee - currentDiscount;
    document.getElementById('summaryMerchandise').textContent = merch.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryShipping').textContent    = currentShippingFee.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryTotal').textContent       = Math.max(0, total).toLocaleString('vi-VN') + 'đ';
    document.getElementById('totalFinalInput').value          = Math.max(0, total);

    const discRow = document.getElementById('summaryDiscountRow');
    if (currentDiscount > 0) {
        discRow.style.setProperty('display', 'flex', 'important');
        document.getElementById('summaryDiscount').textContent = '-' + currentDiscount.toLocaleString('vi-VN') + 'đ';
    } else {
        discRow.style.setProperty('display', 'none', 'important');
    }
}

// ── VOUCHER LOGIC ──────────────────────────────────────────
async function applyVoucher() {
    const code = document.getElementById('voucherInput').value.trim();
    const msgEl = document.getElementById('voucherMsg');
    if (!code) { msgEl.innerHTML = '<span style="color:red;">Vui lòng nhập mã</span>'; return; }

    try {
        const res = await fetch('index.php?controller=cart&action=applyVoucherAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: code, subtotal: currentMerchandise })
        });
        const data = await res.json();

        if (data.status === 'success') {
            currentDiscount = parseInt(data.discount);
            document.getElementById('voucherDiscountRow').style.display = 'block';
            document.getElementById('voucherDiscountLabel').textContent = code.toUpperCase() + ' (Giảm ' + currentDiscount.toLocaleString('vi-VN') + 'đ)';
            document.getElementById('voucherCodeInput').value = code;
            document.getElementById('voucherDiscountInput').value = currentDiscount;
            
            const btn = document.getElementById('voucherBtn');
            btn.textContent = 'Bỏ mã';
            btn.style.color = '#e53935'; btn.style.borderColor = '#e53935';
            btn.onclick = removeVoucher;
            msgEl.innerHTML = '';
        } else {
            msgEl.innerHTML = `<span style="color:red;">${data.msg}</span>`;
        }
    } catch(e) { msgEl.innerHTML = '<span style="color:red;">Lỗi kết nối</span>'; }
    recalcTotal();
}

function removeVoucher() {
    currentDiscount = 0;
    document.getElementById('voucherDiscountRow').style.display = 'none';
    document.getElementById('voucherCodeInput').value = '';
    document.getElementById('voucherDiscountInput').value = 0;
    document.getElementById('voucherInput').value = '';
    
    const btn = document.getElementById('voucherBtn');
    btn.textContent = 'Áp dụng';
    btn.style.color = 'var(--btn-secondary)'; btn.style.borderColor = 'var(--btn-secondary)';
    btn.onclick = applyVoucher;
    recalcTotal();
}

// ── ADDRESS MODAL LOGIC (DATABASE DRIVEN) ─────────────────
async function openAddrModal() {
    addrModal.show();
    if(document.getElementById('sessProvince').options.length <= 1) {
        const res = await fetch('index.php?controller=checkout&action=getProvinces');
        const data = await res.json();
        let html = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
        data.forEach(p => html += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`);
        document.getElementById('sessProvince').innerHTML = html;
    }
}

async function loadDistricts() {
    const provId = document.getElementById('sessProvince').value;
    const distSelect = document.getElementById('sessDistrict');
    const wardSelect = document.getElementById('sessWard');
    
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>'; wardSelect.disabled = true;
    if(!provId) { distSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>'; distSelect.disabled = true; return; }
    
    const res = await fetch(`index.php?controller=checkout&action=getDistricts&province_id=${provId}`);
    const data = await res.json();
    let html = '<option value="">-- Chọn Quận/Huyện --</option>';
    data.forEach(d => html += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`);
    distSelect.innerHTML = html; distSelect.disabled = false;
}

async function loadWards() {
    const distId = document.getElementById('sessDistrict').value;
    const wardSelect = document.getElementById('sessWard');
    if(!distId) { wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>'; wardSelect.disabled = true; return; }
    
    const res = await fetch(`index.php?controller=checkout&action=getWards&district_id=${distId}`);
    const data = await res.json();
    let html = '<option value="">-- Chọn Phường/Xã --</option>';
    data.forEach(w => html += `<option value="${w.id}" data-name="${w.name}">${w.name}</option>`);
    wardSelect.innerHTML = html; wardSelect.disabled = false;
}

async function submitSessAddr() {
    const provSel = document.getElementById('sessProvince');
    const distSel = document.getElementById('sessDistrict');
    const wardSel = document.getElementById('sessWard');
    const street  = document.getElementById('sessStreet').value.trim();

    if (provSel.selectedIndex <= 0 || distSel.selectedIndex <= 0 || wardSel.selectedIndex <= 0 || !street) {
        document.getElementById('addrMsg').innerHTML = '<span style="color:red;">Vui lòng chọn đầy đủ thông tin.</span>';
        return;
    }

    const provinceName = provSel.options[provSel.selectedIndex].dataset.name;
    const districtName = distSel.options[distSel.selectedIndex].dataset.name;
    const wardName     = wardSel.options[wardSel.selectedIndex].dataset.name;
    const fullAddr     = street + ', ' + wardName + ', ' + districtName + ', ' + provinceName;

    try {
        const res = await fetch('index.php?controller=checkout&action=saveAddressSessionAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ streetAddress: fullAddr, province: provinceName, district: districtName, ward: wardName, street: street })
        });
        const data = await res.json();
        if (data.status === 'success') window.location.reload();
    } catch(e) { }
}

// ── UTILITIES ──────────────────────────────────────────────
function changeCoQty(listingId, delta) {
    const inp = document.getElementById('coqty-' + listingId);
    let qty = parseInt(inp.value) + delta;
    const stock = parseInt(inp.dataset.stock || 99);
    if (qty < 1) return;
    if (qty > stock) { alert('Số lượng vượt quá tồn kho (' + stock + ')!'); return; }
    inp.value = qty;
    document.getElementById('cosubtotal-' + listingId).textContent = (qty * parseInt(inp.dataset.price)).toLocaleString('vi-VN') + 'đ';
    recalcTotal();
}
// Hàm hiển thị hộp thoại xác nhận khi rời trang
function confirmLeave(e) {
    const confirmMsg = "Cậu đang trong quá trình thanh toán.\nCậu có chắc chắn muốn hủy thanh toán và rời khỏi trang này không?";
    if (!confirm(confirmMsg)) {
        e.preventDefault(); // Nếu bấm No (Cancel) -> Ngăn chặn chuyển trang (Ở lại)
        return false;
    }
    return true; // Nếu bấm Yes (OK) -> Cho phép chuyển trang
}
</script>
</body>
</html>