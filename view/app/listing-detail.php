<?php 
$product = $product ?? [];
$images = $images ?? [];
$avgRating = $avgRating ?? 0;
$totalReviews = $totalReviews ?? 0;
$productReviews = $productReviews ?? [];
$activeVouchers = $activeVouchers ?? []; // Thêm biến hứng Voucher
?>
<?php require_once __DIR__ . '/../partials/user-header.php'; ?>

<style>
.btn-hover-zoom { transition: transform 0.2s, background-color 0.2s; }
.btn-hover-zoom:hover { transform: scale(1.02); background-color: #e66932 !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main class="container py-4" style="min-height: 75vh;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php?controller=home" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?></li>
        </ol>
    </nav>
    <div class="d-flex align-items-center gap-3 my-2">
        <div class="text-warning fs-5">
            <?php 
            $fullStars = floor($avgRating);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $fullStars) {
                    echo '<i class="bi bi-star-fill"></i>';
                } else if ($i == $fullStars + 1 && ($avgRating - $fullStars) >= 0.5) {
                    echo '<i class="bi bi-star-half"></i>';
                } else {
                    echo '<i class="bi bi-star"></i>';
                }
            }
            ?>
            <span class="text-dark ms-1 fw-bold"><?= $avgRating ?>/5</span>
        </div>
        <span class="text-secondary">|</span>
        <span class="text-muted fw-medium"><?= $totalReviews ?> Đánh giá</span>
    </div>
    
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="bg-white border rounded-4 overflow-hidden p-3 mb-4 shadow-sm text-center">
                <?php $mainImg = !empty($images) ? $images[0]['image_url'] : 'https://ui-avatars.com/api/?name=No+Image&background=f1f1f1&color=999&size=500'; ?>
                <img src="<?= htmlspecialchars($mainImg) ?>" id="mainProductImg" class="img-fluid object-fit-contain" style="height: 450px; width: 100%; background-color: #fafafa; border-radius: 8px;">
                
                <?php if(count($images) > 1): ?>
                    <div class="d-flex gap-2 justify-content-center mt-3 overflow-x-auto py-1">
                        <?php foreach($images as $img): ?>
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" class="img-thumbnail object-fit-cover rounded-3" style="width: 65px; height: 65px; cursor: pointer; transition: 0.2s;" onclick="document.getElementById('mainProductImg').src = this.src">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Thông tin chi tiết</h5>
                <div class="row g-3 mb-4 small text-secondary">
                    <div class="col-6 col-md-4"><i class="bi bi-tag-fill me-1 text-primary"></i> Danh mục: <strong class="text-dark"><?= htmlspecialchars($product['category_name'] ?? 'Khác') ?></strong></div>
                    <div class="col-6 col-md-4"><i class="bi bi-info-circle-fill me-1 text-primary"></i> Tình trạng: <strong class="text-dark"><?= htmlspecialchars($product['condition_name'] ?? 'Cũ') ?></strong></div>
                    <div class="col-6 col-md-4"><i class="bi bi-box-seam-fill me-1 text-primary"></i> Số lượng: <strong class="text-dark"><?= htmlspecialchars($product['stock_quantity'] ?? 0) ?></strong></div>
                </div>
                <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Mô tả sản phẩm</h5>
                <p class="text-dark lh-base" style="white-space: pre-line;"><?= htmlspecialchars($product['description'] ?? 'Người bán không để lại mô tả sản phẩm.') ?></p>
            </div>

            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                    <i class="bi bi-chat-left-heart-fill text-warning me-2"></i>Đánh giá từ người mua thực tế
                </h5>

                <div class="row align-items-center bg-light p-3 rounded-3 mb-4 g-3 mx-0 border">
                    <div class="col-md-4 text-center border-end border-2 border-white">
                        <h1 class="text-warning fw-bold mb-0 display-5"><?= $avgRating ?></h1>
                        <p class="text-muted small mb-1">trên 5 sao</p>
                        <div class="text-warning fs-5">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= floor($avgRating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="col-md-8 px-4 text-center text-md-start">
                        <?php $currentStar = isset($_GET['star']) ? (int)$_GET['star'] : 0; ?>
                        
                        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>" 
                               class="btn btn-<?= $currentStar === 0 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               Tất cả (<?= $totalReviews ?>)
                            </a>
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>&star=5" 
                               class="btn btn-<?= $currentStar === 5 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               5 Sao
                            </a>
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>&star=4" 
                               class="btn btn-<?= $currentStar === 4 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               4 Sao
                            </a>
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>&star=3" 
                               class="btn btn-<?= $currentStar === 3 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               3 Sao
                            </a>
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>&star=2" 
                               class="btn btn-<?= $currentStar === 2 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               2 Sao
                            </a>
                            <a href="index.php?controller=listing&action=detail&id=<?= $product['id'] ?>&star=1" 
                               class="btn btn-<?= $currentStar === 1 ? 'warning text-dark fw-bold' : 'outline-secondary bg-white' ?> rounded-pill px-3 py-1 fs-6 shadow-sm">
                               1 Sao
                            </a>
                        </div>
                    </div>
                </div>

                <div class="review-list">
                    <?php if (empty($productReviews)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Sản phẩm này hiện tại chưa có đánh giá nào từ người mua.
                        </div>
                    <?php else: ?>
                        <?php foreach ($productReviews as $review): ?>
                            <div class="py-3 border-bottom <?php echo next($productReviews) ? '' : 'border-0'; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong class="text-dark small d-block"><?= htmlspecialchars($review['reviewer_name']) ?></strong>
                                        <div class="text-warning my-1" style="font-size: 13px;">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></span>
                                </div>
                                <div class="bg-light p-2 rounded-2 text-secondary small mt-2 d-inline-block w-100">
                                    <?= nl2br(htmlspecialchars($review['comment'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="sticky-top" style="top: 20px; z-index: 1;">
                
                <div class="bg-white border rounded-4 p-4 shadow-sm mb-3">
                    <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['title'] ?? '') ?></h4>
                    <div class="fw-bold display-6 mb-3" style="color: #FF7A3D;"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</div>
                    
                    <div class="text-muted small mb-3 d-flex align-items-center gap-1">
                        <i class="bi bi-geo-alt-fill text-danger"></i><span>Khu vực: <?= htmlspecialchars($product['ward_name'] ?? 'Toàn quốc') ?></span>
                    </div>

                    <?php 
                        $currentPrice = $product['price'] ?? 0;
                        $applicableVouchers = [];
                        if (!empty($activeVouchers)) {
                            foreach ($activeVouchers as $v) {
                                if ($v['min_order_value'] <= $currentPrice) {
                                    $applicableVouchers[] = $v;
                                }
                            }
                        }
                    ?>

                    <?php if (!empty($applicableVouchers)): ?>
                    <div class="mb-4 p-3 rounded-3 shadow-sm" style="background-color: #fff9f6; border: 1px dashed #FF7A3D;">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Mã giảm giá áp dụng được:</h6>
                        
                        <div class="d-flex flex-column gap-2">
                            <?php foreach (array_slice($applicableVouchers, 0, 3) as $v): ?>
                                <?php 
                                    $discountText = ($v['type_id'] == 1) ? "Giảm {$v['discount_value']}%" : "Giảm " . number_format($v['discount_value'], 0, ',', '.') . "đ"; 
                                    $remaining = $v['total_quantity'] - $v['used_quantity'];
                                ?>
                                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border border-warning-subtle shadow-sm">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge" style="background-color: #FF7A3D;">Mã: <?= htmlspecialchars($v['code']) ?></span>
                                            <span class="small fw-bold text-dark"><?= $discountText ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="text-danger fw-medium" style="font-size: 0.7rem;">Còn <?= $remaining ?> lượt</span>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <button class="btn btn-sm text-white py-0 px-3 rounded-pill" style="font-size: 0.75rem; background-color: #FF7A3D;" onclick="window.detailCopyVoucherCode('<?= htmlspecialchars($v['code']) ?>')">Lưu</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm text-white py-0 px-3 rounded-pill" style="font-size: 0.75rem; background-color: #FF7A3D;" onclick="window.detailRequireLoginToCopy()">Lưu</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-3 mt-3">
                        <?php 
                        if(isset($product['stock_quantity']) && $product['stock_quantity'] > 0 && isset($product['status_id']) && $product['status_id'] != 3): 
                        ?>
                            <div class="row g-2">
                                <div class="col-4">
                                    <button type="button" onclick="window.detailActionChat(<?= $product['user_id'] ?? 0 ?>, <?= $product['id'] ?? 0 ?>)" class="btn btn-outline-dark w-100 py-2.5 rounded-3 fw-semibold small" style="border-color: #1F3C5A; color: #1F3C5A;"><i class="bi bi-chat-text d-block fs-5 mb-0.5"></i> Chat</button>
                                </div>
                                <div class="col-8">
                                    <button type="button" onclick="window.detailAddToCart(<?= $product['id'] ?? 0 ?>)" class="btn btn-outline-warning w-100 h-100 py-2.5 rounded-3 fw-bold" style="border-color: #FF7A3D; color: #FF7A3D;"><i class="bi bi-cart-plus fs-5 me-1"></i> Thêm vào giỏ</button>
                                </div>
                            </div>
                            
                            <button type="button" onclick="window.detailBuyNow(<?= $product['id'] ?? 0 ?>)" class="btn btn-lg text-white fw-bold py-3 rounded-3 shadow-sm btn-hover-zoom" style="background-color: #FF7A3D; border: none; font-size: 16px;">MUA NGAY (Giao dịch an toàn)</button>

                            <button type="button" class="btn btn-sm btn-light border w-100 py-2 rounded-3 text-secondary fw-semibold small" onclick="window.detailActionChat(<?= $product['user_id'] ?? 0 ?>, <?= $product['id'] ?? 0 ?>, true)"><i class="bi bi-tags me-1 text-warning"></i> Bạn muốn trả giá? Yêu cầu thương lượng</button>                        
                        <?php else: ?>
                            <button class="btn btn-lg btn-secondary fw-bold py-3 rounded-3 shadow-sm" disabled><i class="bi bi-x-circle me-2"></i> Sản phẩm đã hết hàng</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= !empty($product['avatar_url']) ? htmlspecialchars($product['avatar_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($product['username'] ?? 'User') . '&background=1F3C5A&color=fff' ?>" class="rounded-circle object-fit-cover border shadow-sm" width="55" height="55">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($product['full_name'] ?? '') ?></h6>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <small class="text-muted">@<?= htmlspecialchars($product['username'] ?? '') ?></small>
                                <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.8rem;" title="Người bán đã xác thực"></i>
                            </div>
                        </div>
                    </div>
                    <a href="index.php?controller=shop&id=<?= $product['user_id'] ?? 0 ?>" class="btn btn-sm btn-outline-dark w-100 py-2 rounded-3 fw-bold small transition-all" style="border-color: #e2e8f0;">
                        <i class="bi bi-shop me-2 text-warning"></i> Xem cửa hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    window.currentUserId = <?= $_SESSION['user_id'] ?? 0 ?>;
</script>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>