<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
    require_once __DIR__ . '/admin-footer.php';
    return; 
}
?>

<footer class="text-center py-4 text-white mt-auto" style="background-color: var(--nav-color); border-top: 3px solid var(--btn-primary);">
    <div class="container">
        <h5 class="fw-bold mb-1" style="color: var(--btn-primary); letter-spacing: -0.5px; font-size: 20px;">2Life MARKETPLACE</h5>
        <p class="small text-white-50 mb-3" style="font-size: 13px;">Nền tảng trao đổi đồ cũ uy tín và tiết kiệm dành cho sinh viên.</p>
        
        <div class="d-flex justify-content-center gap-4 mb-3" style="font-size: 13.5px;">
            <a href="index.php?controller=home" class="text-white-50 text-decoration-none nav-icon-hover">Trang chủ</a>
            <a href="index.php?controller=info&action=index&tab=rules" class="text-white-50 text-decoration-none nav-icon-hover">Quy chế hoạt động</a>
            <a href="index.php?controller=info&action=index&tab=privacy" class="text-white-50 text-decoration-none nav-icon-hover">Chính sách bảo mật</a>
            <a href="index.php?controller=info&action=index&tab=contact" class="text-white-50 text-decoration-none nav-icon-hover">Liên hệ hỗ trợ</a>
        </div>

        <div class="border-top border-secondary my-3 opacity-25"></div>
        
        <p class="text-white-50 mb-0" style="font-size: 12px; opacity: 0.8;">
            © 2026 2Life. Phát triển bởi Nhóm 3 (Phát triển ứng dụng Web UEH).
        </p>
    </div>
</footer>

<button onclick="openSupportModal()" 
   class="btn shadow-lg d-flex align-items-center justify-content-center text-white border-0" 
   style="position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; background-color: #FF7A3D; border-radius: 50%; z-index: 9999; transition: transform 0.2s;" 
   onmouseover="this.style.transform='scale(1.1)'" 
   onmouseout="this.style.transform='scale(1)'" 
   title="Chat hỗ trợ">
    <i class="bi bi-headset fs-4"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="layout/script.js"></script>

<script>

function openSupportModal() {
    Swal.fire({
        title: '<h5 class="fw-bold">Bạn đang gặp vấn đề gì?</h5>',
        html: `<select id="support-category" class="form-select mt-3 py-2">
                <option value="1">📦 Vấn đề Đơn hàng</option>
                <option value="2">💳 Thanh toán & Hoàn tiền</option>
                <option value="3">⚠️ Tố cáo vi phạm</option>
                <option value="10">💬 Khác</option>
               </select>`,
        confirmButtonText: 'Bắt đầu Chat <i class="bi bi-send ms-1"></i>',
        confirmButtonColor: '#FF7A3D',
        showCancelButton: true,
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            let catId = document.getElementById('support-category').value;
            window.location.href = `index.php?controller=chat&action=startSupport&cat_id=${catId}`;
        }
    });
}

let searchTimeout = null; // Biến lưu thời gian chờ

document.getElementById('searchInput').addEventListener('input', function() {
    let keyword = this.value.trim();
    let suggestBox = document.getElementById('search-suggest');

    // 1. Gõ ít hơn 2 chữ thì giấu hộp gợi ý đi
    if (keyword.length < 2) {
        suggestBox.classList.add('d-none');
        return;
    }

    // 2. KỸ THUẬT DEBOUNCE: Xóa cái lệnh cũ nếu người dùng đang gõ tiếp
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    // 3. Đặt đồng hồ đếm ngược (400ms = 0.4 giây). Dừng gõ 0.4s mới bắt đầu tìm
    searchTimeout = setTimeout(async () => {
        try {
            // Thêm hiệu ứng loading cho xịn
            suggestBox.innerHTML = `<div class="p-3 text-muted small text-center"><div class="spinner-border spinner-border-sm text-secondary mb-1" role="status"></div><br>Đang tìm kiếm...</div>`;
            suggestBox.classList.remove('d-none');

            // Gọi API (có thêm encodeURIComponent để an toàn)
            let res = await fetch(`index.php?controller=listing&action=suggestAjax&keyword=${encodeURIComponent(keyword)}`);
            let data = await res.json();

            if (data && data.length > 0) {
                let html = ''; // CHÍNH LÀ THIẾU CÁI DÒNG NÀY ĐÂY!!! Khai báo rỗng trước khi vòng lặp chạy
                
                data.forEach(item => {
                    let priceFormatted = new Intl.NumberFormat('vi-VN').format(item.price) + 'đ';
                    
                    // Nâng cấp: Check nếu hết hàng (số lượng = 0 HOẶC trạng thái = 3)
                    let isOutOfStock = (item.stock_quantity <= 0 || item.status_id == 3);
                    let opacityClass = isOutOfStock ? 'opacity-50' : '';
                    let stockBadge = isOutOfStock ? '<span class="badge bg-secondary ms-2" style="font-size: 10px;">Hết hàng</span>' : '';
                    
                    // Gài ảnh 2Life nếu bị mất ảnh
                    let img = item.image_url ? item.image_url : 'https://ui-avatars.com/api/?name=2+Life&background=f1f1f1&color=999';
                    
                    html += `
                    <a href="index.php?controller=listing&action=detail&id=${item.id}" class="d-flex align-items-center gap-3 p-2 text-decoration-none border-bottom text-dark ${opacityClass}" style="transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                        <img src="${img}" onerror="this.src='https://ui-avatars.com/api/?name=2+Life&background=f1f1f1&color=999'" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                        <div class="d-flex flex-column overflow-hidden w-100">
                            <div class="text-truncate fw-semibold text-dark" style="font-size: 14px;">${item.title} ${stockBadge}</div>
                            <div class="fw-bold" style="font-size: 13px; color: #FF7A3D;">${priceFormatted}</div>
                        </div>
                    </a>`;
                });
                suggestBox.innerHTML = html;
            } else {
                suggestBox.innerHTML = `<div class="p-3 text-muted small text-center"><i class="bi bi-search text-secondary opacity-50 mb-1 fs-5 d-block"></i>Không tìm thấy sản phẩm</div>`;
            }
        } catch (e) {
            console.error("Lỗi live search:", e);
            suggestBox.innerHTML = `<div class="p-3 text-danger small text-center">Có lỗi xảy ra, thử lại sau.</div>`;
        }
    }, 400); 
});

// Ẩn hộp gợi ý khi click ra ngoài
document.addEventListener('click', function(e) {
    let searchInput = document.getElementById('searchInput');
    let searchSuggest = document.getElementById('search-suggest');
    if (searchInput && searchSuggest && !searchInput.contains(e.target) && !searchSuggest.contains(e.target)) {
        searchSuggest.classList.add('d-none');
    }
});
</script>
<?php if (isset($_SESSION['show_unauth_modal']) && $_SESSION['show_unauth_modal'] === true): ?>
    
    <?php 
        // Nạp giao diện Modal vào ngay cuối trang
        require_once __DIR__ . '/../unauthorized_modal.php'; 
    ?>
    
    <?php unset($_SESSION['show_unauth_modal']); ?>

<?php endif; ?>
</body>
</html>