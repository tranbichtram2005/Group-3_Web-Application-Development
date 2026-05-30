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

// ==========================================
// KHU VỰC: CHI TIẾT PHÊ DUYỆT NGƯỜI BÁN (APPROVE SELLER DETAIL)
// ==========================================

// Xử lý sự kiện bấm Phê duyệt
function approveSellerDetail_handleApprove(btnElement) {
    const profileId = btnElement.getAttribute('data-profile');
    const userId = btnElement.getAttribute('data-user');
    approveSellerDetail_sendAjaxRequest('approve', profileId, userId, '');
}

// Xử lý sự kiện bấm Xác nhận từ chối
function approveSellerDetail_handleReject(btnElement) {
    const profileId = btnElement.getAttribute('data-profile');
    const userId = btnElement.getAttribute('data-user');
    const reasonInput = document.getElementById('rejectReasonInput');
    const reason = reasonInput ? reasonInput.value.trim() : '';

    if (!reason) {
        alert('Admin vui lòng cung cấp lý do từ chối hồ sơ!');
        return;
    }
    approveSellerDetail_sendAjaxRequest('reject', profileId, userId, reason);
}

// Xử lý gửi AJAX chung
function approveSellerDetail_sendAjaxRequest(actionName, profileId, userId, reason) {
    const formData = new FormData();
    formData.append('profile_id', profileId);
    formData.append('user_id', userId);
    
    if (actionName === 'reject') {
        formData.append('reject_reason', reason);
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Đang xử lý dữ liệu...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }

    fetch(`index.php?controller=approveseller&action=${actionName}`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Thành công', data.message, 'success').then(() => approveSellerDetail_updateUI(actionName));
            } else {
                alert(data.message);
                approveSellerDetail_updateUI(actionName);
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Thất bại', data.message, 'error');
            } else {
                alert(data.message);
            }
        }
    })
    .catch(err => {
        console.error("Fetch Error:", err);
        alert('Lỗi đường truyền: Không thể kết nối tới máy chủ!');
    });
}

// Cập nhật giao diện sau xử lý
function approveSellerDetail_updateUI(actionName) {
    // Dọn dẹp Modal Bootstrap triệt để
    const modalEl = document.getElementById('rejectModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }
    
    // Cố ép xóa màn hình đen (backdrop) nếu Bootstrap bị kẹt
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    // Cập nhật DOM hiển thị kết quả
    const wrapper = document.getElementById('panel-action-wrapper');
    const statusBadge = document.getElementById('status-badge');

    if (actionName === 'approve') {
        if (wrapper) wrapper.innerHTML = `<div class="alert alert-success m-0 rounded-3 text-dark small"><i class="bi bi-patch-check-fill me-2 text-success"></i>Hồ sơ này vừa được kích hoạt thành công.</div>`;
        if (statusBadge) {
            statusBadge.className = "badge p-2 rounded-3 bg-success-subtle text-success";
            statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>ĐÃ KÍCH HOẠT';
        }
    } else {
        if (wrapper) wrapper.innerHTML = `<div class="alert alert-secondary m-0 rounded-3 text-dark small"><i class="bi bi-x-octagon-fill me-2 text-danger"></i>Đơn đăng ký đã bị từ chối và xóa khỏi danh sách.</div>`;
        if (statusBadge) {
            statusBadge.className = "badge p-2 rounded-3 bg-danger-subtle text-danger";
            statusBadge.innerHTML = '<i class="bi bi-x-circle me-1"></i>ĐÃ TỪ CHỐI';
        }
    }
}

// ==========================================
// KHU VỰC: DANH SÁCH PHÊ DUYỆT NGƯỜI BÁN (APPROVE SELLER LIST)
// ==========================================

function approveSeller_switchTab(event, clickedTab) {
    event.preventDefault();
    const status = clickedTab.getAttribute('data-status');
    const wrapper = document.getElementById('sellerListWrapper');
    const allTabs = document.querySelectorAll('.tab-action');

    // Nếu không tìm thấy các thành phần này thì ngưng chạy để tránh lỗi
    if (!wrapper || allTabs.length === 0) return;

    // 1. Cập nhật phong cách hiển thị cho Tab đang click
    allTabs.forEach(t => {
        t.classList.remove('active');
        t.style.backgroundColor = '';
        t.style.color = '#555';
    });
    
    clickedTab.classList.add('active');
    clickedTab.style.backgroundColor = '#0d6efd';
    clickedTab.style.color = 'white';

    // 2. Loading State hiệu ứng chờ
    wrapper.innerHTML = `
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted small mb-0">Đang tải danh sách dữ liệu...</p>
        </div>`;

    // 3. Gọi AJAX nhận HTML render mới
    fetch(`index.php?controller=approveseller&action=fetchList&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                wrapper.innerHTML = data.html;
                
                // Cập nhật lại số lượng trên Badge (có kiểm tra tồn tại DOM)
                const badgePending = document.getElementById('badge-pending');
                const badgeVerified = document.getElementById('badge-verified');
                const badgeRejected = document.getElementById('badge-rejected');

                if (badgePending && data.stats[0] !== undefined) badgePending.innerText = data.stats[0];
                if (badgeVerified && data.stats[1] !== undefined) badgeVerified.innerText = data.stats[1];
                if (badgeRejected && data.stats[2] !== undefined) badgeRejected.innerText = data.stats[2];
            }
        })
        .catch(err => {
            console.error("AJAX Error: ", err);
            wrapper.innerHTML = '<div class="alert alert-danger rounded-4 m-0">Lỗi không thể nạp danh sách dữ liệu. Vui lòng F5 thử lại.</div>';
        });
}

// ==========================================
// KHU VỰC: CHI TIẾT ĐƠN HÀNG BÊN BÁN (MANAGE ORDER SELLER DETAIL)
// ==========================================

function manageOrderSellerDetail_handleAjaxSubmit(event, actionName, formElement) {
    event.preventDefault();
    const formData = new FormData(formElement);

    // Bật hiệu ứng Loading an toàn
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Đang xử lý...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }

    fetch(`index.php?controller=manageorderseller&action=${actionName}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof Swal !== 'undefined') {
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
                alert(data.message);
                window.location.reload();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Lỗi', text: data.message });
            } else {
                alert(data.message);
            }
        }
    })
    .catch(err => {
        console.error("Fetch Error:", err);
        if (typeof Swal !== 'undefined') {
            Swal.fire('Lỗi', 'Không thể kết nối với máy chủ!', 'error');
        } else {
            alert('Lỗi: Không thể kết nối với máy chủ!');
        }
    });
}

// ==========================================
// KHU VỰC: DANH SÁCH ĐƠN HÀNG BÊN BÁN (MANAGE ORDER SELLER LIST)
// ==========================================

function manageOrderSellerList_switchTab(event, clickedTab) {
    event.preventDefault();
    const status = clickedTab.getAttribute('data-status');
    const container = document.getElementById('orderListContainer');
    const allTabs = document.querySelectorAll('.ajax-tab');

    // Dừng thực thi nếu không tìm thấy DOM (Bảo vệ cho trang khác)
    if (!container || allTabs.length === 0) return;

    // 1. Đổi style Tab
    allTabs.forEach(t => {
        t.classList.remove('active');
        t.style.backgroundColor = '';
        t.style.color = '#555';
    });
    
    clickedTab.classList.add('active');
    clickedTab.style.backgroundColor = '#FF7A3D';
    clickedTab.style.color = 'white';

    // 2. Loading state chờ phản hồi
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-warning" role="status"></div><p class="mt-2 text-muted">Đang tải...</p></div>';

    // 3. Gọi AJAX lấy data HTML theo Status mới
    fetch(`index.php?controller=manageorderseller&action=fetchList&status=${status}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Update nội dung HTML list
            container.innerHTML = data.html;
            
            // Cập nhật lại số lượng badge trên các tab một cách an toàn
            const count0 = document.getElementById('count-0');
            const count1 = document.getElementById('count-1');
            const count3 = document.getElementById('count-3');
            const count4 = document.getElementById('count-4');
            const count5 = document.getElementById('count-5');
            const count6 = document.getElementById('count-6');

            if(count0 && data.counts[0] !== undefined) count0.innerText = data.counts[0];
            if(count1 && data.counts[1] !== undefined) count1.innerText = data.counts[1];
            if(count3 && data.counts[3] !== undefined) count3.innerText = data.counts[3];
            if(count4 && data.counts[4] !== undefined) count4.innerText = data.counts[4];
            if(count5 && data.counts[5] !== undefined) count5.innerText = data.counts[5];
            if(count6 && data.counts[6] !== undefined) count6.innerText = data.counts[6];
        } else {
            container.innerHTML = '<div class="text-center py-5 text-danger">Lỗi dữ liệu từ máy chủ. Vui lòng F5 thử lại.</div>';
        }
    })
    .catch(error => {
        console.error("AJAX Error: ", error);
        container.innerHTML = '<div class="text-center py-5 text-danger">Lỗi tải dữ liệu. Vui lòng thử lại.</div>';
    });
}
// ==========================================
// KHU VỰC: KÊNH NGƯỜI BÁN - THỐNG KÊ (SELLER DASHBOARD)
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    const ctxRev = document.getElementById('revenueChart');
    const ctxStatus = document.getElementById('statusChart');
    const ctxTop = document.getElementById('topProductsChart');

    // Chỉ khởi tạo biểu đồ nếu đang ở trang Dashboard và có dữ liệu
    if (ctxRev && ctxStatus && ctxTop && window.sellerDashboardData) {
        const data = window.sellerDashboardData;

        // 1. Biểu đồ Doanh Thu (Line Chart)
        new Chart(ctxRev.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.revDates,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: data.revData,
                    borderColor: '#FF7A3D',
                    backgroundColor: 'rgba(255, 122, 61, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#FF7A3D',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value.toLocaleString('vi-VN') + ' đ' }
                    }
                }
            }
        });

        // 2. Biểu đồ Trạng thái (Doughnut Chart)
        new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.statusLabels,
                datasets: [{
                    data: data.statusCounts,
                    backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545', '#fd7e14', '#6c757d'],
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // 3. Biểu đồ Top Sản phẩm (Horizontal Bar Chart)
        new Chart(ctxTop.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.prodLabels,
                datasets: [{
                    label: 'Số lượng bán ra',
                    data: data.prodSold,
                    backgroundColor: '#0dcaf0',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', // Quay ngang thanh cột
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { beginAtZero: true, ticks: { stepSize: 1 } } 
                }
            }
        });
    }
});