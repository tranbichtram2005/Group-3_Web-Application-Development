<?php 
// 1. CODE PHÒNG THỦ: Khởi tạo giá trị mặc định nếu Controller chưa truyền sang
$page = $page ?? 1;
$totalPages = $totalPages ?? 0;
$categories = $categories ?? [];
$listings = $listings ?? [];
?>
<?php require_once __DIR__ . '/../partials/user-header.php'; ?>

<section class="hero-section text-center text-white position-relative mt-2 mx-3 rounded-4 overflow-hidden shadow-sm" style="background-image: linear-gradient(rgba(31, 60, 90, 0.6), rgba(31, 60, 90, 0.6)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80'); padding: 80px 0; background-size: cover; background-position: center;">
    <h1 class="fw-bold display-5 mb-3">Tái sử dụng - Tiết kiệm - Bền vững</h1>
    <p class="fs-5 mb-4">Nền tảng trao đổi đồ cũ uy tín nhất dành cho sinh viên</p>
</section>

<main class="container py-5">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .voucher-scroller { display: flex; gap: 0.8rem; overflow-x: auto; padding-bottom: 10px; scroll-snap-type: x mandatory; }
        .voucher-scroller::-webkit-scrollbar { height: 4px; }
        .voucher-scroller::-webkit-scrollbar-thumb { background: #FF7A3D; border-radius: 10px; }
        
        .voucher-ticket { 
            min-width: 240px; height: 110px; scroll-snap-align: start; 
            background: linear-gradient(135deg, #fff3ee, #ffe6db); 
            border: 1px solid #ffccb8; border-radius: 10px; 
            display: flex; position: relative; overflow: hidden; 
        }
        .voucher-left { 
            background: #FF7A3D; color: #fff; padding: 10px; 
            display: flex; flex-direction: column; justify-content: center; align-items: center; 
            border-right: 2px dashed #fff; width: 30%; 
        }
        .voucher-right { 
            padding: 10px 12px; width: 70%; 
            display: flex; flex-direction: column; justify-content: center; 
        }
        .copy-btn { 
            background: #FF7A3D; color: white; border: none; 
            padding: 3px 10px; border-radius: 12px; 
            font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: 0.2s; 
        }
        .copy-btn:hover { background: #e66a35; }
    </style>

    <?php if (!empty($activeVouchers)): ?>
    <div class="mb-5 mt-4">
        <h5 class="fw-bold mb-3" style="color: var(--nav-color);"><i class="bi bi-ticket-perforated text-danger me-2"></i>Mã Giảm Giá Dành Cho Bạn</h5>
        <div class="voucher-scroller">
            <?php foreach ($activeVouchers as $v): 
                $discountText = ($v['type_id'] == 1) ? "Giảm {$v['discount_value']}%" : "Giảm " . number_format($v['discount_value'], 0, ',', '.') . "đ";
                $remaining = $v['total_quantity'] - $v['used_quantity'];
            ?>
                <div class="voucher-ticket shadow-sm">
                    <div class="voucher-left">
                        <span class="fs-5 fw-bold">2LIFE</span>
                        <small style="font-size: 0.65rem;">Voucher</small>
                    </div>
                    <div class="voucher-right">
                        <h6 class="fw-bold text-danger mb-1" style="font-size: 0.9rem;"><?= $discountText ?></h6>
                        <p class="text-muted mb-1" style="font-size: 0.75rem;">Đơn từ <?= number_format($v['min_order_value'], 0, ',', '.') ?>đ</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div class="fw-semibold text-dark" style="font-size: 0.75rem;">
                                Mã: <span class="font-monospace user-select-all"><?= htmlspecialchars($v['code']) ?></span>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="copy-btn" onclick="copyVoucherCode('<?= htmlspecialchars($v['code']) ?>')">Lưu</button>
                            <?php else: ?>
                                <button class="copy-btn" onclick="requireLoginToCopy()">Lưu</button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="progress mt-2" style="height: 3px;">
                            <?php $percent = ($v['total_quantity'] > 0) ? ($v['used_quantity'] / $v['total_quantity']) * 100 : 0; ?>
                            <div class="progress-bar bg-danger" style="width: <?= $percent ?>%"></div>
                        </div>
                        <small class="text-danger mt-1" style="font-size: 0.65rem;">Chỉ còn <?= $remaining ?> lượt</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <script>
    function copyVoucherCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Lưu mã thành công!',
                text: 'Mã: ' + code,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                iconColor: '#FF7A3D'
            });
        }).catch(err => {
            console.error('Lỗi khi copy: ', err);
        });
    }

    function requireLoginToCopy() {
        Swal.fire({
            icon: 'info',
            title: 'Khoan đã!',
            text: 'Cậu cần đăng nhập để lưu mã giảm giá này nhé!',
            confirmButtonText: 'Đăng nhập ngay',
            confirmButtonColor: '#FF7A3D',
            showCancelButton: true,
            cancelButtonText: 'Để sau'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?controller=auth&action=login';
            }
        });
    }
    </script>
    
    <div class="mb-5">
        <h4 class="fw-bold mb-4" style="color: var(--nav-color);">Danh Mục Nổi Bật</h4>
        <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: thin;">
<?php if(!empty($categories)): foreach($categories as $cat): ?>
<a href="index.php?controller=listing&action=category&id=<?= $cat['id'] ?>" class="text-decoration-none text-center" style="min-width: 100px;">
    <div class="bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-center mx-auto mb-2" 
         style="width: 70px; height: 70px; transition: 0.2s;">
        
        <?php if(!empty($cat['icon_url'])): ?>
            <img src="<?= htmlspecialchars($cat['icon_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" 
                 style="width: 40px; height: 40px; object-fit: contain;">
        <?php else: ?>
            <i class="bi bi-box-seam fs-3" style="color: #FF7A3D;"></i>
        <?php endif; ?>

    </div>
    <span class="small text-dark fw-semibold"><?= htmlspecialchars($cat['name']) ?></span>
</a>
<?php endforeach; endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
        <h4 class="fw-bold mb-0" style="color: var(--nav-color);">
            <i class="bi bi-stars" style="color: var(--btn-primary);"></i> 
            <?php 
                if (isset($_GET['keyword']) && $_GET['keyword'] !== '') {
                    echo "Kết quả tìm kiếm cho: '<span class='text-danger'>" . htmlspecialchars($_GET['keyword']) . "</span>'";
                } else {
                    echo "Tin Đăng Mới Nhất";
                }
            ?>
        </h4>
    </div>

    <div class="row g-3 g-md-4">
        <?php if(!empty($listings)): foreach($listings as $item): ?>
        
        <?php 
            // Gom logic check hết hàng: Nếu tồn kho <= 0 HOẶC trạng thái = 3 (Đã bán)
            $isSoldOut = (isset($item['stock_quantity']) && $item['stock_quantity'] <= 0) || (isset($item['status_id']) && $item['status_id'] == 3); 
        ?>

        <div class="col-6 col-md-4 col-lg-3">
            <a href="index.php?controller=listing&action=detail&id=<?= $item['id'] ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm card-product" style="border-radius: 12px; transition: transform 0.3s; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    
                    <div class="position-relative">
                        <img src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://ui-avatars.com/api/?name=2+Life&background=f1f1f1&color=999' ?>" 
     onerror="this.src='https://ui-avatars.com/api/?name=2+Life&background=f1f1f1&color=999'"
     class="card-img-top object-fit-cover" 
     style="height: 200px;" 
     alt="<?= htmlspecialchars($item['title']) ?>">
                        
                        <?php if($isSoldOut): ?>
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 255, 255, 0.6); z-index: 10;">
                                <span class="badge bg-secondary border border-white text-white fs-6 px-3 py-2 shadow">HẾT HÀNG</span>
                            </div>
                        <?php elseif(isset($item['condition_id']) && $item['condition_id'] == 1): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 shadow-sm">Mới 100%</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-3 d-flex flex-column <?= $isSoldOut ? 'opacity-50' : '' ?>">
                        <h6 class="text-dark fw-bold mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px; line-height: 1.4;">
                            <?= htmlspecialchars($item['title']) ?>
                        </h6>
                        <div class="fw-bold fs-5 mb-2" style="color: #FF7A3D;">
                            <?= number_format($item['price'], 0, ',', '.') ?>đ
                        </div>
                        <div class="mt-auto d-flex justify-content-between align-items-center text-muted small">
                            <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars((string)($item['ward_name'] ?? 'Toàn quốc')) ?></span>
                        </div>
                    </div>

                </div>
            </a>
        </div>
        <?php endforeach; else: ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="120" class="mb-3 opacity-50">
                <h5 class="text-secondary fw-bold">Không tìm thấy sản phẩm nào!</h5>
                <p class="text-muted">Thử tìm kiếm với từ khóa khác xem sao nhé.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if(isset($totalPages) && $totalPages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php 
                // Giữ lại keyword (nếu có) trên URL khi bấm chuyển trang
                $kwParam = isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '';
                // Giữ lại controller (home hoặc product)
                $ctrlParam = isset($_GET['controller']) ? $_GET['controller'] : 'home';
                $actParam = isset($_GET['action']) ? '&action='.$_GET['action'] : '';
            ?>
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm" href="index.php?controller=<?= $ctrlParam ?><?= $actParam ?><?= $kwParam ?>&page=<?= $page - 1 ?>">Trước</a>
            </li>
            
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link shadow-sm" href="index.php?controller=<?= $ctrlParam ?><?= $actParam ?><?= $kwParam ?>&page=<?= $i ?>" <?= ($page == $i) ? 'style="background-color: #FF7A3D; border-color: #FF7A3D;"' : '' ?>><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm" href="index.php?controller=<?= $ctrlParam ?><?= $actParam ?><?= $kwParam ?>&page=<?= $page + 1 ?>">Sau</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>