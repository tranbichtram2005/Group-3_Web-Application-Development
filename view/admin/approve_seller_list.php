<?php if (empty($sellers)): ?>
    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
        <i class="bi bi-person-exclamation text-muted fs-1"></i>
        <p class="text-muted mt-2 mb-0">Hiện tại không có hồ sơ đăng ký nào trong danh sách này.</p>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white rounded-4 shadow-sm border">
        <table class="table align-middle table-hover mb-0">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold">
                    <th class="ps-4">Tên Shop Đăng Ký</th>
                    <th>Chủ Sở Hữu</th>
                    <th>Email / SĐT</th>
                    <th>Mã Số Thuế</th>
                    <th>Ngày Đăng Ký</th>
                    <th class="pe-4 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $s): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block"><?= htmlspecialchars($s['shop_name']) ?></span>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?= htmlspecialchars($s['description']) ?></small>
                        </td>
                        <td><span class="fw-semibold text-secondary"><?= htmlspecialchars($s['full_name']) ?></span></td>
                        <td>
                            <span class="small d-block text-dark"><?= htmlspecialchars($s['email']) ?></span>
                            <small class="text-muted"><?= htmlspecialchars($s['phone'] ?? 'Chưa bổ sung') ?></small>
                        </td>
                        <td>
                            <code class="px-2 py-1 bg-light text-danger rounded border small font-monospace">
                                <?= htmlspecialchars($s['tax_code'] ?? 'N/A') ?>
                            </code>
                        </td>
                        <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></small></td>
                        <td class="pe-4 text-end">
                            <a href="index.php?controller=approveseller&action=detail&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-medium">
                                Xem hồ sơ
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>