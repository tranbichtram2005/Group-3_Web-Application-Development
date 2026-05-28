<?php include 'view/partials/admin-header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="index.php?controller=approvelisting" class="text-decoration-none">Phê duyệt tin đăng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>
            <h2 class="h4 mb-0 fw-bold">Chi tiết: <?= htmlspecialchars($listing['title']) ?></h2>
        </div>
        <div>
            <a href="index.php?controller=approvelisting" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-box-seam me-2 text-primary"></i>Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="fw-bold mb-3 text-secondary">Hình ảnh đính kèm</h6>
                        <?php if (!empty($images)): ?>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($images as $img): ?>
                                    <div class="border rounded p-1 position-relative" style="width: 160px; height: 160px;">
                                        <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Product Image" class="w-100 h-100 object-fit-cover rounded">
                                        <?php if ($img['is_primary']): ?>
                                            <span class="position-absolute top-0 start-0 badge bg-danger m-2">Ảnh bìa</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-4 bg-light text-center rounded text-muted">
                                <i class="bi bi-image text-secondary fs-1 d-block mb-2"></i>
                                Tin đăng này không có hình ảnh.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Tên sản phẩm:</div>
                        <div class="col-md-9 fw-bold fs-5 text-dark"><?= htmlspecialchars($listing['title']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Danh mục:</div>
                        <div class="col-md-9"><span class="badge bg-light text-dark border"><?= htmlspecialchars($listing['category_name']) ?></span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Tình trạng:</div>
                        <div class="col-md-9"><?= htmlspecialchars($listing['condition_name']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Giá bán:</div>
                        <div class="col-md-9 text-danger fw-bold fs-5">
                            <?= number_format($listing['price'], 0, ',', '.') ?> đ
                            <?php if ($listing['is_negotiable']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-2" style="font-size: 12px; vertical-align: middle;">Có thương lượng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Khu vực:</div>
                        <div class="col-md-9"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($listing['ward_name']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 text-muted">Mô tả chi tiết:</div>
                        <div class="col-md-9">
                            <div class="p-3 bg-light rounded text-dark" style="white-space: pre-wrap; font-size: 14px;"><?= htmlspecialchars($listing['description']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Thông tin người bán</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3 fw-bold shadow-sm" style="width: 50px; height: 50px; font-size: 22px;">
                            <?= strtoupper(substr($listing['seller_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold fs-5"><?= htmlspecialchars($listing['seller_name']) ?></h6>
                            <small class="text-muted">User ID: #<?= $listing['user_id'] ?></small>
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded">
                        <div class="mb-2"><i class="bi bi-telephone-fill text-secondary me-2"></i> <?= htmlspecialchars($listing['phone_number'] ?? 'Chưa cập nhật') ?></div>
                        <div><i class="bi bi-envelope-fill text-secondary me-2"></i> <?= htmlspecialchars($listing['email'] ?? 'Chưa cập nhật') ?></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm border-top border-primary border-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Quyết định kiểm duyệt</h5>

                    <div class="mb-4" id="status-badge-container">
                        Trạng thái hiện tại:
                        <?php if ($listing['status_id'] == 1): ?>
                            <span class="badge bg-warning text-dark fs-6 ms-2">Chờ duyệt</span>
                        <?php elseif ($listing['status_id'] == 2): ?>
                            <span class="badge bg-success fs-6 ms-2">Đang hiển thị</span>
                        <?php elseif ($listing['status_id'] == 3): ?>
                            <span class="badge bg-danger fs-6 ms-2">Đã từ chối</span>
                        <?php else: ?>
                            <span class="badge bg-secondary fs-6 ms-2">Đã ẩn/Gỡ</span>
                        <?php endif; ?>
                    </div>

                    <div id="action-buttons-container">
                        <?php if ($listing['status_id'] == 1): ?>
                            <div class="d-flex flex-column gap-2">
                                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=approve&id=<?= $listing['id'] ?>" class="btn btn-success fw-bold py-2 btn-action" data-type="approve" data-text="Bạn chắc chắn muốn duyệt tin đăng này lên sàn?">
                                    <i class="bi bi-check-circle-fill me-1"></i> Phê duyệt hiển thị
                                </button>
                                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=reject&id=<?= $listing['id'] ?>" class="btn btn-danger fw-bold py-2 btn-action" data-type="reject" data-text="Bạn chắc chắn muốn từ chối tin đăng này?">
                                    <i class="bi bi-x-circle-fill me-1"></i> Từ chối tin đăng
                                </button>
                            </div>
                        <?php elseif ($listing['status_id'] == 2): ?>
                            <div class="d-flex flex-column gap-2">
                                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=hide&id=<?= $listing['id'] ?>" class="btn btn-warning text-dark fw-bold py-2 btn-action" data-type="hide" data-text="Gỡ tin đăng này khỏi hệ thống ngay lập tức?">
                                    <i class="bi bi-eye-slash-fill me-1"></i> Buộc gỡ / Ẩn tin
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary mb-0 text-center">
                                Tin đăng này đã được xử lý xong.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Lắng nghe sự kiện click trên toàn bộ body
                    document.body.addEventListener('click', function(e) {
                        // Tìm nút có class .btn-action mà người dùng vừa click
                        const btn = e.target.closest('.btn-action');
                        if (!btn) return;

                        e.preventDefault(); // Chặn hành động chuyển trang mặc định

                        // KIỂM TRA LỖI 1: Thư viện SweetAlert2 chưa load được
                        if (typeof Swal === 'undefined') {
                            alert('Lỗi: Thư viện SweetAlert2 chưa được tải về! Vui lòng kiểm tra lại kết nối mạng hoặc thẻ CDN.');
                            return;
                        }

                        const url = btn.getAttribute('data-href');
                        const confirmText = btn.getAttribute('data-text');
                        const type = btn.getAttribute('data-type');

                        let confirmButtonColor = '#198754';
                        if (type === 'reject') confirmButtonColor = '#dc3545';
                        if (type === 'hide') confirmButtonColor = '#ffc107';

                        // Hiển thị Pop-up
                        Swal.fire({
                            title: 'Xác nhận hành động?',
                            text: confirmText,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: confirmButtonColor,
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Hủy bỏ'
                        }).then((result) => {
                            if (result.isConfirmed) {

                                // Hiển thị loading trong lúc gọi Ajax
                                Swal.fire({
                                    title: 'Đang xử lý...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Gọi Ajax
                                fetch(url)
                                    .then(async response => {
                                        // Lấy text raw ra kiểm tra trước (tránh lỗi PHP in ra cảnh báo làm hỏng chuỗi JSON)
                                        const rawText = await response.text();
                                        try {
                                            return JSON.parse(rawText);
                                        } catch (err) {
                                            console.error("Lỗi định dạng JSON trả về:", rawText);
                                            throw new Error("Dữ liệu trả về từ Controller không hợp lệ (không phải JSON). Hãy ấn F12 xem Console để biết chi tiết.");
                                        }
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Thành công!',
                                                text: data.message,
                                                icon: 'success',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });

                                            const listingId = url.split('&id=')[1];
                                            updateApprovalUI(data.status_id, listingId);
                                        } else {
                                            Swal.fire('Thất bại', data.message || 'Lỗi xử lý.', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error FETCH:', error);
                                        Swal.fire('Lỗi hệ thống', error.message, 'error');
                                    });
                            }
                        });
                    });
                });

                function updateApprovalUI(statusId, listingId) {
                    const badgeContainer = document.getElementById('status-badge-container');
                    const buttonsContainer = document.getElementById('action-buttons-container');

                    let badgeHTML = 'Trạng thái hiện tại: ';
                    if (statusId == 1) badgeHTML += '<span class="badge bg-warning text-dark fs-6 ms-2">Chờ duyệt</span>';
                    else if (statusId == 2) badgeHTML += '<span class="badge bg-success fs-6 ms-2">Đang hiển thị</span>';
                    else if (statusId == 3) badgeHTML += '<span class="badge bg-danger fs-6 ms-2">Đã từ chối</span>';
                    else badgeHTML += '<span class="badge bg-secondary fs-6 ms-2">Đã ẩn/Gỡ</span>';

                    badgeContainer.innerHTML = badgeHTML;

                    if (statusId == 1) {
                        buttonsContainer.innerHTML = `
            <div class="d-flex flex-column gap-2">
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=approve&id=${listingId}" class="btn btn-success fw-bold py-2 btn-action" data-type="approve" data-text="Bạn chắc chắn muốn duyệt tin đăng này lên sàn?">
                    <i class="bi bi-check-circle-fill me-1"></i> Phê duyệt hiển thị
                </button>
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=reject&id=${listingId}" class="btn btn-danger fw-bold py-2 btn-action" data-type="reject" data-text="Bạn chắc chắn muốn từ chối tin đăng này?">
                    <i class="bi bi-x-circle-fill me-1"></i> Từ chối tin đăng
                </button>
            </div>
        `;
                    } else if (statusId == 2) {
                        buttonsContainer.innerHTML = `
            <div class="d-flex flex-column gap-2">
                <button data-href="index.php?controller=approvelisting&action=changeStatus&type=hide&id=${listingId}" class="btn btn-warning text-dark fw-bold py-2 btn-action" data-type="hide" data-text="Gỡ tin đăng này khỏi hệ thống ngay lập tức?">
                    <i class="bi bi-eye-slash-fill me-1"></i> Buộc gỡ / Ẩn tin
                </button>
            </div>
        `;
                    } else {
                        buttonsContainer.innerHTML = `
            <div class="alert alert-secondary mb-0 text-center">
                Tin đăng này đã được xử lý xong.
            </div>
        `;
                    }
                }
            </script>

        </div>
    </div>
</div>

<?php include 'view/partials/admin-footer.php'; ?>