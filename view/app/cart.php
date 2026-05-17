<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - 2Life</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">

    <style>
        :root {
            --bg-main:        #FAF7F2;
            --bg-section:     #D6EEF8;
            --bg-card:        #EFE6DD;
            --nav-color:      #1F3C5A;
            --btn-primary:    #FF7A3D;
            --btn-secondary:  #4A90E2;
            --text-primary:   #1F3C5A;
            --text-secondary: #7F8C8D;
            --border-color:   #E0DCD5;
            --error-color:    #E74C3C;
        }

        body {
            background-color: var(--bg-main) !important;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
        }

        .navbar-2life {
            background-color: var(--nav-color);
            padding: 12px 0;
        }

        .navbar-2life .logo {
            color: #fff !important;
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
        }

        .navbar-2life .nav-link-text {
            color: #fff !important;
            text-decoration: none;
            font-weight: 500;
        }

        .navbar-2life .nav-badge {
            background-color: var(--btn-primary);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 11px;
            position: absolute;
            top: -8px;
            right: -10px;
        }

        .page-title {
            color: var(--text-primary);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        /* Ô sản phẩm theo đúng thiết kế của nhóm */
        .cart-item {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .cart-item img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            background-color: var(--bg-card);
        }

        .item-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 6px 0;
            color: var(--text-primary);
        }

        .seller-name {
            font-size: 13px;
            color: var(--text-secondary);
            margin: 0 0 4px 0;
        }

        /* Bộ tăng giảm số lượng */
        .item-quantity {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            background: none;
            border: none;
            font-size: 16px;
            color: var(--text-primary);
            cursor: pointer;
            font-weight: 600;
        }

        .qty-btn:hover {
            background-color: var(--bg-main);
        }

        .qty-input {
            width: 40px;
            height: 36px;
            text-align: center;
            border: none;
            border-left: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--text-primary);
        }

        .item-price {
            font-size: 17px;
            font-weight: 700;
            color: var(--btn-primary);
            min-width: 120px;
            text-align: right;
        }

        .btn-remove {
            background: none;
            border: none;
            color: var(--error-color);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn-remove:hover {
            background-color: #FDF2F2;
        }

        /* Khối tóm tắt đơn hàng bên phải */
        .cart-summary {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        }

        .cart-summary h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .summary-total {
            border-top: 1px solid var(--border-color);
            padding-top: 18px;
            margin-top: 18px;
            font-weight: 700;
            font-size: 16px;
            color: var(--text-primary);
        }

        .total-price {
            font-size: 24px;
            color: var(--btn-primary);
            font-weight: 700;
        }

        .btn-2life-primary {
            background-color: var(--btn-primary) !important;
            color: #fff !important;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            width: 100%;
            transition: 0.2s;
            text-align: center;
        }

        .btn-2life-primary:hover {
            opacity: 0.9;
        }

        .btn-2life-secondary {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .security-note {
            font-size: 12px;
            color: var(--text-secondary);
            text-align: center;
            margin-top: 14px;
        }
    </style>
</head>
<body>

<header class="navbar-2life">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center g-2">
            <div class="col-6 col-md-2">
                <a href="index.php?controller=home" class="logo">2Life</a>
            </div>

            <div class="col-md-5 d-none d-md-block">
                <div class="d-flex align-items-center" style="background:#fff;border-radius:25px;padding:4px 4px 4px 16px;border:1px solid var(--border-color);">
                    <input type="text" placeholder="Tìm kiếm đồ cũ giá hời..." style="flex:1;border:none;outline:none;font-size:14px;background:transparent;color:var(--text-primary);">
                    <button class="btn-2life-primary" style="border-radius:20px;padding:7px 18px;white-space:nowrap;flex-shrink:0; width: auto;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-6 col-md-5 d-flex justify-content-end align-items-center gap-2 gap-md-3">
                <a href="index.php?controller=cart" class="nav-link-text position-relative" title="Giỏ hàng" style="color:var(--btn-primary)">
                    <i class="bi bi-cart3-fill" style="font-size:18px"></i>
                    <span class="nav-badge" id="header-badge"><?= count($cartItems ?? []) ?></span>
                    <span class="d-none d-lg-inline ms-1">Giỏ hàng</span>
                </a>

                <div class="nav-dropdown d-none d-sm-block">
                    <a href="#" class="nav-link-text" title="Quản lý" style="color: #fff;">
                        <i class="bi bi-grid-3x3-gap" style="font-size:16px"></i>
                        <span class="d-none d-lg-inline">Quản lý</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.7"></i>
                    </a>
                </div>

               <a href="#" class="nav-link-text" style="color: #fff;">
    <i class="bi bi-person-circle" style="font-size:18px"></i>
    <span class="d-none d-lg-inline fw-bold">Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Thành viên') ?></span>
</a>
            </div>
        </div>
    </div>
</header>

<main class="container-fluid px-3 px-md-4" style="max-width:1140px;padding-top:28px;padding-bottom:40px">
    <nav style="font-size:13px;color:var(--text-secondary);margin-bottom:16px">
        <a href="index.php?controller=home" style="color:var(--text-secondary);text-decoration:none">Trang chủ</a>
        <span style="margin:0 6px">/</span>
        <span style="color: var(--text-primary); font-weight: 600;">Giỏ hàng</span>
    </nav>

    <h1 class="page-title">
        <i class="bi bi-cart3 me-2" style="color:var(--btn-primary)"></i>Giỏ hàng của bạn
    </h1>

    <?php if(empty($cartItems)): ?>
        <div class="text-center py-5 shadow-sm" style="background: #fff; border: 1px solid var(--border-color); border-radius:16px;">
            <i class="bi bi-cart-x text-muted" style="font-size:5rem;"></i>
            <h4 class="mt-3 fw-bold">Giỏ hàng của bạn đang trống</h4>
            <p class="text-secondary">Hãy tìm thêm những món đồ ưng ý và lấp đầy giỏ hàng nhé!</p>
            <a href="index.php?controller=home" class="btn-2life-primary mt-3 px-4 py-2 fs-6" style="display:inline-block; text-decoration:none; width: auto;">Đi mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3" style="font-size:13px;color:var(--text-secondary)">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)" style="width:15px;height:15px;accent-color:var(--btn-primary)">
                        <span>Chọn tất cả (<span id="tongSoItem"><?= count($cartItems) ?></span> sản phẩm)</span>
                    </label>
                    <button onclick="removeSelected()" style="background:none;border:none;color:var(--error-color);font-size:13px;cursor:pointer;padding:0">
                        <i class="bi bi-trash me-1"></i>Xóa đã chọn
                    </button>
                </div>

                <div id="cart-items-list">
                    <?php foreach($cartItems as $sp): ?>
                    <div class="cart-item" id="item-<?= $sp['listing_id'] ?>" style="transition: all 0.3s ease;">
                        <input type="checkbox" class="item-check" value="<?= $sp['listing_id'] ?>" style="width:15px;height:15px;accent-color:var(--btn-primary);flex-shrink:0" onchange="updateSummary()">
                        
                        <img src="<?= htmlspecialchars($sp['image_url'] ?? 'https://via.placeholder.com/200') ?>" alt="Product Image">
                        
                        <div class="item-info" style="flex: 1;">
                            <h3><?= htmlspecialchars($sp['title']) ?></h3>
                            <p class="seller-name"><i class="bi bi-person-circle me-1"></i>Người bán: <?= htmlspecialchars($sp['seller_name']) ?></p>
                            <span style="font-size:11px;background:#E8F5E9;color:#388E3C;padding:2px 8px;border-radius:4px;margin-top:4px;display:inline-block">Kho: <?= $sp['stock_quantity'] ?></span>
                        </div>
                        
                        <div class="item-quantity">
                            <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, -1)">−</button>
                            <input type="text" value="<?= $sp['quantity'] ?>" class="qty-input" id="qty-<?= $sp['listing_id'] ?>" data-dongia="<?= $sp['price_snapshot'] ?>" readonly>
                            <button class="qty-btn" onclick="changeQty(this, <?= $sp['listing_id'] ?>, 1)">+</button>
                        </div>
                        
                        <div class="item-price" id="price-<?= $sp['listing_id'] ?>">
                            <?= number_format($sp['price_snapshot'] * $sp['quantity'], 0, ',', '.') ?>đ
                        </div>
                        
                        <button class="btn-remove" onclick="removeItem(<?= $sp['listing_id'] ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="cart-summary">
                    <h3><i class="bi bi-receipt me-2" style="color:var(--btn-secondary)"></i>Tóm tắt đơn hàng</h3>

                    <div class="summary-row">
                        <span>Tạm tính (<span id="itemCount">0</span> sản phẩm):</span>
                        <span id="subtotal">0đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span style="color:var(--btn-secondary);font-weight:600">Thỏa thuận</span>
                    </div>
                    <div class="summary-row">
                        <span>Giảm giá:</span>
                        <span style="color:#388E3C;font-weight:600">—</span>
                    </div>

                    <div class="summary-row summary-total">
                        <span>Tổng cộng:</span>
                        <span class="total-price" id="totalPrice">0đ</span>
                    </div>

                    <div class="d-flex gap-2 mt-4 mb-3">
                        <input type="text" placeholder="Nhập mã giảm giá..." style="flex:1;padding:9px 13px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;outline:none;color:var(--text-primary)">
                        <button class="btn-2life-secondary" style="border-radius:8px;padding:9px 14px;font-size:13px;white-space:nowrap">Áp dụng</button>
                    </div>

                    <button class="btn-2life-primary">
                        <i class="bi bi-lock-fill me-2"></i>Thanh toán ngay
                    </button>
                    
                    <a href="index.php?controller=home" style="display:block;text-align:center;margin-top:14px;font-size:13px;color:var(--btn-secondary);text-decoration:none">
                        <i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm
                    </a>
                    
                    <p class="security-note">
                        <i class="bi bi-shield-check me-1" style="color:var(--btn-primary)"></i>
                        Kiểm tra hàng trước khi thanh toán
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
async function sendCartActionToDB(payload) {
    try {
        const response = await fetch('index.php?controller=cart&action=updateAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        
        if (result.status !== 'success') {
            alert('Lỗi từ hệ thống: ' + result.msg);
            return false;
        }
        return result;
    } catch (error) {
        console.error('Lỗi API:', error);
        return false;
    }
}

async function changeQty(btn, listingId, delta) {
    const input = document.getElementById('qty-' + listingId);
    let currentQty = parseInt(input.value);
    let newQty = currentQty + delta;

    if (newQty <= 0) {
        removeItem(listingId);
        return;
    }

    let success = await sendCartActionToDB({ action: 'update', listingId: listingId, quantity: newQty });
    
    if (success) {
        input.value = newQty;
        const priceEl = document.getElementById('price-' + listingId);
        const unitPrice = parseInt(input.dataset.dongia);
        priceEl.textContent = (unitPrice * newQty).toLocaleString('vi-VN') + 'đ';
        updateSummary();
    }
}

async function removeItem(listingId) {
    if (confirm("Cậu chắc chắn muốn bỏ sản phẩm này khỏi giỏ hàng?")) {
        let success = await sendCartActionToDB({ action: 'remove', listingId: listingId });
        if (success) {
            const item = document.getElementById('item-' + listingId);
            item.style.opacity = '0';
            item.style.transform = 'translateX(30px)';
            setTimeout(() => { 
                item.remove(); 
                updateSummary(); 
                if (document.querySelectorAll('.cart-item').length === 0) {
                    window.location.reload();
                }
            }, 300);
        }
    }
}

function toggleAll(master) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = master.checked);
    updateSummary();
}

async function removeSelected() {
    const selectedChecks = document.querySelectorAll('.item-check:checked');
    if (selectedChecks.length === 0) {
        alert("Cậu chưa chọn sản phẩm nào để xóa!");
        return;
    }

    if (confirm("Xóa toàn bộ các sản phẩm đã chọn?")) {
        for (let check of selectedChecks) {
            let listingId = check.value;
            await sendCartActionToDB({ action: 'remove', listingId: listingId });
            document.getElementById('item-' + listingId).remove();
        }
        updateSummary();
        if (document.querySelectorAll('.cart-item').length === 0) {
            window.location.reload();
        }
    }
}

function updateSummary() {
    const items = document.querySelectorAll('.cart-item');
    let total = 0;
    let count = 0;

    items.forEach(item => {
        const check = item.querySelector('.item-check');
        if (check && check.checked) {
            const input = item.querySelector('.qty-input');
            const qty = parseInt(input.value);
            const price = parseInt(input.dataset.dongia);
            total += qty * price;
            count++;
        }
    });

    document.getElementById('itemCount').textContent = count;
    document.getElementById('subtotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + 'đ';

    const currentTotalRows = items.length;
    const tongSoItemEl = document.getElementById('tongSoItem');
    if (tongSoItemEl) tongSoItemEl.textContent = currentTotalRows;

    const badge = document.getElementById('header-badge');
    if (badge) badge.textContent = currentTotalRows;
}
</script>
</body>
</html>