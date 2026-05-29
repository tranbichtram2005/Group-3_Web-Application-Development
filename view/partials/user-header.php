<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
// PHÉP THUẬT Ở ĐÂY: Nếu là Admin, tự động tráo thành Header Admin và DỪNG load Header User
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
    require_once __DIR__ . '/admin-header.php'; // Đổi lại tên file cho đúng nếu cần
    return; // Lệnh return này cực kỳ quyền lực, nó sẽ cắt đứt không cho code của user-header chạy tiếp!
}

// Hứng dữ liệu từ index.php truyền sang thông qua biến toàn cục GLOBALS
$cartCount = $GLOBALS['cartCount'] ?? 0;
$notiCount = $GLOBALS['notiCount'] ?? 0;
$msgCount  = $GLOBALS['msgCount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Life Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
</head>
<body>

<header class="navbar-2life py-2">
    <div class="container-fluid px-3 px-md-4">
<div class="row align-items-center g-2">            
            <div class="col-4 col-md-2">
                <a href="index.php?controller=home" class="logo text-decoration-none">2Life</a>
            </div>

            <div class="col-12 col-md-5 order-3 order-md-2 mt-2 mt-md-0">
<form action="index.php" method="GET" class="d-flex align-items-center bg-white rounded-pill px-3 border custom-search-bar" style="height: 38px; max-width: 480px; margin: 0 auto; position: relative;">
    <input type="hidden" name="controller" value="listing"> 
    <input type="hidden" name="action" value="search">
    <input type="text" name="keyword" id="search-input" class="form-control border-0 shadow-none bg-transparent p-0" placeholder="Tìm kiếm đồ cũ giá hời..." style="font-size: 14px;" autocomplete="off">
    <button type="submit" class="border-0 bg-transparent text-secondary p-0 ms-2"><i class="bi bi-search"></i></button>
    <!-- Khung gợi ý -->
    <div id="search-results" class="position-absolute w-100 bg-white shadow-sm mt-1 rounded border d-none" style="top: 100%; left: 0; z-index: 999; max-height: 300px; overflow-y: auto;"></div>
</form>
            </div>

            <div class="col-8 col-md-5 order-2 order-md-3 d-flex justify-content-end align-items-center gap-3 gap-md-4">
                
                <?php if ($isLoggedIn): ?>
<a href="index.php?controller=listing&action=create" class="btn btn-2life-primary d-flex rounded-pill py-1 px-3 fw-bold align-items-center" style="font-size: 13px;">
    <i class="bi bi-plus-circle me-sm-1"></i> 
    <span class="d-none d-sm-inline">Đăng tin</span>
</a>

                    <a href="index.php?controller=cart" class="position-relative text-white text-decoration-none nav-icon-hover" title="Giỏ hàng">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>

                    <a href="index.php?controller=chat" class="position-relative text-white text-decoration-none nav-icon-hover" title="Tin nhắn">
                        <i class="bi bi-chat-dots fs-5"></i>
                        <span id="global-msg-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger <?= ($msgCount > 0) ? '' : 'd-none' ?>" style="font-size: 9px; padding: 2px 4px;"><?= $msgCount ?></span>
                    </a>

                    <a href="#" class="position-relative text-white text-decoration-none nav-icon-hover" title="Thông báo">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($notiCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;"><?= $notiCount ?></span>
                        <?php endif; ?>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="text-white text-decoration-none d-flex align-items-center gap-1 nav-icon-hover dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-lg-inline fw-semibold" style="font-size: 14px;">Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Thành viên') ?></span>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 py-2" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 250px;">
                            <li>
                                <a class="dropdown-item nav-dropdown-item" href="index.php?controller=order&action=index">
                                    <i class="bi bi-bag-check"></i> Đơn mua của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-dropdown-item" href="index.php?controller=manageorderseller&action=index">
                                    <i class="bi bi-receipt"></i> Đơn bán của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-dropdown-item" href="index.php?controller=dashboard">
                                    <i class="bi bi-graph-up-arrow"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-dropdown-item" href="index.php?controller=manage_listing&action=index">
                                    <i class="bi bi-card-list"></i> Quản lý tin đăng
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-dropdown-item" href="index.php?controller=profile">
                                    <i class="bi bi-person-gear"></i> Tài khoản của tôi
                                </a>
                            </li>
                            
                            <li><hr class="dropdown-divider my-2 mx-3 text-secondary opacity-25"></li>
                            
                            <li>
                                <a class="dropdown-item nav-dropdown-item text-danger fw-bold" href="index.php?controller=auth&action=logout">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="btn-2life-outline text-white border-white rounded-pill px-3 py-1 text-decoration-none" style="font-size: 13px;">Đăng nhập</a>
                    <a href="index.php?controller=auth&action=register" class="btn btn-2life-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 13px;">Đăng ký</a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

<?php if ($isLoggedIn): ?>
<script>
    // RADAR QUÉT TIN NHẮN CHƯA ĐỌC TOÀN HỆ THỐNG
    setInterval(async () => {
        try {
            let res = await fetch('index.php?controller=chat&action=index&ajax_radar=1');
            let json = await res.json(); 
            
            if(json.status === 'success') {
                let badge = document.getElementById('global-msg-badge');
                if(badge) {
                    if(json.total > 0) {
                        badge.textContent = json.total;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }
                window.dispatchEvent(new CustomEvent('unreadCountsUpdated', { detail: json.per_conv }));
            }
        } catch(e) {}
    }, 3000); 
</script>
<?php endif; ?>

<script>
const searchInput = document.getElementById('search-input');
const resultBox = document.getElementById('search-results');
let debounceTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const currentSearchValue = searchInput.value.trim();
    
    // Nếu gõ ít hơn 2 ký tự thì tắt box tìm kiếm, không làm gì cả
    if (currentSearchValue.length < 2) {
        resultBox.classList.add('d-none');
        resultBox.innerHTML = ''; 
        return;
    }

    // 1. Hiển thị Loading ngay lập tức
    resultBox.innerHTML = '<div class="p-3 text-center text-muted" style="font-size:13px;">Đang tìm kiếm...</div>';
    resultBox.classList.remove('d-none');

    // 2. Debounce 300ms
    debounceTimer = setTimeout(async () => {
        try {
            let res = await fetch(`index.php?controller=listing&action=suggestAjax&keyword=${encodeURIComponent(currentSearchValue)}`);
            let products = await res.json();
            
            // 🔥 CHỐT CHẶN RACE CONDITION: 
            // Nếu người dùng đã gõ chữ mới hoặc xóa sạch rồi thì không được render kết quả cũ
            if (searchInput.value.trim() !== currentSearchValue) return; 

            // 3. Render kết quả (hoặc thông báo rỗng) CHỈ KHI ĐÃ CÓ DATA
            if (products.length > 0) {
                resultBox.innerHTML = products.map(p => `
                    <a href="index.php?controller=listing&action=detail&id=${p.id}" class="suggestion-item border-bottom">
                        <img src="${p.image_url || 'assets/default.jpg'}" alt="img">
                        <div class="suggestion-info">
                            <div class="text-truncate">${p.title}</div>
                            <div style="color: #FF7A3D; font-weight: 600;">${new Intl.NumberFormat('vi-VN').format(p.price)}đ</div>
                        </div>
                    </a>
                `).join('');
            } else {
                resultBox.innerHTML = '<div class="p-3 text-muted text-center" style="font-size:13px;">Không tìm thấy kết quả.</div>';
            }
            // Đảm bảo box luôn hiện khi có kết quả
            resultBox.classList.remove('d-none');
            
        } catch(e) { 
            console.error(e);
            resultBox.classList.add('d-none');
        }
    }, 300);
});

// Ẩn khung khi click ra ngoài
document.addEventListener('click', (e) => {
    if(!resultBox.contains(e.target) && e.target !== searchInput) resultBox.classList.add('d-none');
});
</script>

</header>