<?php
$pageTitle = isset($product) ? "Chỉnh sửa tin đăng - 2Life" : "Đăng tin bán - 2Life";
include 'view/partials/user-header.php';

// Cờ xác định Đang sửa (Edit) hay Đăng mới (Create)
$isEditMode = isset($product) && !empty($product);
$actionUrl = $isEditMode ? "index.php?controller=listing&action=update&id=" . $product['id'] : "index.php?controller=listing&action=create";
$btnSubmitText = $isEditMode ? "Cập nhật tin đăng" : "Đăng bán ngay";
?>

<main class="container post-main my-4">
    
    <div class="text-center mb-4 mb-md-5">
        <h1 class="fw-bold fs-2"><?= $isEditMode ? 'Chỉnh sửa tin đăng' : 'Đăng tin bán sản phẩm' ?></h1>
        <p class="text-secondary mt-2">Vui lòng điền đầy đủ thông tin để người mua dễ dàng tìm thấy sản phẩm của bạn.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <form id="formPostProduct" action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data" onsubmit="postProduct_handleSubmit(event, this)">
                
                <div class="card-white p-3 p-md-4 mb-4 shadow-sm border-0 rounded-4">
                    <h2 class="post-section-title h5 fw-bold mb-4">
                        <span class="badge bg-primary rounded-circle me-2">1</span> Hình ảnh &amp; Video
                    </h2>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm <?= !$isEditMode ? '<span class="text-danger">*</span>' : '' ?></label>
                            <input type="file" name="images[]" id="imageUpload" multiple accept="image/jpeg, image/png, image/webp" style="display: none;" <?= !$isEditMode ? 'required' : '' ?> onchange="postProduct_handleImageChange(event)">
                            
                            <div class="position-relative border border-dashed rounded bg-light overflow-hidden" style="height: 250px;">
                                <div id="uploadBoxUI" class="upload-box d-flex flex-column justify-content-center align-items-center p-4 text-center h-100 w-100" 
                                     onclick="postProduct_triggerImageSelect();" 
                                     style="cursor: pointer; transition: 0.3s; <?= ($isEditMode && !empty($images)) ? 'display: none !important;' : 'display: flex;' ?>">
                                    <i class="bi bi-images fs-1 text-secondary mb-2"></i>
                                    <p class="mb-1">Kéo thả hoặc <strong>Chọn ảnh</strong></p>
                                    <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc</small>
                                </div>

                                <div id="imagePreviewContainer" class="h-100 w-100 position-relative" style="<?= ($isEditMode && !empty($images)) ? 'display: block;' : 'display: none;' ?>">
                                    <div id="productImageCarousel" class="carousel slide h-100 w-100" data-bs-ride="carousel">
                                        <div class="carousel-inner h-100 w-100" id="carouselInnerImages" style="background: #f8f9fa;">
                                            <?php if ($isEditMode && !empty($images)): ?>
                                                <?php foreach ($images as $index => $img): ?>
                                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100 w-100">
                                                        <img src="<?= htmlspecialchars($img['image_url']) ?>" class="d-block w-100 h-100" style="object-fit: contain;" alt="Existing Image">
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" style="background-color: rgba(0,0,0,0.6); border-radius: 50%; padding: 12px;" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" style="background-color: rgba(0,0,0,0.6); border-radius: 50%; padding: 12px;" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>

                                    <div class="position-absolute bottom-0 end-0 m-3 z-3">
                                        <button type="button" class="btn btn-sm btn-dark text-white shadow rounded-pill px-3 opacity-90" onclick="postProduct_triggerImageSelect();">
                                            <i class="bi bi-pencil-square me-1"></i> Thay đổi ảnh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Video <span class="badge bg-light text-secondary border fw-normal ms-1">Tùy chọn</span></label>
                            <input type="file" name="video" id="videoUpload" accept="video/mp4" style="display: none;" onchange="postProduct_handleVideoChange(event)">
                            <div class="upload-box border border-dashed rounded bg-light d-flex flex-column justify-content-center align-items-center p-4 text-center" onclick="postProduct_triggerVideoSelect();" style="cursor: pointer; min-height: 250px;">
                                <i class="bi bi-camera-video fs-1 text-secondary mb-2"></i>
                                <p class="mb-1" id="videoText">Kéo thả hoặc <strong>Chọn video</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-white p-3 p-md-4 mb-4 shadow-sm border-0 rounded-4">
                    <h2 class="post-section-title h5 fw-bold mb-4">
                        <span class="badge bg-primary rounded-circle me-2">2</span> Thông tin chi tiết
                    </h2>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="VD: Áo khoác da bò Vintage size L" value="<?= $isEditMode ? htmlspecialchars($product['title']) : '' ?>" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php if(!empty($categories)): ?>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($isEditMode && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Tình trạng <span class="text-danger">*</span></label>
                            <select name="condition_id" class="form-select" required>
                                <option value="">-- Chọn tình trạng --</option> 
                                <?php if(!empty($conditions)): ?>
                                    <?php foreach($conditions as $cond): ?>
                                        <option value="<?= $cond['id'] ?>" <?= ($isEditMode && $product['condition_id'] == $cond['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cond['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="VD: 550000" min="0" value="<?= $isEditMode ? $product['price'] : '' ?>" required>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_negotiable" value="1" id="isNegotiable" <?= ($isEditMode && isset($product['is_negotiable']) && $product['is_negotiable'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label text-secondary small" for="isNegotiable">Cho phép thương lượng</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control" min="1" value="<?= $isEditMode ? $product['stock_quantity'] : '1' ?>" required>
                        </div>
                    </div>

                   <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select name="province_id" id="provinceSelect" class="form-select" required onchange="postProduct_loadDistricts(this.value)">
                                <option value="">-- Chọn Tỉnh/Thành --</option>
                                <?php if(!empty($provinces)): ?>
                                    <?php foreach($provinces as $prov): ?>
                                        <option value="<?= $prov['id'] ?>" <?= ($isEditMode && isset($product['province_id']) && $product['province_id'] == $prov['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prov['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Quận/Huyện <span class="text-danger">*</span></label>
                            <select name="district_id" id="districtSelect" class="form-select" required <?= ($isEditMode && !empty($districts)) ? '' : 'disabled' ?> onchange="postProduct_loadWards(this.value)">
                                <option value="">-- Chọn Quận/Huyện --</option>
                                <?php if($isEditMode && !empty($districts)): ?>
                                    <?php foreach($districts as $dist): ?>
                                        <option value="<?= $dist['id'] ?>" <?= ($product['district_id'] == $dist['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dist['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Phường/Xã <span class="text-danger">*</span></label>
                            <select name="ward_id" id="wardSelect" class="form-select" required <?= ($isEditMode && !empty($wards)) ? '' : 'disabled' ?>>
                                <option value="">-- Chọn Phường/Xã --</option>
                                <?php if($isEditMode && !empty($wards)): ?>
                                    <?php foreach($wards as $w): ?>
                                        <option value="<?= $w['id'] ?>" <?= ($product['ward_id'] == $w['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($w['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Mô tả ưu điểm, khuyết điểm, thời gian đã sử dụng, lý do bán..." required><?= $isEditMode ? htmlspecialchars($product['description']) : '' ?></textarea>
                    </div>
                </div>

                <div class="card-white p-3 shadow-sm border-0 rounded-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border fw-bold" onclick="history.back()"><i class="bi bi-arrow-left me-1"></i>Trở về</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2"><i class="bi bi-rocket-takeoff me-1"></i><?= $btnSubmitText ?></button>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <i class="bi bi-headset display-4 text-primary mb-2"></i>
                <h5 class="fw-bold mt-2">Cần hỗ trợ?</h5>
                <p class="small text-secondary mb-3">Nếu bạn gặp khó khăn trong quá trình đăng tin, đội ngũ Admin luôn sẵn sàng giúp đỡ.</p>
                <button type="button" class="btn btn-outline-primary w-100"><i class="bi bi-chat-dots me-1"></i>Liên hệ Admin</button>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include 'view/partials/user-footer.php'; ?>