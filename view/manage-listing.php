<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tin đăng - 2Life</title>
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
                <a href="index.php" class="logo">2Life</a>
            </div>
            <div class="col-md-5 d-none d-md-block">
                <div class="d-flex align-items-center" style="background:#fff;border-radius:25px;padding:4px 4px 4px 16px;border:1px solid var(--border-color);">
                    <input type="text" placeholder="Tìm kiếm trong cửa hàng của bạn..." style="flex:1;border:none;outline:none;font-size:14px;background:transparent;">
                    <button class="btn-2life-primary" style="border-radius:20px;padding:7px 18px;"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-6 col-md-5 d-flex justify-content-end align-items-center gap-3">
                <a href="index.php?controller=listing&action=create" class="btn-2life-primary text-white d-none d-sm-inline-block" style="border-radius: 20px; text-decoration:none; padding: 8px 16px;">
                    <i class="bi bi-plus-lg me-1"></i> Đăng tin mới
                </a>
                <a href="#" class="nav-link-text"><i class="bi bi-person-circle" style="font-size:18px"></i> Kênh Seller</a>
            </div>
        </div>
    </div>
</header>

<main class="container post-main">
    <nav class="breadcrumb-2life">
        <a href="index.php">Trang chủ</a><span class="sep">/</span>
        <a href="#">Kênh người bán</a><span class="sep">/</span>
        <span>Quản lý tin đăng</span>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold fs-3 mb-1">Danh sách tin đăng</h1>
            <p class="text-secondary small mb-0">Xem lịch sử, trạng thái kiểm duyệt và quản lý các mặt hàng đồ cũ đang bán.</p>
        </div>
        <a href="index.php?controller=listing&action=create" class="btn btn-primary d-sm-none w-100"><i class="bi bi-plus-lg"></i> Đăng tin mới</a>
    </div>

    <div class="card-white p-3 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?controller=manage_listing&action=index&tab=all" class="btn <?= $currentTab === 'all' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3 border-radius-lg">
                        Tất cả (<?= $counts['all_count'] ?? 0 ?>)
                    </a>
                    <a href="index.php?controller=manage_listing&action=index&tab=active" class="btn <?= $currentTab === 'active' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">
                        Đang bán (<?= $counts['active_count'] ?? 0 ?>)
                    </a>
                    <a href="index.php?controller=manage_listing&action=index&tab=pending" class="btn <?= $currentTab === 'pending' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">
                        Chờ duyệt (<?= $counts['pending_count'] ?? 0 ?>)
                    </a>
                    <a href="index.php?controller=manage_listing&action=index&tab=hidden" class="btn <?= $currentTab === 'hidden' ? 'btn-2life-primary text-white' : 'btn-2life-outline' ?> btn-sm px-3">
                        Đã ẩn/đóng (<?= $counts['hidden_count'] ?? 0 ?>)
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <form action="index.php" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="controller" value="manage_listing">
                    <input type="hidden" name="action" value="index">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($currentTab) ?>">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo tên sản phẩm..." value="<?= htmlspecialchars($searchKeyword) ?>">
                    <button type="submit" class="btn btn-secondary btn-sm px-3"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-white p-0 overflow-hidden shadow-sm">
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
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3 py-2">
                                        <img src="<?= !empty($item['primary_image']) ? htmlspecialchars($item['primary_image']) : 'https://placehold.co/60x60?text=No+Image' ?>" 
                                             alt="product" class="rounded border object-cover" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 280px;"><?= htmlspecialchars($item['title']) ?></h6>
                                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 11px;"><?= htmlspecialchars($item['category_name']) ?></span>
                                            <span class="text-muted small ms-2"><i class="bi bi-box-seam me-1"></i>SL: <?= $item['stock_quantity'] ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-danger">
                                    <?= number_format($item['price'], 0, ',', '.') ?> VNĐ
                                </td>
                                <td class="text-secondary small">
                                    <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($item['status_id'] == 2): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Đang bán</span>
                                    <?php elseif ($item['status_id'] == 1): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1.5"><i class="bi bi-hourglass-split me-1"></i> Chờ duyệt</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5"><i class="bi bi-eye-slash-fill me-1"></i> Đã ẩn/Đóng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-menu-item text-dark" href="index.php?controller=product&action=detail&id=<?= $item['id'] ?>"><i class="bi bi-eye me-2"></i>Xem chi tiết</a></li>
                                            
                                            <?php if ($item['status_id'] == 2): ?>
                                                <li><a class="dropdown-menu-item text-warning" href="index.php?controller=manage_listing&action=changeStatus&type=hide&id=<?= $item['id'] ?>" onclick="return confirm('Bạn chắc chắn muốn ẩn tin đăng này?')"><i class="bi bi-eye-slash me-2"></i>Ẩn tin</a></li>
                                                <li><a class="dropdown-menu-item text-success" href="index.php?controller=manage_listing&action=changeStatus&type=sold&id=<?= $item['id'] ?>" onclick="return confirm('Đánh dấu sản phẩm này đã bán thành công?')"><i class="bi bi-check2-circle me-2"></i>Đã bán</a></li>
                                            <?php endif; ?>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-menu-item text-secondary disabled" href="#"><i class="bi bi-pencil me-2"></i>Sửa thông tin</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x display-5 d-block mb-2 text-muted"></i>
                                Không tìm thấy tin đăng nào phù hợp trong danh sách này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>