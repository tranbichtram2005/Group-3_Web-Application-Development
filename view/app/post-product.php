<?php
$pageTitle = isset($product) ? "Chỉnh sửa tin đăng - 2Life" : "Đăng tin bán - 2Life";
include 'view/partials/user-header.php';

// Cờ xác định Đang sửa (Edit) hay Đăng mới (Create)
$isEditMode = isset($product) && !empty($product);
$actionUrl = $isEditMode ? "index.php?controller=listing&action=update&id=" . $product['id'] : "index.php?controller=listing&action=create";
$btnSubmitText = $isEditMode ? "Cập nhật tin đăng" : "Đăng bán ngay";
?>

<main class="container post-main my-4">
    <nav class="breadcrumb-2life mb-3">
        <a href="index.php?controller=home">Trang chủ</a><span class="sep">/</span>
        <a href="index.php?controller=manage_listing&action=index">Kênh người bán</a><span class="sep">/</span>
        <span><?= $isEditMode ? 'Chỉnh sửa tin đăng' : 'Đăng tin bán sản phẩm' ?></span>
    </nav>

    <div class="text-center mb-4 mb-md-5">
        <h1 class="fw-bold fs-2"><?= $isEditMode ? 'Chỉnh sửa tin đăng' : 'Đăng tin bán sản phẩm' ?></h1>
        <p class="text-secondary mt-2">Vui lòng điền đầy đủ thông tin để người mua dễ dàng tìm thấy sản phẩm của bạn.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <form id="formPostProduct" action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data">
                
                <div class="card-white p-3 p-md-4 mb-4 shadow-sm border-0 rounded-4">
                    <h2 class="post-section-title h5 fw-bold mb-4">
                        <span class="badge bg-primary rounded-circle me-2">1</span> Hình ảnh &amp; Video
                    </h2>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm <?= !$isEditMode ? '<span class="text-danger">*</span>' : '' ?></label>
                            <input type="file" name="images[]" id="imageUpload" multiple accept="image/jpeg, image/png, image/webp" style="display: none;" <?= !$isEditMode ? 'required' : '' ?>>
                            
                            <div class="position-relative border border-dashed rounded bg-light overflow-hidden" style="height: 250px;">
                                
                                <div id="uploadBoxUI" class="upload-box d-flex flex-column justify-content-center align-items-center p-4 text-center h-100 w-100" 
                                     onclick="document.getElementById('imageUpload').click();" 
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
                                        <button type="button" class="btn btn-sm btn-dark text-white shadow rounded-pill px-3 opacity-90" onclick="document.getElementById('imageUpload').click();">
                                            <i class="bi bi-pencil-square me-1"></i> Thay đổi ảnh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Video <span class="badge bg-light text-secondary border fw-normal ms-1">Tùy chọn</span></label>
                            <input type="file" name="video" id="videoUpload" accept="video/mp4" style="display: none;">
                            <div class="upload-box border border-dashed rounded bg-light d-flex flex-column justify-content-center align-items-center p-4 text-center" onclick="document.getElementById('videoUpload').click();" style="cursor: pointer; min-height: 250px;">
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
                            <select name="province_id" id="provinceSelect" class="form-select" required>
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
                            <select name="district_id" id="districtSelect" class="form-select" required <?= ($isEditMode && !empty($districts)) ? '' : 'disabled' ?>>
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
<script>
    const imageUpload = document.getElementById('imageUpload');
    const uploadBoxUI = document.getElementById('uploadBoxUI');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const carouselInnerImages = document.getElementById('carouselInnerImages');

    // 1. KHI NGƯỜI DÙNG THAY ĐỔI / CHỌN FILE MỚI
    imageUpload.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files.length > 0) {
            uploadBoxUI.setAttribute('style', 'display: none !important;'); // Ẩn hoàn toàn ô chọn ảnh
            imagePreviewContainer.style.display = 'block'; // Hiện slideshow thay thế vào vị trí đó
            carouselInnerImages.innerHTML = ''; // Làm sạch các ảnh cũ

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const activeClass = index === 0 ? 'active' : ''; 
                        const imgHtml = `
                            <div class="carousel-item ${activeClass} h-100 w-100">
                                <img src="${event.target.result}" class="d-block w-100 h-100" style="object-fit: contain;" alt="Preview Image ${index + 1}">
                            </div>
                        `;
                        carouselInnerImages.insertAdjacentHTML('beforeend', imgHtml);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    document.getElementById('videoUpload').addEventListener('change', function (e) {
        document.getElementById('videoText').innerHTML = e.target.files.length > 0 ? `Đã chọn <strong class="text-primary">1 video</strong>` : 'Kéo thả hoặc <strong>Chọn video</strong>';
    });

    // 2. XỬ LÝ DROPDOWN TỈNH/QUẬN/PHƯỜNG
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');

    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
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
    });

    districtSelect.addEventListener('change', function() {
        const districtId = this.value;
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
    });

    // 3. XỬ LÝ SUBMIT AJAX
    document.getElementById('formPostProduct').addEventListener('submit', function (e) {
        e.preventDefault(); 
        let submitBtn = this.querySelector('button[type="submit"]');
        let originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';
        submitBtn.disabled = true;

        let formData = new FormData(this);

        fetch(this.action, { method: 'POST', body: formData })
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
    });
</script>

<?php include 'view/partials/user-footer.php'; ?>