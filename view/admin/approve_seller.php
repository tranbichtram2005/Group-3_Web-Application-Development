<?php include 'layout/admin-header.php'; ?>

<div class="container-fluid">
    <div class="mb-4">
        <h2 class="h4 mb-1 fw-bold text-dark">Quản Lý Đăng Ký Bán Hàng</h2>
        <p class="text-secondary small">Duyệt yêu cầu mở Shop của các thành viên trên hệ thống.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex gap-2">
                <a href="index.php?controller=approveseller&action=index&tab=pending" class="btn <?= $tab === 'pending' ? 'btn-primary' : 'btn-light text-secondary' ?> btn-sm px-4">
                    Đang chờ duyệt
                </a>
                <a href="index.php?controller=approveseller&action=index&tab=approved" class="btn <?= $tab === 'approved' ? 'btn-success text-white' : 'btn-light text-secondary' ?> btn-sm px-4">
                    Shop đã phê duyệt
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14.5px;">
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th class="ps-4">Chủ tài khoản</th>
                        <th>Tên Shop</th>
                        <th>Số điện thoại</th>
                        <th>Ngày nộp đơn</th>
                        <th class="pe-4 text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($requests)): ?>
                        <?php foreach($requests as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                                </td>
                                <td class="fw-medium text-primary"><?= htmlspecialchars($row['shop_name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td class="pe-4 text-end">
                                    <a href="index.php?controller=approveseller&action=detail&id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="bi bi-search me-1"></i>Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox display-6 text-muted d-block mb-2"></i>
                                Không tìm thấy dữ liệu trong danh sách này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'layout/admin-footer.php'; ?>