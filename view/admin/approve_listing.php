<?php include 'view/partials/admin-header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold">Phê duyệt tin đăng bán</h2>
            <p class="text-secondary small mb-0">Quản lý và kiểm duyệt các sản phẩm do người dùng đăng tải.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link <?= $tab == 'pending' ? 'active bg-primary' : 'bg-light text-dark' ?>"
                        href="index.php?controller=approvelisting&action=index&tab=pending">
                        Chờ duyệt (<?= $countPending ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab == 'active' ? 'active bg-success' : 'bg-light text-dark' ?>"
                        href="index.php?controller=approvelisting&action=index&tab=active">
                        Đang hiển thị (<?= $countActive ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab == 'rejected' ? 'active bg-danger' : 'bg-light text-dark' ?>"
                        href="index.php?controller=approvelisting&action=index&tab=rejected">
                        Đã từ chối (<?= $countRejected ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab == 'hidden' ? 'active bg-secondary' : 'bg-light text-dark' ?>"
                        href="index.php?controller=approvelisting&action=index&tab=hidden">
                        Bị ẩn / Gỡ (<?= $countHidden ?>)
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="card-footer bg-white border-0 py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?controller=approvelisting&action=index&tab=<?= $tab ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Trước</span>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?controller=approvelisting&action=index&tab=<?= $tab ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?controller=approvelisting&action=index&tab=<?= $tab ?>&page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">Sau &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tên sản phẩm</th>
                        <th>Người bán</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Ngày gửi</th>
                        <th class="pe-4 text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listings)): ?>
                        <?php foreach ($listings as $item): ?>
                            <tr>
                                <td class="ps-4 fw-medium" style="max-width: 250px;">
                                    <div class="text-truncate"><?= htmlspecialchars($item['title']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i><?= htmlspecialchars($item['seller_name']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($item['category_name']) ?></td>
                                <td class="text-danger fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                <td class="pe-4 text-end">
                                    <a href="index.php?controller=approvelisting&action=detail&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bi bi-inbox display-4 mb-3 d-block text-muted"></i>
                                Không có dữ liệu trong danh sách này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'view/partials/admin-footer.php'; ?>