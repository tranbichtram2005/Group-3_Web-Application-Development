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
    
    <div class="mb-5">
        <h4 class="fw-bold mb-4" style="color: var(--nav-color);">Danh Mục Nổi Bật</h4>
        <div class="d-flex gap-3 overflow-x-auto pb-2" style="scrollbar-width: thin;">
            <?php if(!empty($categories)): foreach($categories as $cat): ?>
            <a href="index.php?controller=listing&action=category&id=<?= $cat['id'] ?>" class="text-decoration-none text-center" style="min-width: 100px;">
                <div class="bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-center mx-auto mb-2" 
                     style="width: 70px; height: 70px; transition: 0.2s;" 
                     onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                    
                    <?php if(!empty($cat['icon_url'])): ?>
                        <img src="<?= htmlspecialchars($cat['icon_url']) ?>" alt="<?= $cat['name'] ?>" 
                             style="width: 40px; height: 40px; object-fit: contain;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="bi bi-box-seam fs-3" style="color: #FF7A3D; display: none;"></i>
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