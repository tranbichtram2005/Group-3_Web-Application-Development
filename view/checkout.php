<?php
// Khai báo giá trị mặc định để chống lỗi gạch đỏ Undefined Variable của VS Code Intelephense
$checkoutItems = $checkoutItems ?? [];
$buyerName = $buyerName ?? 'Người mua 2Life';
$buyerPhone = $buyerPhone ?? '(+84) 901 234 567';
$buyerAddress = $buyerAddress ?? 'Phường Điện Hồng, Quận 10, TP. Hồ Chí Minh';

// Tính toán hóa đơn dựa trên dữ liệu thực tế đổ từ database giỏ hàng
$totalMerchandise = 0;
foreach ($checkoutItems as $item) {
    $totalMerchandise += ($item['unit_price'] * $item['quantity']);
}
$shippingFee = 30000;
$totalFinal = $totalMerchandise + $shippingFee;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f5f5f5;
            --btn-primary: #FF7A3D; /* Màu cam chủ đạo của 2Life */
            --btn-secondary: #4DA8DA;
            --text-primary: #2B2B2B;
        }
        body { 
            background-color: var(--bg-main); 
            color: var(--text-primary); 
            font-family: 'Inter', sans-serif;
        }
        .checkout-block {
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
            padding: 24px 30px;
            margin-bottom: 16px;
        }
        /* Dải viền họa tiết phong cách Shopee */
        .shopee-border {
            height: 3px;
            width: 100%;
            background-position-x: -30px;
            background-size: 116px 3px;
            background-image: repeating-linear-gradient(45deg,#6fa6d6,#6fa6d6 33px,transparent 0,transparent 41px,#f18d9b 0,#f18d9b 74px,transparent 0,transparent 82px);
            margin-bottom: 10px;
        }
        .product-img { 
            width: 50px; 
            height: 50px; 
            object-fit: cover; 
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        .payment-option input:checked + label { 
            border-color: var(--btn-primary); 
            color: var(--btn-primary); 
            font-weight: 600;
            background-color: #fffaf8;
        }
        .payment-option label { 
            border: 1px solid #e0e0e0; 
            padding: 12px 24px; 
            border-radius: 4px; 
            cursor: pointer; 
            transition: 0.2s;
        }
    </style>
</head>
<body>

<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container d-flex align-items-center">
        <h2 class="mb-0 fw-bold me-3" style="color: var(--btn-primary);">2Life</h2>
        <h4 class="mb-0 text-secondary border-start ps-3 py-1" style="font-size: 1.25rem;">Thanh Toán</h4>
    </div>
</header>

<main class="container mb-5">
    <form action="index.php?controller=checkout&action=processOrder" method="POST">
        
        <div class="checkout-block pt-0 px-0 overflow-hidden">
            <div class="shopee-border"></div>
            <div class="px-4 py-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                    <h5 class="mb-0 fw-bold" style="color: var(--btn-primary); font-size: 1.1rem;">Địa Chỉ Nhận Hàng</h5>
                </div>
                <div class="d-flex align-items-center fs-6 flex-wrap gap-2">
                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($buyerName); ?> <?php echo htmlspecialchars($buyerPhone); ?></div>
                    <div class="text-secondary ms-2"><?php echo htmlspecialchars($buyerAddress); ?></div>
                    <a href="#" class="ms-auto text-decoration-none fw-semibold" style="color: var(--btn-secondary); font-size: 0.9rem;">Thay đổi</a>
                </div>
                <input type="hidden" name="streetAddress" value="<?php echo htmlspecialchars($buyerAddress); ?>">
            </div>
        </div>

        <div class="checkout-block">
            <table class="table table-borderless align-middle mb-0">
                <thead>
                    <tr class="text-secondary border-bottom" style="font-size: 0.9rem;">
                        <th style="width: 45%; padding-bottom: 16px;">Sản phẩm</th>
                        <th class="text-center" style="padding-bottom: 16px;">Đơn giá</th>
                        <th class="text-center" style="padding-bottom: 16px;">Số lượng</th>
                        <th class="text-end" style="padding-bottom: 16px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentSeller = null;
                    foreach ($checkoutItems as $item): 
                        // Tự động gộp nhóm tiêu đề nếu các sản phẩm thuộc cùng một Người bán
                        if ($currentSeller !== $item['seller_id']):
                            $currentSeller = $item['seller_id'];
                    ?>
                        <tr>
                            <td colspan="4" class="pt-4 pb-2">
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <i class="bi bi-shop me-2 text-secondary"></i>
                                    <span><?php echo htmlspecialchars($item['seller_name']); ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                        
                    <tr class="border-bottom-subtle">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="product-img me-3" alt="Product Image">
                                <span class="text-truncate fw-semibold text-dark" style="max-width: 350px;">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-center text-dark"><?php echo number_format($item['unit_price'], 0, ',', '.'); ?>đ</td>
                        <td class="text-center text-secondary"><?php echo $item['quantity']; ?></td>
                        <td class="text-end fw-bold text-dark"><?php echo number_format($item['unit_price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                    </tr>
                    <?php endforeach; ?>

                    <tr class="bg-light border-top">
                        <td colspan="2" class="py-3 px-3">
                            <div class="d-flex align-items-center">
                                <label for="shippingNote" class="me-3 text-nowrap text-secondary" style="font-size: 0.9 /rem;">Lời nhắn:</label>
                                <input type="text" id="shippingNote" name="shippingNote" class="form-control form-control-sm" placeholder="Lưu ý cho Người bán chuyển đồ thanh lý..." style="border-radius: 4px;">
                            </div>
                        </td>
                        <td colspan="2" class="py-3 px-3 text-end">
                            <span class="text-success me-3" style="font-size: 0.9rem;"><i class="bi bi-truck me-1"></i>Đơn vị vận chuyển Nhanh</span>
                            Phí: <span class="fw-bold text-dark"><?php echo number_format($shippingFee, 0, ',', '.'); ?>đ</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="checkout-block d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-ticket-perforated-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">2Life Voucher</span>
            </div>
            <a href="#" class="text-decoration-none fw-semibold" style="color: var(--btn-secondary); font-size: 0.9rem;">Chọn hoặc nhập mã</a>
        </div>

        <div class="checkout-block">
            <h6 class="fw-bold text-dark mb-3" style="font-size: 1rem;">Phương thức thanh toán</h6>
            <div class="d-flex gap-3 flex-wrap">
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payCOD" value="1" class="d-none" checked>
                    <label for="payCOD"><i class="bi bi-cash-coin me-1"></i>Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payVNPAY" value="2" class="d-none">
                    <label for="payVNPAY"><i class="bi bi-wallet2 me-1"></i>Ví điện tử VNPay / MoMo</label>
                </div>
            </div>
        </div>

        <div class="checkout-block" style="background-color: #fffaf8; border: 1px solid #ffe3d5;">
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Tổng tiền hàng</span>
                        <span class="text-dark"><?php echo number_format($totalMerchandise, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-dark"><?php echo number_format($shippingFee, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 mt-3 border-top pt-3">
                        <span class="text-dark fs-5 fw-semibold">Tổng thanh toán</span>
                        <span class="fs-4 fw-bold" style="color: var(--btn-primary);"><?php echo number_format($totalFinal, 0, ',', '.'); ?>đ</span>
                    </div>
                    
                    <input type="hidden" name="totalFinal" value="<?php echo $totalFinal; ?>">
                    <button type="submit" class="btn w-100 fw-bold py-2 fs-5 text-white border-0 shadow-sm" style="background-color: var(--btn-primary); border-radius: 4px;">
                        Đặt Hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>