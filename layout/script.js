document.addEventListener("DOMContentLoaded", function () {
    // ================================================================
    // 1. CHỨC NĂNG DROPDOWN MENU
    // ================================================================
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.nav-avatar-btn');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                dropdowns.forEach(d => { if (d !== dropdown) d.classList.remove('open'); });
                dropdown.classList.toggle('open');
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            dropdowns.forEach(d => d.classList.remove('open'));
        }
    });

    // ================================================================
    // 2. KHỞI TẠO CHO TRANG THANH TOÁN
    // ================================================================
    const addrModalEl = document.getElementById('addrModal');
    if (addrModalEl) {
        window.addrModal = new bootstrap.Modal(addrModalEl);
        if (typeof recalcTotal === 'function') recalcTotal();
    }
});

/**
 * ============================================================================
 * CÁC HÀM XỬ LÝ RIÊNG CHO TRANG THANH TOÁN (CHECKOUT)
 * (Khai báo tự do để HTML click gọi được)
 * ============================================================================
 */

// ========================================================================
// 1. BIẾN TOÀN CỤC 
// ========================================================================
let currentDiscount = 0;
let addrModal = null;

// ========================================================================
// 2. KHỐI NÀY TỰ ĐỘNG CHẠY KHI VỪA LOAD XONG TRANG
// ========================================================================
document.addEventListener("DOMContentLoaded", function () {
    // --- Tính năng Dropdown Menu của cậu ---
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.nav-avatar-btn');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                dropdowns.forEach(d => { if (d !== dropdown) d.classList.remove('open'); });
                dropdown.classList.toggle('open');
            }
        });
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            dropdowns.forEach(d => d.classList.remove('open'));
        }
    });

    // --- Khởi tạo Modal Địa chỉ (Nếu đang ở trang Checkout) ---
    const addrModalEl = document.getElementById('addrModal');
    if (addrModalEl) {
        addrModal = new bootstrap.Modal(addrModalEl);
        recalcTotal(); // Tự tính tiền lúc mới vào
    }
});

// ========================================================================
// 3. CÁC HÀM XỬ LÝ (PHẢI NẰM BÊN NGOÀI ĐỂ NÚT BẤM HTML CÓ THỂ GỌI)
// ========================================================================

function recalcTotal() {
    if (!document.getElementById('summaryMerchandise')) return; // Không phải trang thanh toán thì bỏ qua

    let merch = 0;
    document.querySelectorAll('[id^="coqty-"]').forEach(inp => {
        merch += parseInt(inp.value) * parseInt(inp.dataset.price);
    });
    
    let shipFee = window.currentShippingFee || 0;
    window.currentMerchandise = merch;

    const total = merch + shipFee - currentDiscount;
    
    document.getElementById('summaryMerchandise').textContent = merch.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryShipping').textContent    = shipFee.toLocaleString('vi-VN') + 'đ';
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

async function applyVoucher() {
    const code = document.getElementById('voucherInput')?.value.trim();
    const msgEl = document.getElementById('voucherMsg');
    if (!code) { if(msgEl) msgEl.innerHTML = '<span style="color:red;">Vui lòng nhập mã</span>'; return; }

    try {
        const res = await fetch('index.php?controller=cart&action=applyVoucherAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: code, subtotal: window.currentMerchandise })
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
            btn.style.color = '#dc3545'; btn.style.borderColor = '#dc3545';
            btn.onclick = removeVoucher;
            if(msgEl) msgEl.innerHTML = '';
        } else {
            if(msgEl) msgEl.innerHTML = `<span style="color:red;">${data.msg}</span>`;
        }
    } catch(e) { if(msgEl) msgEl.innerHTML = '<span style="color:red;">Lỗi kết nối</span>'; }
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

async function openAddrModal() {
    if(addrModal) addrModal.show();
    const sessProv = document.getElementById('sessProvince');
    if(sessProv && sessProv.options.length <= 1) {
        const res = await fetch('index.php?controller=checkout&action=getProvinces');
        const data = await res.json();
        let html = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
        data.forEach(p => html += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`);
        sessProv.innerHTML = html;
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
    } catch(e) { console.error("Lỗi cập nhật địa chỉ", e); }
}

function changeCoQty(listingId, delta) {
    const inp = document.getElementById('coqty-' + listingId);
    if(!inp) return;
    let qty = parseInt(inp.value) + delta;
    validateAndApplyQty(listingId, inp, qty);
}

function manualChangeQty(listingId, event) {
    const inp = event.target;
    let qty = parseInt(inp.value);
    validateAndApplyQty(listingId, inp, qty);
}

function validateAndApplyQty(listingId, inp, qty) {
    const stock = parseInt(inp.dataset.stock || 99);
    if (isNaN(qty) || qty < 1) qty = 1; 
    else if (qty > stock) { 
        qty = stock; 
        if(typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Hết hàng!', text: 'Kho chỉ còn tối đa ' + stock + ' sản phẩm.', confirmButtonColor: '#FF7A3D' });
        }
    }
    inp.value = qty;
    const subtotalEl = document.getElementById('cosubtotal-' + listingId);
    if(subtotalEl) subtotalEl.textContent = (qty * parseInt(inp.dataset.price)).toLocaleString('vi-VN') + 'đ';
    recalcTotal();
}

function confirmLeave(event) {
    event.preventDefault(); 
    const targetUrl = event.currentTarget.href; 
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Xác nhận rời đi?',
            text: "Bạn đang trong quá trình thanh toán. Thông tin đơn hàng sẽ không được lưu lại nếu bạn rời trang.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Rời trang',
            cancelButtonText: 'Ở lại'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = targetUrl;
        });
    } else {
        window.location.href = targetUrl;
    }
}

// ========================================================================
// 4. CÁC HÀM XỬ LÝ RIÊNG CHO TRANG GIỎ HÀNG (CART)
// Đã thêm tiền tố "cart" để không bị xung đột với trang Checkout
// ========================================================================

window.cartAppliedDiscount  = 0;
window.cartAppliedVoucherId = null;

// Tự động tính tiền khi vào trang giỏ hàng
document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById('cart-items-list')) {
        window.cartUpdateSummary();
        window.cartUpdateOrderBtn();
    }
});

window.cartSendActionToDB = async function(payload) {
    try {
        const response = await fetch('index.php?controller=cart&action=updateAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.status !== 'success') {
            if(typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Úi, có lỗi!', text: result.msg, confirmButtonColor: '#FF7A3D' });
            return false;
        }
        return result;
    } catch (error) {
        if(typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Lỗi kết nối', text: 'Vui lòng kiểm tra lại mạng!', confirmButtonColor: '#FF7A3D' });
        return false;
    }
}

window.cartChangeQty = async function(btn, listingId, delta) {
    let container = btn.closest('.item-quantity');
    container.style.pointerEvents = 'none'; 
    container.style.opacity = '0.5';

    const input  = document.getElementById('qty-' + listingId);
    let currentQty = parseInt(input.value);
    let newQty     = currentQty + delta;
    const stock    = parseInt(input.dataset.stock);

    if (newQty <= 0) {
        container.style.pointerEvents = 'auto'; container.style.opacity = '1';
        window.cartRemoveItem(listingId); return;
    }
    if (newQty > stock) {
        window.cartShowToast(`Chỉ còn ${stock} sản phẩm trong kho!`, 'error');
        container.style.pointerEvents = 'auto'; container.style.opacity = '1'; return;
    }

    input.value = newQty;
    const priceEl  = document.getElementById('price-' + listingId);
    const unitPrice = parseInt(input.dataset.dongia);
    priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
    window.cartUpdateSummary();

    let success = await window.cartSendActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    if (success) {
        window.cartResetVoucher(); window.cartUpdateOrderBtn();
    } else { 
        input.value = currentQty;
        priceEl.textContent = (unitPrice * currentQty).toLocaleString('vi-VN') + 'đ'; window.cartUpdateSummary();
    }
    container.style.pointerEvents = 'auto'; container.style.opacity = '1';
}

window.cartManualChangeQty = async function(input, listingId) {
    let container = input.closest('.item-quantity');
    container.style.pointerEvents = 'none';
    container.style.opacity = '0.5';

    let newQty = parseInt(input.value);
    const stock = parseInt(input.dataset.stock);

    if (isNaN(newQty) || newQty <= 0) {
        container.style.pointerEvents = 'auto'; container.style.opacity = '1';
        window.cartRemoveItem(listingId); return;
    }

    if (newQty > stock) {
        window.cartShowToast(`Chỉ còn ${stock} sản phẩm trong kho!`, 'error');
        newQty = stock; 
        input.value = newQty;
    }

    const priceEl  = document.getElementById('price-' + listingId);
    const unitPrice = parseInt(input.dataset.dongia);
    priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
    window.cartUpdateSummary();

    let success = await window.cartSendActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    if (success) {
        window.cartResetVoucher(); window.cartUpdateOrderBtn();
    } else {
        window.location.reload(); 
    }
    container.style.pointerEvents = 'auto'; container.style.opacity = '1';
}

window.cartRemoveItem = function(listingId) {
    if(typeof Swal === 'undefined') return;
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
            let success = await window.cartSendActionToDB({ action: 'remove', listingId: listingId });
            if (success) {
                const item = document.getElementById('item-' + listingId);
                item.style.opacity = '0';
                item.style.transform = 'translateX(30px)';
                setTimeout(() => { 
                    item.remove();
                    window.cartResetVoucher();
                    window.cartUpdateSummary();
                    window.cartUpdateOrderBtn();
                    if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
                }, 300);
            }
        }
    });
}

window.cartToggleAll = function(master) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = master.checked);
    window.cartUpdateSummary();
    window.cartUpdateOrderBtn();
}

window.cartRemoveSelected = function() {
    const selectedChecks = document.querySelectorAll('.item-check:checked');
    if (selectedChecks.length === 0) { 
        if(typeof Swal !== 'undefined') Swal.fire({ icon: 'info', title: 'Ê khoan!', text: 'Cậu chưa chọn sản phẩm nào để xóa cả!', confirmButtonColor: '#FF7A3D' });
        return; 
    }
    
    if(typeof Swal === 'undefined') return;
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
                await window.cartSendActionToDB({ action: 'remove', listingId: listingId });
                document.getElementById('item-' + listingId).remove();
            }
            window.cartResetVoucher();
            window.cartUpdateSummary();
            window.cartUpdateOrderBtn();
            if (document.querySelectorAll('.cart-item').length === 0) { window.location.reload(); }
        }
    });
}

window.cartUpdateSummary = function() {
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

    const elItemCount = document.getElementById('itemCount');
    if(elItemCount) elItemCount.textContent = count;
    
    const elSubtotal = document.getElementById('subtotal');
    if(elSubtotal) elSubtotal.textContent   = subtotal.toLocaleString('vi-VN') + 'đ';

    const finalTotal = Math.max(0, subtotal - window.cartAppliedDiscount);
    const elTotalPrice = document.getElementById('totalPrice');
    if(elTotalPrice) elTotalPrice.textContent  = finalTotal.toLocaleString('vi-VN') + 'đ';

    const tongSoItemEl = document.getElementById('tongSoItem');
    if (tongSoItemEl) tongSoItemEl.textContent = items.length;
}

window.cartUpdateOrderBtn = function() {
    const btn     = document.getElementById('orderBtn');
    const msgEl   = document.getElementById('noSelectMsg');
    if(!btn || !msgEl) return;
    
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

window.cartGoToCheckout = function() {
    const checked = document.querySelectorAll('.item-check:checked');
    if (checked.length === 0) {
        if(typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Khoan đã!', text: 'Cậu chưa chọn sản phẩm nào để đặt hàng cả!', confirmButtonColor: '#FF7A3D' });
        return;
    }
    const ids = Array.from(checked).map(c => c.value).join(',');
    document.getElementById('selectedIdsInput').value = ids;
    document.getElementById('checkoutForm').submit();
}

window.cartApplyVoucher = async function() {
    const code  = document.getElementById('voucher-input').value.trim();
    const btn   = document.getElementById('voucher-btn');

    if (!code) {
        window.cartShowVoucherMsg('Vui lòng nhập mã voucher.', 'error');
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
        window.cartShowVoucherMsg('Vui lòng chọn ít nhất 1 sản phẩm trước khi áp voucher.', 'error');
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
            window.cartAppliedDiscount  = data.discount;
            window.cartAppliedVoucherId = data.voucherId;
            document.getElementById('voucher-discount-row').style.display = 'flex';
            document.getElementById('voucher-code-label').textContent     = code.toUpperCase();
            document.getElementById('voucher-discount-amount').textContent = data.discountFormat;
            btn.textContent   = 'Bỏ';
            btn.onclick       = window.cartResetVoucher;
            btn.style.color   = 'var(--error-color)';
            document.getElementById('voucher-input').disabled = true;
            window.cartShowVoucherMsg(data.msg, 'success');
            window.cartUpdateSummary();
        } else {
            window.cartShowVoucherMsg(data.msg, 'error');
        }
    } catch (e) {
        window.cartShowVoucherMsg('Lỗi kết nối, vui lòng thử lại.', 'error');
    }

    btn.disabled = false;
    if (window.cartAppliedDiscount === 0) btn.textContent = 'Áp dụng';
}

window.cartShowVoucherMsg = function(msg, type) {
    const el = document.getElementById('voucher-msg');
    el.textContent = msg;
    el.style.color = type === 'success' ? '#388E3C' : 'var(--error-color)';
}

window.cartResetVoucher = function() {
    window.cartAppliedDiscount = 0; window.cartAppliedVoucherId = null;
    document.getElementById('voucher-discount-row').style.display  = 'none';
    document.getElementById('voucher-discount-amount').textContent  = '—';
    document.getElementById('voucher-code-label').textContent       = '';
    document.getElementById('voucher-input').disabled               = false;
    document.getElementById('voucher-input').value                  = '';
    document.getElementById('voucher-msg').textContent              = '';
    const btn = document.getElementById('voucher-btn');
    btn.textContent = 'Áp dụng'; btn.onclick = window.cartApplyVoucher; btn.style.color = 'var(--text-primary)';
    window.cartUpdateSummary();
}

window.cartShowToast = function(msg, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;color:#fff;background:${type==='error'?'#e53935':'#43a047'};box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:opacity 0.3s`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ==========================================
// KHU VỰC: QUẢN LÝ TIN ĐĂNG (MANAGE LISTING)
// ==========================================
let manageListing_currentId = null;
let manageListing_modalInstance = null;

// Hàm hỗ trợ khởi tạo Modal an toàn (Chỉ chạy khi Element tồn tại)
function manageListing_getModal() {
    if (!manageListing_modalInstance) {
        const modalElement = document.getElementById('listingDetailModal');
        if (modalElement && typeof bootstrap !== 'undefined') {
            manageListing_modalInstance = new bootstrap.Modal(modalElement);
        }
    }
    return manageListing_modalInstance;
}

// Hàm: Mở chi tiết tin đăng
function manageListing_viewDetail(btnElement) {
    manageListing_currentId = btnElement.getAttribute('data-id');
    let statusId = btnElement.getAttribute('data-status');
    
    // Cập nhật trạng thái nút Chỉnh sửa
    let editBtn = document.getElementById('btn-edit-listing');
    if (editBtn) {
        if (statusId === '1') {
            editBtn.classList.remove('disabled', 'btn-secondary');
            editBtn.classList.add('btn-primary');
            editBtn.href = `index.php?controller=listing&action=edit&id=${manageListing_currentId}`;
        } else {
            editBtn.classList.add('disabled', 'btn-secondary');
            editBtn.classList.remove('btn-primary');
            editBtn.href = "#";
        }
    }

    // Reset nội dung body và hiển thị Loading
    const modalBody = document.getElementById('modal-content-body');
    if (modalBody) {
        modalBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
    }
    
    const modal = manageListing_getModal();
    if (modal) modal.show();

    // Lấy dữ liệu qua AJAX
    fetch(`index.php?controller=manage_listing&action=ajaxGetDetail&id=${manageListing_currentId}`)
        .then(res => res.json())
        .then(response => {
            if(response.status === 'success' && modalBody) {
                let data = response.data;
                modalBody.innerHTML = `
                    <h6 class="fw-bold mb-2">${data.title}</h6>
                    <p class="text-danger fw-bold fs-5 mb-2">${new Intl.NumberFormat('vi-VN').format(data.price)} VNĐ</p>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item px-0"><b>Danh mục:</b> ${data.category_name}</li>
                        <li class="list-group-item px-0"><b>Tình trạng:</b> ${data.condition_name}</li>
                        <li class="list-group-item px-0"><b>Tồn kho:</b> ${data.stock_quantity}</li>
                    </ul>
                    <div class="bg-light p-2 rounded border" style="max-height:100px; overflow-y:auto; font-size:13px">
                        ${data.description.replace(/\n/g, '<br>')}
                    </div>
                `;
            }
        });
}

// Hàm: Xóa tin đăng
function manageListing_deleteListing() {
    if(!manageListing_currentId) return;

    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Tin đăng này sẽ bị xóa vĩnh viễn khỏi hệ thống!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Vâng, Xóa nó!',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            let formData = new FormData();
            formData.append('id', manageListing_currentId);

            fetch('index.php?controller=manage_listing&action=ajaxDelete', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const modal = manageListing_getModal();
                    if (modal) modal.hide();
                    
                    Swal.fire('Đã xóa!', data.message, 'success');
                    
                    const row = document.getElementById('row-' + manageListing_currentId);
                    if (row) row.remove();
                } else {
                    Swal.fire('Lỗi!', data.message, 'error');
                }
            });
        }
    });
}

// Hàm: Ẩn tin đăng
function manageListing_hideListing(event, btnElement) {
    event.preventDefault();
    const url = btnElement.getAttribute('data-href');
    const listingId = btnElement.getAttribute('data-id');

    Swal.fire({
        title: 'Xác nhận ẩn tin?',
        text: "Tin đăng này sẽ bị ẩn khỏi gian hàng và người mua sẽ không nhìn thấy nữa!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Vâng, Ẩn ngay!',
        cancelButtonText: 'Hủy bỏ'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang xử lý...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        const row = document.getElementById('row-' + listingId);
                        if (row) {
                            const statusTd = row.querySelector('td:nth-child(4)');
                            if (statusTd) {
                                statusTd.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2"><i class="bi bi-eye-slash-fill"></i> Đã ẩn/Đóng</span>';
                            }
                            btnElement.remove();
                        }
                    } else {
                        Swal.fire('Lỗi!', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error("Lỗi fetch:", err);
                    Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
                });
        }
    });
}

// ==========================================
// KHU VỰC: ĐĂNG / SỬA TIN BÁN (POST PRODUCT)
// ==========================================

// Kích hoạt input file ảnh ẩn
function postProduct_triggerImageSelect() {
    const fileInput = document.getElementById('imageUpload');
    if (fileInput) fileInput.click();
}

// Kích hoạt input file video ẩn
function postProduct_triggerVideoSelect() {
    const fileInput = document.getElementById('videoUpload');
    if (fileInput) fileInput.click();
}

// Xử lý xem trước hình ảnh khi upload
function postProduct_handleImageChange(event) {
    const files = event.target.files;
    const uploadBoxUI = document.getElementById('uploadBoxUI');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const carouselInnerImages = document.getElementById('carouselInnerImages');

    // Chỉ thực thi nếu người dùng có chọn file và các DOM element thực sự tồn tại
    if (files.length > 0 && uploadBoxUI && imagePreviewContainer && carouselInnerImages) {
        uploadBoxUI.setAttribute('style', 'display: none !important;');
        imagePreviewContainer.style.display = 'block';
        carouselInnerImages.innerHTML = ''; 

        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const activeClass = index === 0 ? 'active' : '';
                    const imgHtml = `
                        <div class="carousel-item ${activeClass} h-100 w-100">
                            <img src="${e.target.result}" class="d-block w-100 h-100" style="object-fit: contain;" alt="Preview Image ${index + 1}">
                        </div>
                    `;
                    carouselInnerImages.insertAdjacentHTML('beforeend', imgHtml);
                }
                reader.readAsDataURL(file);
            }
        });
    }
}

// Xử lý thông báo đã chọn Video
function postProduct_handleVideoChange(event) {
    const videoText = document.getElementById('videoText');
    if (videoText) {
        videoText.innerHTML = event.target.files.length > 0 
            ? `Đã chọn <strong class="text-primary">1 video</strong>` 
            : 'Kéo thả hoặc <strong>Chọn video</strong>';
    }
}

// Load Quận/Huyện dựa trên ID Tỉnh/Thành
function postProduct_loadDistricts(provinceId) {
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');

    if (!districtSelect || !wardSelect) return;

    // Reset Phường/Xã
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    wardSelect.disabled = true;

    if (provinceId) {
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;

        fetch(`index.php?controller=listing&action=getDistrictsAjax&province_id=${provinceId}`)
            .then(res => res.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                data.forEach(item => {
                    districtSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
                districtSelect.disabled = false;
            })
            .catch(err => {
                districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    } else {
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        districtSelect.disabled = true;
    }
}

// Load Phường/Xã dựa trên ID Quận/Huyện
function postProduct_loadWards(districtId) {
    const wardSelect = document.getElementById('wardSelect');
    if (!wardSelect) return;

    if (districtId) {
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        fetch(`index.php?controller=listing&action=getWardsAjax&district_id=${districtId}`)
            .then(res => res.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                data.forEach(item => {
                    wardSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
                wardSelect.disabled = false;
            })
            .catch(err => {
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    } else {
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
    }
}

// Xử lý gửi Form Đăng/Sửa tin qua AJAX
function postProduct_handleSubmit(event, formElement) {
    event.preventDefault(); 
    let submitBtn = formElement.querySelector('button[type="submit"]');
    let originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';
    submitBtn.disabled = true;

    let formData = new FormData(formElement);

    fetch(formElement.action, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Thành công!', text: data.message, showConfirmButton: false, timer: 2000 })
            .then(() => { window.location.href = 'index.php?controller=manage_listing&action=index'; });
        } else {
            Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
            submitBtn.innerHTML = originalText; 
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        Swal.fire({ icon: 'error', title: 'Lỗi kết nối', text: 'Không thể gửi dữ liệu lên máy chủ.' });
        submitBtn.innerHTML = originalText; 
        submitBtn.disabled = false;
    });
}

// ==========================================
// KHU VỰC: CHI TIẾT DUYỆT TIN ĐĂNG (APPROVE LISTING DETAIL)
// ==========================================

// Xử lý sự kiện bấm nút Duyệt/Từ chối/Ẩn
function approveListingDetail_handleAction(e, btn) {
    e.preventDefault(); // Chặn hành động chuyển trang mặc định

    // Kiểm tra SweetAlert2
    if (typeof Swal === 'undefined') {
        alert('Lỗi: Thư viện SweetAlert2 chưa được tải về! Vui lòng kiểm tra lại kết nối mạng hoặc thẻ CDN.');
        return;
    }

    const url = btn.getAttribute('data-href');
    const confirmText = btn.getAttribute('data-text');
    const type = btn.getAttribute('data-type');

    let confirmButtonColor = '#198754';
    if (type === 'reject') confirmButtonColor = '#dc3545';
    if (type === 'hide') confirmButtonColor = '#ffc107';

    // Hiển thị Pop-up xác nhận
    Swal.fire({
        title: 'Xác nhận hành động?',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy bỏ'
    }).then((result) => {
        if (result.isConfirmed) {

            // Hiển thị loading trong lúc gọi Ajax
            Swal.fire({
                title: 'Đang xử lý...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Gọi Ajax
            fetch(url)
                .then(async response => {
                    const rawText = await response.text();
                    try {
                        return JSON.parse(rawText);
                    } catch (err) {
                        console.error("Lỗi định dạng JSON trả về:", rawText);
                        throw new Error("Dữ liệu trả về từ Controller không hợp lệ (không phải JSON). Hãy ấn F12 xem Console để biết chi tiết.");
                    }
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        const listingId = url.split('&id=')[1];
                        approveListingDetail_updateUI(data.status_id, listingId);
                    } else {
                        Swal.fire('Thất bại', data.message || 'Lỗi xử lý.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error FETCH:', error);
                    Swal.fire('Lỗi hệ thống', error.message, 'error');
                });
        }
    });
}

// Hàm cập nhật giao diện sau khi gọi Ajax thành công
function approveListingDetail_updateUI(statusId, listingId) {
    const badgeContainer = document.getElementById('status-badge-container');
    const buttonsContainer = document.getElementById('action-buttons-container');
    
    if (!badgeContainer || !buttonsContainer) return;

    let badgeHTML = 'Trạng thái hiện tại: ';
    if (statusId == 1) badgeHTML += '<span class="badge bg-warning text-dark fs-6 ms-2">Chờ duyệt</span>';
    else if (statusId == 2) badgeHTML += '<span class="badge bg-success fs-6 ms-2">Đang hiển thị</span>';
    else if (statusId == 3) badgeHTML += '<span class="badge bg-danger fs-6 ms-2">Đã từ chối</span>';
    else badgeHTML += '<span class="badge bg-secondary fs-6 ms-2">Đã ẩn/Gỡ</span>';

    badgeContainer.innerHTML = badgeHTML;

    // Cập nhật lại các nút bấm (Nhớ phải chèn thêm onclick="approveListingDetail_handleAction(event, this)")
    if (statusId == 1) {
        buttonsContainer.innerHTML = `
            <div class="d-flex flex-column gap-2">
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=approve&id=${listingId}" class="btn btn-success fw-bold py-2" data-type="approve" data-text="Bạn chắc chắn muốn duyệt tin đăng này lên sàn?" onclick="approveListingDetail_handleAction(event, this)">
                    <i class="bi bi-check-circle-fill me-1"></i> Phê duyệt hiển thị
                </button>
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=reject&id=${listingId}" class="btn btn-danger fw-bold py-2" data-type="reject" data-text="Bạn chắc chắn muốn từ chối tin đăng này?" onclick="approveListingDetail_handleAction(event, this)">
                    <i class="bi bi-x-circle-fill me-1"></i> Từ chối tin đăng
                </button>
            </div>
        `;
    } else if (statusId == 2) {
        buttonsContainer.innerHTML = `
            <div class="d-flex flex-column gap-2">
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=hide&id=${listingId}" class="btn btn-warning text-dark fw-bold py-2" data-type="hide" data-text="Gỡ tin đăng này khỏi hệ thống ngay lập tức?" onclick="approveListingDetail_handleAction(event, this)">
                    <i class="bi bi-eye-slash-fill me-1"></i> Buộc gỡ / Ẩn tin
                </button>
            </div>
        `;
    } else {
        buttonsContainer.innerHTML = `
            <div class="alert alert-secondary mb-0 text-center">
                Tin đăng này đã được xử lý xong.
            </div>
        `;
    }
}

// ==========================================
// KHU VỰC: DANH SÁCH DUYỆT TIN ĐĂNG BÁN (APPROVE LISTING LIST)
// ==========================================

// Xử lý hiệu ứng Loading khi Admin chuyển Tab hoặc chuyển Trang (Pagination)
function approveListingList_navigate(event, targetUrl) {
    // Nếu link đang active hoặc bị disabled thì không làm gì cả
    const parentLi = event.currentTarget.parentElement;
    if (parentLi && (parentLi.classList.contains('active') || parentLi.classList.contains('disabled'))) {
        event.preventDefault();
        return;
    }

    event.preventDefault(); // Tạm dừng chuyển trang

    // Gọi SweetAlert2 hiển thị Loading
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Đang tải dữ liệu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Chuyển hướng URL sau khi Pop-up đã bật
    window.location.href = targetUrl;
}

// Xử lý hiệu ứng Loading khi Admin bấm nút "Xem chi tiết"
function approveListingList_goToDetail(event, targetUrl) {
    event.preventDefault(); 

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Đang lấy thông tin sản phẩm...',
            text: 'Vui lòng chờ trong giây lát',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Thực hiện chuyển trang
    window.location.href = targetUrl;
}
