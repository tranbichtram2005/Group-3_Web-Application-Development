<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng tin bán - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">
</head>

<body>

    <header class="navbar-2life">
        <div class="container-fluid px-3 px-md-4">
            <div class="row align-items-center g-2">
                <div class="col-6 col-md-2">
                    <a href="../app/home.php" class="logo">2Life</a>
                </div>

                <div class="col-md-5 d-none d-md-block">
                    <div class="d-flex align-items-center"
                        style="background:#fff;border-radius:25px;padding:4px 4px 4px 16px;border:1px solid var(--border-color);">
                        <input type="text" placeholder="Tìm kiếm đồ cũ giá hời..."
                            style="flex:1;border:none;outline:none;font-size:14px;background:transparent;color:var(--text-primary);">
                        <button class="btn-2life-primary"
                            style="border-radius:20px;padding:7px 18px;white-space:nowrap;flex-shrink:0">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-6 col-md-5 d-flex justify-content-end align-items-center gap-2 gap-md-3">

                    <a href="#" class="nav-link-text position-relative" title="Giỏ hàng">
                        <i class="bi bi-cart3" style="font-size:18px"></i>
                        <span class="nav-badge">3</span>
                        <span class="d-none d-lg-inline ms-1">Giỏ hàng</span>
                    </a>

                    <div class="nav-dropdown d-none d-sm-block">
                        <a href="#" class="nav-link-text" title="Quản lý">
                            <i class="bi bi-grid-3x3-gap" style="font-size:16px"></i>
                            <span class="d-none d-lg-inline">Quản lý</span>
                            <i class="bi bi-chevron-down" style="font-size:10px;opacity:.7"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                        </div>
                    </div>

                    <a href="#" class="nav-link-text position-relative d-none d-sm-flex" title="Thông báo">
                        <i class="bi bi-bell" style="font-size:17px"></i>
                        <span class="nav-badge">5</span>
                        <span class="d-none d-lg-inline ms-1">Thông báo</span>
                    </a>

                    <div class="nav-dropdown d-none d-md-block">
                        <a href="#" class="nav-link-text">
                            <i class="bi bi-headset" style="font-size:16px"></i>
                            <span class="d-none d-lg-inline">Hỗ trợ</span>
                            <i class="bi bi-chevron-down" style="font-size:10px;opacity:.7"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                        </div>
                    </div>

                    <a href="#" class="nav-link-text">
                        <i class="bi bi-person-circle" style="font-size:18px"></i>
                        <span class="d-none d-lg-inline">Tài khoản</span>
                    </a>

                    <button class="btn-2life-primary d-md-none" style="padding:8px 12px">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="col-12 d-md-none">
                    <input type="text" class="search-input" placeholder="Tìm kiếm đồ cũ giá hời...">
                </div>

            </div>
        </div>
    </header>

    <main class="container post-main">

        <nav class="breadcrumb-2life">
            <a href="#">Trang chủ</a><span class="sep">/</span>
            <a href="#">Tài khoản</a><span class="sep">/</span>
            <span>Đăng tin bán sản phẩm</span>
        </nav>

        <div class="text-center mb-4 mb-md-5">
            <h1 class="fw-bold fs-2">Đăng tin bán sản phẩm</h1>
            <p class="text-secondary mt-2">Vui lòng điền đầy đủ thông tin để người mua dễ dàng tìm thấy sản phẩm của
                bạn.</p>
        </div>

        <div class="row g-4">

            <div class="col-12 col-lg-8">
                <form action="index.php?controller=listing&action=create" method="POST" enctype="multipart/form-data">

                    <div class="card-white p-3 p-md-4 mb-4">
                        <h2 class="post-section-title">
                            <span class="post-section-num">1</span> Hình ảnh &amp; Video
                        </h2>

                        <div class="row g-3 align-items-stretch">
                            <div class="col-12 col-md-6 d-flex flex-column">
                                <label class="form-label fw-bold">
                                    Hình ảnh sản phẩm <span class="text-danger">*</span>
                                </label>
                                <p class="small text-secondary mb-2">Tối thiểu 1 ảnh, tối đa 5 ảnh. Ảnh đầu tiên làm ảnh
                                    đại diện.</p>

                                <input type="file" name="images[]" id="imageUpload" multiple
                                    accept="image/jpeg, image/png" style="display: none;" required>

                                <div class="upload-box flex-grow-1 d-flex flex-column justify-content-center align-items-center p-4"
                                    onclick="document.getElementById('imageUpload').click();" style="cursor: pointer;">
                                    <i class="bi bi-camera upload-icon mb-2"></i>
                                    <p class="mb-1" id="imageText">Kéo thả hoặc <strong>Chọn ảnh</strong></p>
                                    <p class="small text-secondary mb-0">JPG, PNG · Tối đa 5MB</p>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 d-flex flex-column">
                                <label class="form-label fw-bold">
                                    Video sản phẩm
                                    <span class="badge bg-light text-secondary border fw-normal ms-1"
                                        style="font-size:11px">Không bắt buộc</span>
                                </label>
                                <p class="small text-secondary mb-2">Tối đa 1 video · Dưới 30 giây · &lt; 20MB</p>

                                <input type="file" name="video" id="videoUpload" accept="video/mp4"
                                    style="display: none;">

                                <div class="upload-box video-box flex-grow-1 d-flex flex-column justify-content-center align-items-center p-4"
                                    onclick="document.getElementById('videoUpload').click();" style="cursor: pointer;">
                                    <i class="bi bi-camera-video upload-icon mb-2"></i>
                                    <p class="mb-1" id="videoText">Kéo thả hoặc <strong>Chọn video</strong></p>
                                    <p class="small text-secondary mb-0">Định dạng MP4</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-white p-3 p-md-4 mb-4">
                        <h2 class="post-section-title">
                            <span class="post-section-num">2</span> Thông tin chi tiết
                        </h2>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                placeholder="VD: Áo khoác da bò Vintage size L" required>
                            <p class="small text-secondary mt-1 mb-0">Nên chứa từ khóa, thương hiệu, màu sắc, tình
                                trạng.</p>
                        </div>
                        <div class="row g-3 mb-4 align-items-stretch">
                            <div class="col-12 col-md-6 d-flex flex-column">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select flex-grow-1" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php if(!empty($categories)): ?>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 d-flex flex-column">
                                <label class="form-label fw-bold">Tình trạng <span class="text-danger">*</span></label>
                                <select name="condition_id" class="form-select flex-grow-1" required>
                                    <option value="">-- Chọn tình trạng --</option> 
                                    <?php if(!empty($conditions)): ?>
                                    <?php foreach($conditions as $cond): ?>
                                    <option value="<?= $cond['id'] ?>">
                                        <?= htmlspecialchars($cond['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Giá bán (VNĐ) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" placeholder="VD: 550000"
                                    required>

                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_negotiable" value="1"
                                        id="isNegotiable">
                                    <label class="form-check-label text-secondary small" for="isNegotiable">
                                        Cho phép người mua thương lượng giá
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Khu vực đăng bán (ID Phường/Xã)<span
                                        class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <input type="number" name="ward_id" class="form-control"
                                        placeholder="VD: 1 (Phường Bến Nghé)" required>
                                    <button type="button" class="btn-location text-nowrap fw-bold">
                                        <i class="bi bi-geo-alt-fill me-1"></i>Lấy vị trí
                                    </button>
                                </div>
                                <p class="small text-secondary mt-1 mb-0">Nhập mã khu vực để định vị người mua ở gần.
                                </p>
                            </div>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Mô tả chi tiết <span
                                        class="text-danger">*</span></label>
                                <button type="button" class="btn-ai text-white fw-bold">
                                    <i class="bi bi-stars me-1"></i>Gợi ý AI
                                </button>
                            </div>
                            <textarea name="description" class="form-control" rows="6"
                                placeholder="Mô tả ưu điểm, khuyết điểm, thời gian đã sử dụng, lý do bán..."
                                required></textarea>
                            <p class="small text-secondary mt-1 mb-0">Mô tả càng chi tiết, trung thực sẽ giúp bạn nhanh
                                chóng chốt đơn.</p>
                        </div>
                    </div>

                    <div
                        class="card-white p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <button type="button" class="btn-cancel fw-bold" onclick="history.back()">
                            <i class="bi bi-arrow-left me-1"></i>Trở về
                        </button>

                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <button type="button" class="btn-2life-outline">
                                <i class="bi bi-eye me-1"></i>Xem trước
                            </button>
                            <button type="button" class="btn-draft">
                                <i class="bi bi-floppy me-1"></i>Lưu nháp
                            </button>
                            <button type="submit" class="btn-2life-primary">
                                <i class="bi bi-rocket-takeoff me-1"></i>Đăng bán ngay
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-12 col-lg-4">
                <div class="sidebar-sticky">

                    <div class="card-white p-4 mb-3">
                        <div class="section-label">💡 Mẹo đăng tin hiệu quả</div>
                        <ul class="list-unstyled mb-0 guide-list">
                            <li class="mb-3 pb-3 border-bottom border-dashed">
                                <a href="#" class="text-decoration-none guide-link">
                                    <i class="bi bi-camera me-1"></i>Cách chụp ảnh sản phẩm thu hút hàng ngàn lượt xem
                                </a>
                            </li>
                            <li class="mb-3 pb-3 border-bottom border-dashed">
                                <a href="#" class="text-decoration-none guide-link">
                                    <i class="bi bi-journal-text me-1"></i>Hướng dẫn đăng tin bán hàng đơn giản cho
                                    người mới
                                </a>
                            </li>
                            <li class="mb-3 pb-3 border-bottom border-dashed">
                                <a href="#" class="text-decoration-none guide-link">
                                    <i class="bi bi-tags me-1"></i>Mẹo định giá đồ cũ hợp lý, dễ bán
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-decoration-none guide-link">
                                    <i class="bi bi-patch-question me-1"></i>Đăng bài bị lỗi hiển thị thì phải làm sao?
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-white p-4 text-center">
                        <i class="bi bi-headset" style="font-size:2.5rem;color:var(--btn-secondary)"></i>
                        <h3 class="h6 fw-bold mt-2 mb-2">Cần hỗ trợ?</h3>
                        <p class="small text-secondary mb-3">Nếu bạn gặp khó khăn trong quá trình đăng tin, đội ngũ
                            Admin luôn sẵn sàng giúp đỡ.</p>
                        <button type="button" class="btn-2life-secondary w-100">
                            <i class="bi bi-chat-dots me-1"></i>Liên hệ Admin ngay
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <footer class="pt-5 pb-3">
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('imageUpload').addEventListener('change', function (e) {
            var count = e.target.files.length;
            if (count > 0) {
                document.getElementById('imageText').innerHTML = `Đã chọn <strong>${count} ảnh</strong>`;
            } else {
                document.getElementById('imageText').innerHTML = 'Kéo thả hoặc <strong>Chọn ảnh</strong>';
            }
        });

        document.getElementById('videoUpload').addEventListener('change', function (e) {
            if (e.target.files.length > 0) {
                document.getElementById('videoText').innerHTML = `Đã chọn <strong>1 video</strong>`;
            } else {
                document.getElementById('videoText').innerHTML = 'Kéo thả hoặc <strong>Chọn video</strong>';
            }
        });
    </script>
    

</body>

</html>