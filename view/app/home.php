<?php
// 1. GỌI HEADER VÀO ĐÂY (Đã bao gồm CSS, Menu, Dropdown, Check đăng nhập...)
require_once __DIR__ . '/../partials/user-header.php';
?>

<section class="hero-section text-center text-white" style="background-image: linear-gradient(rgba(31, 60, 90, 0.7), rgba(31, 60, 90, 0.7)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1000&q=80'); padding: 100px 0;">
    <h1 class="fw-bold display-5 mb-3">Tái sử dụng - Tiết kiệm - Bền vững</h1>
    <p class="fs-5 mb-4">Nền tảng trao đổi đồ cũ uy tín nhất dành cho sinh viên</p>
    <a href="#explore" class="btn-2life-primary px-4 py-2 fs-5 text-decoration-none">Khám phá ngay</a>
</section>

<main id="explore" class="container py-5">
    <h3 class="fw-bold mb-4" style="color: var(--nav-color);"><i class="bi bi-stars" style="color: var(--btn-primary);"></i> Tin đăng mới nhất</h3>
    <div class="row g-4">
        <div class="col-md-3">
            <a href="view/public/product detail.html" class="text-decoration-none">
                <div class="card-white overflow-hidden" style="transition: 0.3s;">
                    <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=400&q=80" style="width: 100%; height: 200px; object-fit: cover;">
                    <div class="p-3">
                        <span class="tag-2life mb-2">Thời trang</span>
                        <h6 class="text-dark fw-bold text-truncate">Áo khoác da bò Vintage</h6>
                        <div class="fs-5 fw-bold" style="color: var(--btn-primary);">550.000đ</div>
                        <small class="text-secondary"><i class="bi bi-geo-alt"></i> Hà Nội</small>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-3">
            <div class="card-white overflow-hidden">
                <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&q=80" style="width: 100%; height: 200px; object-fit: cover;">
                <div class="p-3">
                    <span class="tag-2life tag-blue mb-2">Đồ nữ</span>
                    <h6 class="text-dark fw-bold text-truncate">Áo khoác jean Unisex</h6>
                    <div class="fs-5 fw-bold" style="color: var(--btn-primary);">320.000đ</div>
                    <small class="text-secondary"><i class="bi bi-geo-alt"></i> TP. HCM</small>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// 2. GỌI FOOTER VÀO ĐÂY (Đã bao gồm JS Bootstrap, app.js và thẻ đóng HTML)
require_once __DIR__ . '/../partials/user-footer.php';
?>