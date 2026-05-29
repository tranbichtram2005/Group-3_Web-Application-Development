<?php
/** 
 * @var array  $checkoutItems 
 * @var string $buyerName 
 * @var string $buyerPhone 
 * @var string $buyerProvince 
 * @var string $buyerDistrict 
 * @var string $buyerWard 
 * @var string $buyerStreet 
 * @var string $fullAddress 
 * @var int    $shippingFee 
 * @var bool   $isDirectCheckout 
 * @var int    $directListingId 
 * @var int    $directQuantity 
 */

$checkoutItems    = $checkoutItems  ?? [];
$buyerName        = $buyerName      ?? 'Người mua';
$buyerPhone       = $buyerPhone     ?? '';
$buyerProvince    = $buyerProvince  ?? 'Thành phố Hồ Chí Minh';
$buyerDistrict    = $buyerDistrict  ?? '';
$buyerWard        = $buyerWard      ?? '';
$buyerStreet      = $buyerStreet    ?? '';
$fullAddress      = $fullAddress    ?? '';
$shippingFee      = $shippingFee    ?? 30000;

$isDirectCheckout = $isDirectCheckout ?? false;
$directListingId  = $directListingId ?? 0;
$directQuantity   = $directQuantity ?? 0;

$totalMerchandise = 0;
foreach ($checkoutItems as $item) {
    $totalMerchandise += ($item['unit_price'] * $item['quantity']);
}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg-main: #f5f5f5; --btn-primary: #FF7A3D; --btn-secondary: #4DA8DA; --text-primary: #2B2B2B; }
        body { background-color: var(--bg-main); color: var(--text-primary); font-family: 'Inter', sans-serif; }
        .checkout-block { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.04); padding: 24px 30px; margin-bottom: 16px; }
        .shopee-border { height: 3px; width: 100%; background-image: repeating-linear-gradient(45deg,#6fa6d6,#6fa6d6 33px,transparent 0,transparent 41px,#f18d9b 0,#f18d9b 74px,transparent 0,transparent 82px); margin-bottom: 10px; }
        .product-img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #e0e0e0; }
        
        /* CSS cho Phương thức thanh toán màu mè xịn xò */
        .payment-option { flex: 1; min-width: 250px; }
        .payment-option input:checked + label { 
            border-color: var(--btn-primary); 
            background-color: #fffaf8; 
            color: var(--btn-primary); 
            box-shadow: 0 0 0 1px var(--btn-primary);
        }
        .payment-option label { 
            border: 1px solid #e0e0e0; 
            padding: 16px 20px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            color: #555; 
            transition: all 0.2s ease; 
            display: flex; 
            align-items: center;
            background: #fff;
            height: 100%;
        }
        .payment-option label:hover {
            border-color: var(--btn-secondary);
            background-color: #f4fbff;
        }

        /* Tinh chỉnh Mobile */
        @media (max-width: 768px) {
            .checkout-block { padding: 16px 15px; border-radius: 0; }
        }

        .qty-ctrl { display: flex; align-items: center; gap: 4px; }
        .qty-ctrl button { width: 30px; height: 30px; border: 1px solid #ddd; background: #f8f8f8; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold;}
        .qty-ctrl button:disabled { background: #eee; color: #aaa; cursor: not-allowed; border-color: #eee;}
        .qty-ctrl input { width: 55px; height: 30px; text-align: center; border: 1px solid #ddd; border-radius: 4px; font-weight: 500; }
        .qty-ctrl input:disabled { background: #eee; color: #666; cursor: not-allowed; border-color: #eee;}
        .qty-ctrl input::-webkit-outer-spin-button, .qty-ctrl input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        
        .price-col { min-width: 110px; }
        .qty-col { min-width: 120px; }
        .total-col { min-width: 120px; }
    </style>
</head>
<body>

<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container d-flex align-items-center">
        <a href="index.php?controller=home" onclick="confirmLeave(event)" class="text-decoration-none">
            <h2 class="mb-0 fw-bold me-3" style="color: var(--btn-primary); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">2Life</h2>
        </a>
        <h4 class="mb-0 text-secondary border-start ps-3 py-1 d-none d-md-block" style="font-size: 1.25rem;">Thanh Toán</h4>
        
        <a href="index.php?controller=cart" onclick="confirmLeave(event)" 
           class="ms-auto btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-3 px-md-4 py-2 d-flex align-items-center"
           style="border-color: #dcdcdc; color: #555;">
            <i class="bi bi-arrow-left me-1 me-md-2"></i> <span class="d-none d-md-inline">Quay lại Giỏ Hàng</span><span class="d-inline d-md-none">Trở lại</span>
        </a>
    </div>
</header>

<main class="container mb-5">
    <form id="checkoutForm" action="index.php?controller=checkout&action=processOrder" method="POST">
        
        <?php if ($isDirectCheckout): ?>
            <input type="hidden" name="direct_listing_id" value="<?= htmlspecialchars((string)$directListingId) ?>">
            <input type="hidden" name="direct_quantity" value="<?= htmlspecialchars((string)$directQuantity) ?>">
        <?php endif; ?>

        <!-- ĐỊA CHỈ NHẬN HÀNG -->
        <div class="checkout-block pt-0 px-0 overflow-hidden">
            <div class="shopee-border"></div>
            <div class="px-3 px-md-4 py-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                    <h5 class="mb-0 fw-bold" style="color: var(--btn-primary); font-size: 1.1rem;">Địa Chỉ Nhận Hàng</h5>
                </div>
                <div id="selectedAddrDisplay" class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                    <div class="mb-2 mb-md-0">
                        <span class="fw-bold text-dark me-2" style="font-size:15px;"><?= htmlspecialchars($buyerName) ?></span>
                        <span class="fw-bold text-dark me-3" style="font-size:15px;"><?= htmlspecialchars($buyerPhone) ?></span>
                        <span class="text-secondary d-block d-md-inline mt-1 mt-md-0" id="addrText"><?= htmlspecialchars($fullAddress ? $fullAddress : 'Chưa thiết lập địa chỉ giao hàng') ?></span>
                    </div>
                    <button type="button" class="ms-md-auto btn btn-sm btn-outline-primary" style="color:var(--btn-secondary);border-color:var(--btn-secondary); width: fit-content;" onclick="openAddrModal()">
                        <i class="bi bi-pencil me-1"></i>Thay đổi
                    </button>
                </div>
                <input type="hidden" name="streetAddress" id="streetAddressInput" value="<?= htmlspecialchars($fullAddress) ?>">
            </div>
        </div>

        <!-- THÔNG TIN SẢN PHẨM -->
        <div class="checkout-block px-3 px-md-4">
            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Thông tin Đơn hàng</h5>
            
            <?php 
            $currentSeller = null;
            foreach ($checkoutItems as $item): 
                if ($currentSeller !== $item['seller_id']):
                    $currentSeller = $item['seller_id'];
            ?>
                <div class="d-flex align-items-center text-dark fw-bold mb-3 mt-4">
                    <i class="bi bi-shop me-2 fs-5 text-secondary"></i><span class="fs-6"><?= htmlspecialchars($item['seller_name']) ?></span>
                </div>
            <?php endif; ?>
                
                <div class="d-flex flex-column flex-md-row align-items-md-center py-3 border-bottom-subtle gap-3" id="corow-<?= $item['listing_id'] ?>">
                    
                    <!-- Cột Hình và Tên (Đã bỏ max-width để full màn hình) -->
                    <div class="d-flex align-items-center flex-grow-1" style="min-width: 0; width: 100%;">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="product-img me-3 shadow-sm flex-shrink-0">
                        <div style="min-width: 0; width: 100%;">
                            <div class="fw-semibold text-dark mb-1 text-truncate" style="font-size:15px;">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </div>
                            <?php if(!empty($item['is_deal'])): ?>
                                <span class="badge bg-warning text-dark border"><i class="bi bi-tag-fill me-1"></i>Giá Deal</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Cột Giá, Số lượng, Thành tiền -->
                    <div class="d-flex align-items-center justify-content-between justify-content-md-end mt-2 mt-md-0 w-100 w-md-auto gap-2 gap-md-4">
                        
                        <div class="text-start text-md-center price-col">
                            <span class="text-muted small d-block d-md-none mb-1">Đơn giá</span>
                            <span class="text-dark fw-medium"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="text-center qty-col d-flex flex-column align-items-center">
                            <span class="text-muted small d-block d-md-none mb-1">Số lượng</span>
                            <div class="qty-ctrl">
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, -1)" <?= !empty($item['is_deal']) ? 'disabled' : '' ?>>−</button>
                                
                                <input type="number" id="coqty-<?= $item['listing_id'] ?>" 
                                       value="<?= (int)$item['quantity'] ?>" 
                                       min="1"
                                       data-price="<?= (int)$item['unit_price'] ?>"
                                       data-stock="<?= (int)($item['stock'] ?? 99) ?>" 
                                       onchange="manualChangeQty(<?= $item['listing_id'] ?>, event)"
                                       <?= !empty($item['is_deal']) ? 'disabled' : '' ?>>
                                       
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, 1)" <?= !empty($item['is_deal']) ? 'disabled' : '' ?>>+</button>
                            </div>
                        </div>

                        <div class="text-end total-col">
                            <span class="text-muted small d-block d-md-none mb-1">Thành tiền</span>
                            <span class="fw-bold text-dark fs-6" id="cosubtotal-<?= $item['listing_id'] ?>">
                                <?= number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') ?>đ
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between align-items-center py-3 bg-light rounded px-3 mt-3 border">
                <div class="text-dark fw-medium">
                    <i class="bi bi-truck text-success me-2 fs-5"></i>Đơn vị vận chuyển: <span class="text-muted d-none d-sm-inline">Giao hàng Tiêu Chuẩn</span>
                </div>
                <div class="text-dark fw-bold fs-6">
                    <?= number_format($shippingFee, 0, ',', '.') ?>đ
                </div>
            </div>
        </div>

        <!-- VOUCHER -->
        <div class="checkout-block px-3 px-md-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-ticket-perforated-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">2Life Voucher</span>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" id="voucherInput" placeholder="Nhập mã voucher..." style="padding:9px 13px;border:1px solid #ddd;border-radius:6px;flex:1;max-width:280px;text-transform:uppercase;">
                <button type="button" id="voucherBtn" onclick="applyVoucher()" style="padding:9px 18px;border:1px solid var(--btn-secondary);border-radius:6px;color:var(--btn-secondary);background:#fff;font-weight:600;font-size:13px;cursor:pointer;transition:0.2s;">Áp dụng</button>
                <span id="voucherMsg" style="font-size:13px;"></span>
            </div>
            <div id="voucherDiscountRow" style="display:none;margin-top:10px;font-size:14px;color:#388E3C;font-weight:600;">
                <i class="bi bi-check-circle me-1"></i>Đã áp dụng: <span id="voucherDiscountLabel"></span>
            </div>
            <input type="hidden" name="voucherCodeInput" id="voucherCodeInput" value="">
            <input type="hidden" name="voucherDiscountInput" id="voucherDiscountInput" value="0">
        </div>

        <!-- PHƯƠNG THỨC THANH TOÁN (Đã làm đẹp) -->
        <div class="checkout-block px-3 px-md-4">
            <h6 class="fw-bold text-dark mb-3" style="font-size: 1rem;">Phương thức thanh toán</h6>
            <div class="d-flex gap-3 flex-wrap">
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payCOD" value="1" class="d-none" checked>
                    <label for="payCOD">
                        <i class="bi bi-cash-coin fs-4 me-3" style="color: #28a745;"></i>
                        <div>
                            <div class="fw-bold text-dark">Thanh toán tiền mặt</div>
                            <div class="small fw-normal text-muted">Thanh toán khi nhận hàng (COD)</div>
                        </div>
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payVNPAY" value="2" class="d-none">
                    <label for="payVNPAY">
                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png" height="30" class="me-3" style="object-fit: contain;"> 
                        <div>
                            <div class="fw-bold text-dark">Ví VNPay</div>
                            <div class="small fw-normal text-muted">Thẻ ATM / Internet Banking / QR</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- TỔNG KẾT & ĐẶT HÀNG -->
        <div class="checkout-block px-3 px-md-4" style="background-color: #fffaf8; border: 1px solid #ffe3d5;">
            <div class="row justify-content-end">
                <div class="col-md-6 col-lg-5">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tổng tiền hàng</span>
                        <span class="text-dark" id="summaryMerchandise"><?= number_format($totalMerchandise, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-dark" id="summaryShipping"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                    </div>
                    <div id="summaryDiscountRow" class="d-flex justify-content-between mb-2" style="display:none !important;">
                        <span class="text-secondary">Giảm giá voucher</span>
                        <span style="color:#388E3C;font-weight:600;" id="summaryDiscount">-0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 mt-3 border-top pt-3">
                        <span class="text-dark fs-5 fw-semibold">Tổng thanh toán</span>
                        <span class="fs-3 fw-bold" style="color: var(--btn-primary);" id="summaryTotal"><?= number_format($totalFinal, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <input type="hidden" name="totalFinal" id="totalFinalInput" value="<?= $totalFinal ?>">

                    <button type="submit" id="submitBtn" class="btn w-100 fw-bold py-3 fs-5 text-white border-0 shadow-sm" style="background-color: var(--btn-primary); border-radius: 8px;">
                        <i class="bi bi-cart-check-fill me-2"></i>Đặt Hàng Ngay
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<div class="modal fade" id="addrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--btn-primary);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt me-2"></i>Thay đổi địa chỉ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tỉnh / Thành phố *</label>
                        <select id="sessProvince" class="form-select form-select-sm" onchange="loadDistricts()">
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Quận / Huyện *</label>
                        <select id="sessDistrict" class="form-select form-select-sm" onchange="loadWards()" disabled>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Phường / Xã *</label>
                        <select id="sessWard" class="form-select form-select-sm" disabled>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Số nhà, tên đường *</label>
                        <input type="text" id="sessStreet" class="form-control form-control-sm" placeholder="Ví dụ: 730 Sư Vạn Hạnh">
                    </div>
                </div>
                <div id="addrMsg" class="mt-2" style="font-size:13px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-sm text-white fw-bold" onclick="submitSessAddr()" style="background:var(--btn-primary);border:none;">Cập nhật</button>
            </div>
</div>
        </div>
    </div>

<script>
    window.currentShippingFee = <?= (int)($shippingFee ?? 0) ?>;
    window.currentMerchandise = <?= (int)($totalMerchandise ?? 0) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="layout/script.js?v=<?= time() ?>"></script>

</body>
</html>