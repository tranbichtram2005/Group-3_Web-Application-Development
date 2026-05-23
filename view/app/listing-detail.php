<?php 
// Code phòng thủ chống lỗi
$product = $product ?? [];
$images = $images ?? [];
?>
<?php require_once __DIR__ . '/../partials/user-header.php'; ?>

<main class="container py-4" style="min-height: 75vh;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php?controller=home" class="text-decoration-none text-secondary">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="bg-white border rounded-4 overflow-hidden p-3 mb-4 shadow-sm text-center">
                <?php $mainImg = !empty($images) ? $images[0]['image_url'] : 'https://ui-avatars.com/api/?name=No+Image&background=f1f1f1&color=999&size=500'; ?>
                <img src="<?= htmlspecialchars($mainImg) ?>" id="mainProductImg" class="img-fluid object-fit-contain" style="max-height: 450px; width: 100%;">
                
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
        </div>

        <div class="col-12 col-lg-4">
            <div class="sticky-top" style="top: 20px; z-index: 1;">
                
                <div class="bg-white border rounded-4 p-4 shadow-sm mb-3">
                    <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['title'] ?? '') ?></h4>
                    <div class="fw-bold display-6 mb-3" style="color: #FF7A3D;"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</div>
                    
                    <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                        <i class="bi bi-geo-alt-fill text-danger"></i><span>Khu vực: <?= htmlspecialchars($product['ward_name'] ?? 'Toàn quốc') ?></span>
                    </div>
                    
                    <div class="d-grid gap-3 mt-4">
                        <?php 
                        // Kiểm tra: Nếu Tồn kho > 0 VÀ Trạng thái không phải là 3 (Đã bán) thì mới cho mua
                        if(isset($product['stock_quantity']) && $product['stock_quantity'] > 0 && isset($product['status_id']) && $product['status_id'] != 3): 
                        ?>
                            <div class="row g-2">
                                <div class="col-4">
                                    <button type="button" onclick="actionChat(<?= $product['user_id'] ?? 0 ?>, <?= $product['id'] ?? 0 ?>)" class="btn btn-outline-dark w-100 py-2.5 rounded-3 fw-semibold small" style="border-color: #1F3C5A; color: #1F3C5A;"><i class="bi bi-chat-text d-block fs-5 mb-0.5"></i> Chat</button>
                                </div>
                                <div class="col-8">
                                    <button type="button" onclick="actionAddToCart(<?= $product['id'] ?? 0 ?>)" class="btn btn-outline-warning w-100 h-100 py-2.5 rounded-3 fw-bold" style="border-color: #FF7A3D; color: #FF7A3D;"><i class="bi bi-cart-plus fs-5 me-1"></i> Thêm vào giỏ</button>
                                </div>
                            </div>
                            
                            <button type="button" onclick="actionBuyNow(<?= $product['id'] ?? 0 ?>)" class="btn btn-lg text-white fw-bold py-3 rounded-3 shadow-sm btn-hover-zoom" style="background-color: #FF7A3D; border: none; font-size: 16px;">MUA NGAY (Giao dịch an toàn)</button>

                            <button type="button" class="btn btn-sm btn-light border w-100 py-2 rounded-3 text-secondary fw-semibold small" onclick="Swal.fire({icon: 'info', title: 'Thông báo', text: 'Tính năng Thương lượng giá đang được phát triển, bạn quay lại sau nhé!', confirmButtonColor: '#FF7A3D'})"><i class="bi bi-tags me-1 text-warning"></i> Bạn muốn trả giá? Yêu cầu thương lượng</button>
                        <?php else: ?>
                            <button class="btn btn-lg btn-secondary fw-bold py-3 rounded-3 shadow-sm" disabled><i class="bi bi-x-circle me-2"></i> Sản phẩm đã hết hàng</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= !empty($product['avatar_url']) ? htmlspecialchars($product['avatar_url']) : 'https://ui-avatars.com/api/?name=' . urlencode($product['username'] ?? 'User') . '&background=1F3C5A&color=fff' ?>" class="rounded-circle object-fit-cover border" width="55" height="55">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($product['full_name'] ?? '') ?></h6>
                            <small class="text-muted">@<?= htmlspecialchars($product['username'] ?? '') ?></small>
                        </div>
                    </div>
                    <a href="#" class="btn btn-sm btn-light border w-100 py-2 rounded-3 fw-semibold text-secondary small"><i class="bi bi-shop me-1"></i> Xem cửa hàng</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>

<script>
// 1. XỬ LÝ THÊM VÀO GIỎ HÀNG (AJAX - CÓ CẬP NHẬT HEADER DOM)
async function actionAddToCart(listingId) {
    try {
        let res = await fetch(`index.php?controller=cart&action=addAjax&id=${listingId}`, { method: 'POST' });
        let data = await res.json(); 
        
        if(data.status === 'success') {
            
            // =========================================================
            // BÍ KÍP DOM: ÉP HEADER NHẢY SỐ NGAY LẬP TỨC KHÔNG CẦN TẢI LẠI TRANG
            if(data.newCartCount !== undefined) {
                let cartIcon = document.querySelector('a[title="Giỏ hàng"]');
                if(cartIcon) {
                    let badge = cartIcon.querySelector('.badge');
                    if(badge) {
                        badge.innerText = data.newCartCount; // Đã có cục đỏ thì đổi số
                    } else {
                        // Chưa có cục đỏ (giỏ trống) thì nhét HTML vào
                        cartIcon.innerHTML += `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;">${data.newCartCount}</span>`;
                    }
                    
                    // Thêm hiệu ứng giật nảy nhẹ cho icon Giỏ hàng để khách chú ý (Tùy chọn cho xịn)
                    cartIcon.style.transform = 'scale(1.2)';
                    setTimeout(() => cartIcon.style.transform = 'scale(1)', 200);
                }
            }
            // =========================================================

            Swal.fire({
                icon: 'success', title: 'Đã thêm vào giỏ hàng!', text: 'Bạn có muốn chuyển đến giỏ hàng không?',
                showCancelButton: true, confirmButtonColor: '#FF7A3D', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đến giỏ hàng', cancelButtonText: 'Ở lại đây'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'index.php?controller=cart';
            });

        } else {
            // Nếu lỗi (vượt tồn kho) thì hiện thông báo cảnh báo màu vàng nhẹ nhàng
            Swal.fire({ icon: 'warning', title: 'Chú ý', text: data.msg });
        }
    } catch (e) {
        console.error("Lỗi giỏ hàng:", e);
        Swal.fire({ icon: 'error', title: 'Lỗi Backend', text: 'Chức năng giỏ hàng đang bảo trì!', confirmButtonColor: '#d33' });
    }
}
function actionBuyNow(listingId) {
    window.location.href = `index.php?controller=checkout&action=index&buy_now_id=${listingId}`;
}

function actionChat(sellerId, listingId) {
    let currentUserId = '<?= $_SESSION['user_id'] ?? 0 ?>';
    if(currentUserId == sellerId) {
        Swal.fire({ icon: 'warning', title: 'Ơ kìa...', text: 'Bạn không thể tự chat với chính mình được nha!' });
        return;
    }
    window.location.href = `index.php?controller=chat&action=room&with=${sellerId}&listing=${listingId}`;
}
</script>

<style>
.btn-hover-zoom { transition: transform 0.2s, background-color 0.2s; }
.btn-hover-zoom:hover { transform: scale(1.02); background-color: #e66932 !important; }
</style>