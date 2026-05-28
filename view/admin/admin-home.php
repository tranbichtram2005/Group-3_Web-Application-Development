<?php require_once __DIR__ . '/../partials/admin-header.php'; ?>

<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$hour = date('H');
if ($hour >= 5 && $hour < 12) {
    $greeting = "Chào buổi sáng";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Chào buổi chiều";
} else {
    $greeting = "Chào buổi tối";
}
?>

<style>
    /* Bọc main và ép độ cao tối thiểu để đẩy Footer xuống đáy màn hình */
    .admin-main-wrapper {
        min-height: calc(100vh - 140px);
        display: flex;
        flex-direction: column;
        padding-bottom: 40px;
    }

    /* Banner đồng bộ màu với Header (#0f172a) */
    .admin-banner {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        height: 220px;
        position: relative;
        overflow: hidden;
        margin-top: -24px;
        margin-left: -24px;
        margin-right: -24px;
        border-radius: 0 0 24px 24px;
    }
    
    .floating-logo {
        position: absolute;
        font-size: 8rem;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.03); /* Chữ chìm mờ ảo */
        white-space: nowrap;
        animation: floatLogo 15s linear infinite alternate;
        top: 10%;
        left: -5%;
        user-select: none;
        pointer-events: none;
    }

    @keyframes floatLogo {
        0% { transform: translateX(0) translateY(0); }
        100% { transform: translateX(100px) translateY(-20px); }
    }

    /* Thanh tìm kiếm Khổng lồ */
    .search-wrapper {
        margin-top: -35px;
        position: relative;
        z-index: 10;
    }

    .search-input {
        border-radius: 50px;
        padding: 1.2rem 1.5rem 1.2rem 3.5rem;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        font-size: 1.1rem;
    }
    
    .search-input:focus {
        box-shadow: 0 8px 25px rgba(56, 189, 248, 0.25); /* Sáng viền xanh dương #38bdf8 */
        border-color: #38bdf8;
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 25px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.4rem;
        color: #64748b;
    }

    /* Khung kết quả tìm kiếm */
    .search-results {
        position: absolute;
        top: 110%;
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        display: none;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        border: 1px solid #e2e8f0;
    }

    .search-results a {
        padding: 14px 24px;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        color: #334155;
        text-decoration: none;
        transition: 0.2s;
    }

    .search-results a:hover {
        background-color: #f0f9ff;
        color: #0284c7;
        padding-left: 30px;
    }

    /* Các khối chức năng (Click được toàn bộ Khối) */
    .feature-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 30px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: #38bdf8;
    }
    
    .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 20px;
        transition: 0.3s;
    }

    .feature-card:hover .icon-box {
        transform: scale(1.1);
    }
</style>

<main class="container-fluid admin-main-wrapper px-4">
    
    <div class="admin-banner d-flex flex-column align-items-center justify-content-center text-center">
        <div class="floating-logo">2LIFE ADMIN SYSTEM</div>
        
        <h2 class="text-white fw-bold mb-2" style="position: relative; z-index: 2; letter-spacing: 0.5px;">
            <?= $greeting ?>, ADMIN
        </h2>
        <p class="text-white-50 fs-6 mb-4" style="position: relative; z-index: 2;">
            Tổng quan quản trị hệ thống
        </p>
    </div>

    <div class="container" style="max-width: 1000px; flex-grow: 1;">
        
        <div class="row justify-content-center">
            <div class="col-lg-10 search-wrapper">
                <div class="position-relative">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="adminSearch" class="form-control search-input" placeholder="Tìm tính năng nhanh (Ví dụ: Voucher, Bài đăng, Người dùng...)" autocomplete="off">
                    
                    <div id="searchResults" class="search-results"></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4 mt-5 text-dark"><i class="bi bi-stars text-warning me-2"></i>Truy cập nhanh</h5>
        <div class="row g-4">
            
            <div class="col-md-4">
                <a href="index.php?controller=approveseller" class="feature-card">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h6 class="fw-bold text-dark fs-5 mb-2">Duyệt Người bán</h6>
                    <p class="text-secondary small mb-0">Xác minh và phê duyệt các hồ sơ đăng ký mở cửa hàng.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="index.php?controller=approvelisting" class="feature-card">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h6 class="fw-bold text-dark fs-5 mb-2">Duyệt Tin đăng</h6>
                    <p class="text-secondary small mb-0">Kiểm tra nội dung các sản phẩm mới được đăng bán.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="index.php?controller=voucher" class="feature-card">
                    <div class="icon-box bg-info bg-opacity-10 text-info" style="color: #0ea5e9 !important;">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <h6 class="fw-bold text-dark fs-5 mb-2">Quản lý Voucher</h6>
                    <p class="text-secondary small mb-0">Tạo và phát hành mã giảm giá cho toàn sàn 2Life.</p>
                </a>
            </div>

        </div>
    </div>
</main>

<script>
    // Đã lọc bỏ các chức năng không cần thiết
    const adminFunctions = [
        { name: "Quản lý danh mục sản phẩm", url: "index.php?controller=category", icon: "bi-tags" },
        { name: "Quản lý người dùng", url: "index.php?controller=user", icon: "bi-people" },
        { name: "Phê duyệt người bán", url: "index.php?controller=approveseller", icon: "bi-person-check" },
        { name: "Phê duyệt tin đăng (Sản phẩm)", url: "index.php?controller=approvelisting", icon: "bi-journal-check" },
        { name: "Quản lý Blog / Bài viết", url: "index.php?controller=blog", icon: "bi-file-earmark-text" },
        { name: "Quản lý Voucher", url: "index.php?controller=voucher", icon: "bi-ticket-perforated" }
    ];

    const searchInput = document.getElementById('adminSearch');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase().trim();
        searchResults.innerHTML = ''; 
        
        if (keyword.length > 0) {
            const filtered = adminFunctions.filter(func => func.name.toLowerCase().includes(keyword));
            
            if (filtered.length > 0) {
                filtered.forEach(func => {
                    const link = document.createElement('a');
                    link.href = func.url;
                    link.innerHTML = `
                        <div class="d-flex align-items-center w-100">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                <i class="bi ${func.icon} text-secondary fs-5"></i>
                            </div>
                            <span class="fw-semibold text-dark">${func.name}</span>
                        </div>`;
                    searchResults.appendChild(link);
                });
            } else {
                searchResults.innerHTML = `<div class="p-4 text-muted text-center"><i class="bi bi-search d-block fs-3 mb-2 opacity-50"></i> Không tìm thấy chức năng phù hợp.</div>`;
            }
            searchResults.style.display = 'block';
        } else {
            searchResults.style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
</script>

<?php require_once __DIR__ . '/../partials/admin-footer.php'; ?>