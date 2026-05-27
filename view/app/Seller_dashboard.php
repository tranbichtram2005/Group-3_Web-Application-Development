<!DOCTYPE html>
<?php
// Code phòng thủ (Guard) để tránh lỗi Undefined variable
$stats = $stats ?? ['total_revenue' => 0, 'total_orders' => 0];
$pendingCount = $pendingCount ?? 0;
$startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $endDate ?? date('Y-m-d');
$dates = $dates ?? [];
$revenues = $revenues ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kênh Người Bán - Báo Cáo Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { transition: 0.3s; border-left: 5px solid #FF7A3D; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        
        /* Chế độ in báo cáo: Ẩn đi các thành phần không cần thiết */
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">

<div class="no-print">
    <?php include __DIR__ . '/../partials/user-header.php'; ?>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="bi bi-shop text-warning me-2"></i>Kênh Người Bán - Tổng Quan</h3>
        
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                <i class="bi bi-printer me-1"></i> In báo cáo
            </button>
            <a href="index.php?controller=dashboard&action=export&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-success shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Xuất CSV
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 no-print rounded-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row align-items-end g-3">
                <input type="hidden" name="controller" value="dashboard">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn text-white fw-bold w-100" style="background-color: #FF7A3D;">
                        <i class="bi bi-funnel-fill me-1"></i> Áp dụng lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-semibold mb-2">Doanh thu thuần (Đã hoàn thành)</h6>
                    <h2 class="text-success fw-bold mb-0"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?> đ</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 h-100 rounded-4" style="border-left-color: #0dcaf0;">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-semibold mb-2">Đơn hàng thành công</h6>
                    <h2 class="text-dark fw-bold mb-0"><?= number_format($stats['total_orders'] ?? 0) ?> <span class="fs-6 text-muted fw-normal">đơn</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm border-0 h-100 rounded-4" style="border-left-color: #dc3545;">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-semibold mb-2">Đơn cần xử lý gấp</h6>
                    <h2 class="text-danger fw-bold mb-0"><?= number_format($pendingCount) ?> <span class="fs-6 text-muted fw-normal">đơn</span></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Biểu đồ doanh thu (<?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>)</h5>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>
</div>

<div class="no-print">
    <?php include __DIR__ . '/../partials/user-footer.php'; ?>
</div>

<script>
    // Dữ liệu đổ từ PHP xuống JS
    const chartLabels = <?= json_encode($dates) ?>;
    const chartData = <?= json_encode($revenues) ?>;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Biểu đồ đường mềm mại
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: chartData,
                borderColor: '#FF7A3D',
                backgroundColor: 'rgba(255, 122, 61, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#FF7A3D',
                pointRadius: 4,
                fill: true,
                tension: 0.3 // Làm cong đường đồ thị
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>