<?php require_once __DIR__ . '/partials/user-header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Chống giật Footer khi bảng có ít dữ liệu */
    .table-container-fixed {
        min-height: 480px; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>

<main class="container post-main py-4">
    <nav class="breadcrumb-2life mb-3">
        <a href="index.php">Trang chủ</a><span class="sep">/</span>
        <a href="#">Kênh người bán</a><span class="sep">/</span>
        <span>Quản lý tin đăng</span>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold fs-3 mb-1">Danh sách tin đăng</h1>
            <p class="text-secondary small mb-0">Xem lịch sử, trạng thái kiểm duyệt và quản lý các mặt hàng đồ cũ đang bán.</p>
        </div>
        <a href="index.php?controller=listing&action=create" class="btn btn-primary btn-2life-primary"><i class="bi bi-plus-lg"></i> Đăng tin mới</a>
    </div>

    <div class="card-white p-3 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?controller=manage_listing&action=index&tab=all" class="btn <?= $currentTab === 'all' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">Tất cả (<?= $counts['all_count'] ?? 0 ?>)</a>
                    <a href="index.php?controller=manage_listing&action=index&tab=active" class="btn <?= $currentTab === 'active' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">Đang bán (<?= $counts['active_count'] ?? 0 ?>)</a>
                    <a href="index.php?controller=manage_listing&action=index&tab=pending" class="btn <?= $currentTab === 'pending' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">Chờ duyệt (<?= $counts['pending_count'] ?? 0 ?>)</a>
                    <a href="index.php?controller=manage_listing&action=index&tab=hidden" class="btn <?= $currentTab === 'hidden' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">Đã ẩn/đóng (<?= $counts['hidden_count'] ?? 0 ?>)</a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <form action="index.php" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="controller" value="manage_listing">
                    <input type="hidden" name="action" value="index">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($currentTab) ?>">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($searchKeyword) ?>">
                    <button type="submit" class="btn btn-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-white p-0 overflow-hidden shadow-sm table-container-fixed">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" style="width: 40%;">Sản phẩm</th>
                        <th style="width: 15%;">Giá bán</th>
                        <th style="width: 15%;">Ngày đăng</th>
                        <th style="width: 15%;">Trạng thái</th>
                        <th class="pe-4 text-end" style="width: 15%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listings)): ?>
                        <?php foreach ($listings as $item): ?>
                            <tr id="row-<?= $item['id'] ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3 py-2">
                                        <img src="<?= !empty($item['primary_image']) ? htmlspecialchars($item['primary_image']) : 'https://placehold.co/60x60?text=No+Image' ?>" class="rounded border object-cover" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($item['title']) ?></h6>
                                            <span class="badge bg-light text-secondary border fw-normal mb-1" style="font-size: 11px;"><?= htmlspecialchars($item['category_name']) ?></span>
                                            
                                            <div class="small">
                                                <?php if (isset($item['stock_quantity']) && $item['stock_quantity'] > 0): ?>
                                                    <span class="text-muted">Kho: <strong class="text-dark"><?= $item['stock_quantity'] ?></strong></span>
                                                <?php else: ?>
                                                    <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> Đã bán hết</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-danger"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td class="text-secondary small"><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                
                                <td>
                                    <?php if ($item['status_id'] == 4): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2"><i class="bi bi-eye-slash-fill"></i> Đã ẩn/Đóng</span>
                                    <?php elseif ($item['status_id'] == 1): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2"><i class="bi bi-hourglass-split"></i> Chờ duyệt</span>
                                    <?php else: ?>
                                        <?php if (isset($item['stock_quantity']) && $item['stock_quantity'] > 0): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2"><i class="bi bi-check-circle-fill"></i> Đang bán</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2"><i class="bi bi-box-seam"></i> Hết hàng</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 btn-view-modal" data-id="<?= $item['id'] ?>" data-status="<?= $item['status_id'] ?>">
                                        <i class="bi bi-eye"></i> 
                                    </button>
                                    
                                    <?php if ($item['status_id'] != 4 && $item['status_id'] != 1): ?>
                                        <button data-href="index.php?controller=manage_listing&action=changeStatus&type=hide&id=<?= $item['id'] ?>" data-id="<?= $item['id'] ?>" class="btn btn-sm btn-outline-secondary btn-hide-listing">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-secondary">Không tìm thấy tin đăng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($totalPages) && $totalPages > 1): ?>
        <div class="d-flex justify-content-center py-3 border-top">
            <ul class="pagination pagination-sm mb-0">
                <?php 
                    $params = $_GET; 
                    unset($params['page']); 
                    $qs = http_build_query($params); 
                ?>
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?<?= $qs ?>&page=<?= $page - 1 ?>">Trước</a>
                </li>
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?<?= $qs ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?<?= $qs ?>&page=<?= $page + 1 ?>">Sau</a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</main>

<div class="modal fade" id="listingDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Chi tiết tin đăng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-start" id="modal-content-body">
         <div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" id="btn-delete-listing"><i class="bi bi-trash"></i> Xóa tin</button>
        <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <a href="#" class="btn btn-primary" id="btn-edit-listing"><i class="bi bi-pencil"></i> Chỉnh sửa</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailModal = new bootstrap.Modal(document.getElementById('listingDetailModal'));
    let currentListingId = null;

    // 1. Khi bấm Xem chi tiết
    document.querySelectorAll('.btn-view-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            currentListingId = this.getAttribute('data-id');
            let statusId = this.getAttribute('data-status');
            
            // Xử lý nút Edit: Chỉ active nếu status_id == 1 (Chờ duyệt)
            let editBtn = document.getElementById('btn-edit-listing');
            if(statusId === '1') {
                editBtn.classList.remove('disabled', 'btn-secondary');
                editBtn.classList.add('btn-primary');
                editBtn.href = `index.php?controller=listing&action=edit&id=${currentListingId}`;
            } else {
                editBtn.classList.add('disabled', 'btn-secondary');
                editBtn.classList.remove('btn-primary');
                editBtn.href = "#";
            }

            // Mở Modal và hiển thị Loading
            document.getElementById('modal-content-body').innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
            detailModal.show();

            // Gọi AJAX lấy chi tiết
            fetch(`index.php?controller=manage_listing&action=ajaxGetDetail&id=${currentListingId}`)
                .then(res => res.json())
                .then(response => {
                    if(response.status === 'success') {
                        let data = response.data;
                        document.getElementById('modal-content-body').innerHTML = `
                            <h6 class="fw-bold mb-2">${data.title}</h6>
                            <p class="text-danger fw-bold fs-5 mb-2">${new Intl.NumberFormat('vi-VN').format(data.price)} VNĐ</p>
                            <ul class="list-group list-group-flush small mb-3">
                                <li class="list-group-item px-0"><b>Danh mục:</b> ${data.category_name}</li>
                                <li class="list-group-item px-0"><b>Tình trạng:</b> ${data.condition_name}</li>
                                <li class="list-group-item px-0"><b>Tồn kho:</b> ${data.stock_quantity}</li>
                            </ul>
                            <div class="bg-light p-2 rounded border" style="max-height:100px; overflow-y:auto; font-size:13px">
                                ${data.description.replace(/\n/g, '<br>')}
                            </div>
                        `;
                    }
                });
        });
    });

    // 2. Khi bấm Xóa tin bằng SweetAlert
    document.getElementById('btn-delete-listing').addEventListener('click', function() {
        if(!currentListingId) return;

        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Tin đăng này sẽ bị xóa vĩnh viễn khỏi hệ thống!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Vâng, Xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gửi AJAX Xóa
                let formData = new FormData();
                formData.append('id', currentListingId);

                fetch('index.php?controller=manage_listing&action=ajaxDelete', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        detailModal.hide();
                        Swal.fire('Đã xóa!', data.message, 'success');
                        // Xóa dòng đó khỏi UI mà không cần tải lại trang
                        document.getElementById('row-' + currentListingId).remove();
                    } else {
                        Swal.fire('Lỗi!', data.message, 'error');
                    }
                });
            }
        })
    });

    // 3. Khi bấm nút Ẩn tin (AJAX + SweetAlert)
    document.querySelectorAll('.btn-hide-listing').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-href');
            const listingId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Xác nhận ẩn tin?',
                text: "Tin đăng này sẽ bị ẩn khỏi gian hàng và người mua sẽ không nhìn thấy nữa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Vâng, Ẩn ngay!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang xử lý...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                
                                // Cập nhật lại giao diện tại dòng đó mà không cần load lại trang
                                const row = document.getElementById('row-' + listingId);
                                if (row) {
                                    // Thay đổi Badge Trạng thái thành Đã ẩn (Cột thứ 4)
                                    const statusTd = row.querySelector('td:nth-child(4)');
                                    if (statusTd) {
                                        statusTd.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2"><i class="bi bi-eye-slash-fill"></i> Đã ẩn/Đóng</span>';
                                    }
                                    
                                    // Ẩn/Xóa luôn nút "Ẩn tin"
                                    const hideBtn = row.querySelector('.btn-hide-listing');
                                    if (hideBtn) {
                                        hideBtn.remove();
                                    }
                                }
                            } else {
                                Swal.fire('Lỗi!', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            console.error("Lỗi fetch:", err);
                            Swal.fire('Lỗi!', 'Không thể kết nối máy chủ. Vui lòng kiểm tra lại cấu hình Controller.', 'error');
                        });
                }
            })
        });
    });
});
</script>

<?php require_once __DIR__ . '/partials/user-footer.php'; ?>