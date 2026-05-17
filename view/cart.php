<?php
session_start();
// GIẢ LẬP GIỎ HÀNG ĐỂ TEST BACKEND (Vì giao diện đang fix cứng 2 sản phẩm)
// Khi làm thật, cậu sẽ lặp (foreach) cái session này ra HTML ở cột trái nhé
$_SESSION['cart_items'] = [
    [
        'listing_id' => 1, // ID Áo khoác da bò
        'seller_id' => 2,
        'quantity' => 1,
        'unit_price' => 550000
    ],
    [
        'listing_id' => 2, // ID Tai nghe
        'seller_id' => 3,
        'quantity' => 1,
        'unit_price' => 1200000
    ]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - 2Life</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


    <style>
        /* =====================================================
           BIẾN MÀU & STYLE GIỮ NGUYÊN 100% CỦA CẬU
           ===================================================== */
        :root {
            --bg-main:        #FAF7F2;
            --bg-section:     #D6EEF8;
            --bg-card:        #EFE6DD;
            --nav-color:      #1F3C5A;
            --btn-primary:    #FF7A3D;
            --btn-secondary:  #4DA8DA;
            --btn-hover:      #A7D0E8;
            --text-primary:   #2B2B2B;
            --text-secondary: #6B6B6B;
            --border-color:   #D9D9D9;
            --tag-color:      #7C8C6B;
            --error-color:    #FF5E5B;
        }


        * { box-sizing: border-box; }
        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }


        .navbar-2life { background-color: var(--nav-color); padding: 12px 0; }
        .navbar-2life .logo { font-size: 24px; font-weight: 700; color: var(--btn-primary); text-decoration: none; }
        .navbar-2life .nav-link-text { color: #fff; text-decoration: none; font-size: 14px; display: flex; align-items: center; gap: 5px; white-space: nowrap; }
        .navbar-2life .nav-link-text:hover { color: var(--btn-hover); }


        .btn-2life-primary {
            background-color: var(--btn-primary);
            color: #fff; border: none; border-radius: 25px; padding: 10px 22px; font-weight: 600; font-size: 14px; transition: .25s; cursor: pointer;
        }
        .btn-2life-primary:hover { background-color: var(--error-color); color: #fff; }


        .btn-2life-secondary {
            background-color: var(--btn-secondary);
            color: #fff; border: none; border-radius: 25px; padding: 10px 22px; font-weight: 600; font-size: 14px; transition: .25s; cursor: pointer;
        }
        .btn-2life-secondary:hover { background-color: var(--btn-hover); color: var(--text-primary); }


        .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid var(--border-color); }
        .cart-item { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 18px; margin-bottom: 16px; }
        .cart-item img { width: 96px; height: 96px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
        .item-info { flex: 1; min-width: 0; }
        .item-info h3 { font-size: 15px; font-weight: 600; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .seller-name { font-size: 13px; color: var(--text-secondary); }


        .item-quantity { display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: #fff; flex-shrink: 0; }
        .qty-btn { width: 34px; height: 34px; background: none; border: none; font-size: 16px; cursor: pointer; color: var(--text-primary); transition: background .15s; }
        .qty-btn:hover { background-color: var(--bg-section); }
        .qty-input { width: 38px; height: 34px; text-align: center; border: none; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color); font-weight: 600; font-size: 14px; outline: none; }
       
        .item-price { font-size: 16px; font-weight: 700; color: var(--btn-primary); min-width: 110px; text-align: right; flex-shrink: 0; }
        .btn-remove { background: none; border: none; color: var(--error-color); font-size: 13px; font-weight: 600; cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: background .15s; flex-shrink: 0; }
        .btn-remove:hover { background: #fff0f0; }


        .cart-summary { background: #fff; border: 1px solid var(--border-color); border-radius: 14px; padding: 28px; position: sticky; top: 20px; }
        .cart-summary h3 { font-size: 17px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; font-size: 14px; color: var(--text-secondary); }
        .summary-total { border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 8px; font-weight: 700; font-size: 15px; color: var(--text-primary); }
        .total-price { font-size: 22px; font-weight: 700; color: var(--btn-primary); }
        .security-note { font-size: 12px; color: var(--text-secondary); text-align: center; margin-top: 14px; }
    </style>
</head>
<body>


<header class="navbar-2life">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center g-2">
            <div class="col-6 col-md-2"><a href="../index.php" class="logo">2Life</a></div>
            <div class="col-6 col-md-10 d-flex justify-content-end align-items-center gap-2">
                <a href="cart.php" class="nav-link-text position-relative" style="color:var(--btn-primary)">
                    <i class="bi bi-cart3-fill" style="font-size:18px"></i><span class="d-none d-lg-inline ms-1">Giỏ hàng</span>
                </a>
                <a href="#" class="nav-link-text"><i class="bi bi-person-circle" style="font-size:18px"></i><span class="d-none d-lg-inline ms-1">Tài khoản</span></a>
            </div>
        </div>
    </div>
</header>


<main class="container-fluid px-3 px-md-4" style="max-width:1140px;padding-top:28px;padding-bottom:40px">
    <h1 class="page-title"><i class="bi bi-cart3 me-2" style="color:var(--btn-primary)"></i>Giỏ hàng của bạn</h1>


    <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-8">
            <div class="cart-item">
                <input type="checkbox" class="item-check" checked style="width:15px;height:15px;accent-color:var(--btn-primary);flex-shrink:0" onchange="updateSummary()">
                <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=200&q=80" alt="Áo khoác">
                <div class="item-info">
                    <h3>Áo khoác da bò Vintage</h3>
                    <p class="seller-name"><i class="bi bi-person-circle me-1"></i>Người bán: Nguyễn Văn A</p>
                </div>
                <div class="item-quantity">
                    <button type="button" class="qty-btn" onclick="changeQty(this,-1)">−</button>
                    <input type="text" value="1" class="qty-input" readonly data-price="550000">
                    <button type="button" class="qty-btn" onclick="changeQty(this,1)">+</button>
                </div>
                <div class="item-price">550.000đ</div>
                <button type="button" class="btn-remove" onclick="removeItem(this)"><i class="bi bi-trash"></i></button>
            </div>


            <div class="cart-item">
                <input type="checkbox" class="item-check" checked style="width:15px;height:15px;accent-color:var(--btn-primary);flex-shrink:0" onchange="updateSummary()">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&q=80" alt="Tai nghe">
                <div class="item-info">
                    <h3>Tai nghe Chụp tai (Pass nhanh)</h3>
                    <p class="seller-name"><i class="bi bi-person-circle me-1"></i>Người bán: Trần B</p>
                </div>
                <div class="item-quantity">
                    <button type="button" class="qty-btn" onclick="changeQty(this,-1)">−</button>
                    <input type="text" value="1" class="qty-input" readonly data-price="1200000">
                    <button type="button" class="qty-btn" onclick="changeQty(this,1)">+</button>
                </div>
                <div class="item-price">1.200.000đ</div>
                <button type="button" class="btn-remove" onclick="removeItem(this)"><i class="bi bi-trash"></i></button>
            </div>
        </div>


        <div class="col-12 col-lg-4">
           
            <form action="../control/CheckoutController.php" method="POST">
               
                <div class="cart-summary mb-3" style="padding: 20px;">
                    <h3 style="font-size: 16px; margin-bottom: 15px;"><i class="bi bi-geo-alt me-2" style="color:var(--btn-secondary)"></i>Thông tin nhận hàng</h3>
                   
                    <div class="mb-2">
                        <input type="text" name="ward_id" class="form-control" placeholder="Phường/Xã (VD: Phường Điện Hồng)" required style="font-size:13px; border-radius:8px;">
                    </div>
                    <div class="mb-2">
                        <input type="text" name="street_address" class="form-control" placeholder="Số nhà, Tên đường..." required style="font-size:13px; border-radius:8px;">
                    </div>
                    <div class="mb-2">
                        <textarea name="note" class="form-control" placeholder="Ghi chú đơn hàng..." rows="2" style="font-size:13px; border-radius:8px;"></textarea>
                    </div>
                    <div>
                        <select name="payment_method" class="form-select" style="font-size:13px; border-radius:8px;">
                            <option value="1">Thanh toán khi nhận hàng (COD)</option>
                            <option value="2">Chuyển khoản / Ví điện tử</option>
                        </select>
                    </div>
                </div>


                <div class="cart-summary">
                    <h3><i class="bi bi-receipt me-2" style="color:var(--btn-secondary)"></i>Tóm tắt đơn hàng</h3>


                    <div class="summary-row">
                        <span>Tạm tính (<span id="itemCount">2</span> sản phẩm):</span>
                        <span id="subtotal">1.750.000đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span style="color:var(--tag-color);font-weight:600">Thỏa thuận</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Tổng cộng:</span>
                        <span class="total-price" id="totalPrice">1.750.000đ</span>
                    </div>


                    <input type="hidden" name="total_final" id="hiddenTotalInput" value="1750000">


                    <button type="submit" name="place_order" class="btn-2life-primary mt-3" style="width:100%;border-radius:10px;padding:14px;font-size:15px;font-weight:700">
                        <i class="bi bi-lock-fill me-2"></i>Thanh toán ngay
                    </button>
                   
                    <p class="security-note"><i class="bi bi-shield-check me-1" style="color:var(--tag-color)"></i>Kiểm tra hàng trước khi thanh toán</p>
                </div>
            </form>
        </div>
    </div>
</main>


<script>
    /* Javascript được giữ lại y chang, chỉ bổ sung cập nhật input ẩn total_final */
    function changeQty(btn, delta) {
        const input = btn.closest('.item-quantity').querySelector('.qty-input');
        const price = parseInt(input.dataset.price);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        const priceEl = btn.closest('.cart-item').querySelector('.item-price');
        priceEl.textContent = (price * val).toLocaleString('vi-VN') + 'đ';
        updateSummary();
    }


    function removeItem(btn) {
        const item = btn.closest('.cart-item');
        item.style.transition = 'opacity .3s, transform .3s';
        item.style.opacity = '0';
        item.style.transform = 'translateX(30px)';
        setTimeout(() => { item.remove(); updateSummary(); }, 300);
    }


    function updateSummary() {
        const items = document.querySelectorAll('.cart-item:not([style*="display: none"])');
        let total = 0;
        let count = 0;
        items.forEach(item => {
            const check = item.querySelector('.item-check');
            if(check && check.checked) {
                const input = item.querySelector('.qty-input');
                const qty = parseInt(input.value);
                const price = parseInt(input.dataset.price);
                total += qty * price;
                count++;
            }
        });
        document.getElementById('itemCount').textContent = count;
        document.getElementById('subtotal').textContent = total.toLocaleString('vi-VN') + 'đ';
        document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + 'đ';
       
        // Cập nhật giá trị gửi lên backend
        document.getElementById('hiddenTotalInput').value = total;
    }
</script>
</body>
</html>