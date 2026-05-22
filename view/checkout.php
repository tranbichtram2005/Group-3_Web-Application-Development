<?php
// Guard defaults
$checkoutItems  = $checkoutItems  ?? [];
$buyerName      = $buyerName      ?? 'Người mua';
$buyerPhone     = $buyerPhone     ?? '';
$buyerProvince  = $buyerProvince  ?? 'TP. Hồ Chí Minh';
$buyerDistrict  = $buyerDistrict  ?? '';
$buyerWard      = $buyerWard      ?? '';
$buyerStreet    = $buyerStreet    ?? '';
$addressId      = $addressId      ?? null;
$fullAddress    = $fullAddress    ?? '';
$shippingFee    = $shippingFee    ?? 30000;

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
    <style>
        :root {
            --bg-main: #f5f5f5;
            --btn-primary: #FF7A3D;
            --btn-secondary: #4DA8DA;
            --text-primary: #2B2B2B;
        }
        body { background-color: var(--bg-main); color: var(--text-primary); font-family: 'Inter', sans-serif; }
        .checkout-block {
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
            padding: 24px 30px;
            margin-bottom: 16px;
        }
        .shopee-border {
            height: 3px; width: 100%;
            background-size: 116px 3px;
            background-image: repeating-linear-gradient(45deg,#6fa6d6,#6fa6d6 33px,transparent 0,transparent 41px,#f18d9b 0,#f18d9b 74px,transparent 0,transparent 82px);
            margin-bottom: 10px;
        }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e0e0e0; }
        .payment-option input:checked + label { border-color: var(--btn-primary); color: var(--btn-primary); font-weight: 600; background-color: #fffaf8; }
        .payment-option label { border: 1px solid #e0e0e0; padding: 12px 24px; border-radius: 4px; cursor: pointer; transition: 0.2s; }
        /* Address modal */
        .addr-card { border: 1.5px solid #e0e0e0; border-radius: 8px; padding: 14px 16px; cursor: pointer; transition: 0.2s; margin-bottom: 10px; }
        .addr-card:hover { border-color: var(--btn-primary); }
        .addr-card.selected { border-color: var(--btn-primary); background: #fffaf8; }
        .addr-default-badge { background: var(--btn-primary); color: #fff; font-size: 10px; padding: 1px 7px; border-radius: 10px; margin-left: 6px; }
        /* Qty controls */
        .qty-ctrl { display: flex; align-items: center; gap: 4px; }
        .qty-ctrl button { width: 26px; height: 26px; border: 1px solid #ddd; background: #f8f8f8; border-radius: 4px; cursor: pointer; font-size: 16px; line-height: 1; display: flex; align-items: center; justify-content: center; }
        .qty-ctrl input { width: 44px; height: 26px; text-align: center; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>

<header class="bg-white py-3 mb-4 shadow-sm">
    <div class="container d-flex align-items-center">
        <h2 class="mb-0 fw-bold me-3" style="color: var(--btn-primary);">2Life</h2>
        <h4 class="mb-0 text-secondary border-start ps-3 py-1" style="font-size: 1.25rem;">Thanh Toán</h4>
        <a href="index.php?controller=cart" class="ms-auto text-decoration-none text-secondary" style="font-size:13px;"><i class="bi bi-arrow-left me-1"></i>Quay lại giỏ hàng</a>
    </div>
</header>

<main class="container mb-5">
    <form id="checkoutForm" action="index.php?controller=checkout&action=processOrder" method="POST">
        
        <!-- ✅ ĐỊA CHỈ NHẬN HÀNG - Shopee style -->
        <div class="checkout-block pt-0 px-0 overflow-hidden">
            <div class="shopee-border"></div>
            <div class="px-4 py-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-geo-alt-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                    <h5 class="mb-0 fw-bold" style="color: var(--btn-primary); font-size: 1.1rem;">Địa Chỉ Nhận Hàng</h5>
                </div>
                <div id="selectedAddrDisplay" class="d-flex align-items-center flex-wrap gap-2">
                    <?php if($fullAddress): ?>
                    <div>
                        <span class="fw-bold text-dark me-2"><?= htmlspecialchars($buyerName) ?></span>
                        <span class="text-secondary me-2"><?= htmlspecialchars($buyerPhone) ?></span>
                        <span class="text-secondary" id="addrText"><?= htmlspecialchars($fullAddress) ?></span>
                    </div>
                    <?php else: ?>
                    <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Chưa có địa chỉ giao hàng</span>
                    <?php endif; ?>
                    <button type="button" class="ms-auto btn btn-sm btn-outline-primary" style="color:var(--btn-secondary);border-color:var(--btn-secondary);font-size:0.85rem;" onclick="openAddrModal()">
                        <i class="bi bi-pencil me-1"></i>Thay đổi
                    </button>
                </div>
                <input type="hidden" name="streetAddress" id="streetAddressInput" value="<?= htmlspecialchars($fullAddress) ?>">
                <input type="hidden" name="selectedAddressId" id="selectedAddressId" value="<?= htmlspecialchars($addressId) ?>">
                <!-- Phí ship sẽ được cập nhật khi đổi địa chỉ -->
                <input type="hidden" name="shippingProvince" id="shippingProvince" value="<?= htmlspecialchars($buyerProvince) ?>">
            </div>
        </div>

        <!-- SẢN PHẨM -->
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
                        if ($currentSeller !== $item['seller_id']):
                            $currentSeller = $item['seller_id'];
                    ?>
                        <tr>
                            <td colspan="4" class="pt-4 pb-2">
                                <div class="d-flex align-items-center text-dark fw-bold">
                                    <i class="bi bi-shop me-2 text-secondary"></i>
                                    <span><?= htmlspecialchars($item['seller_name']) ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                        
                    <tr class="border-bottom-subtle" id="corow-<?= $item['listing_id'] ?>">
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($item['image']) ?>" class="product-img me-3" alt="">
                                <div>
                                    <span class="fw-semibold text-dark" style="font-size:14px;"><?= htmlspecialchars($item['product_name']) ?></span>
                                    <div style="font-size:11px;color:#388E3C;margin-top:2px;">Kho: <?= (int)($item['stock'] ?? 99) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center text-dark"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</td>
                        <td class="text-center">
                            <!-- ✅ Tăng/Giảm số lượng với check tồn kho -->
                            <div class="qty-ctrl justify-content-center">
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, -1)">−</button>
                                <input type="number" id="coqty-<?= $item['listing_id'] ?>" 
                                       value="<?= (int)$item['quantity'] ?>" 
                                       min="1" max="<?= (int)($item['stock'] ?? 99) ?>"
                                       data-price="<?= (int)$item['unit_price'] ?>"
                                       data-stock="<?= (int)($item['stock'] ?? 99) ?>"
                                       onchange="syncQty(<?= $item['listing_id'] ?>)"
                                       style="width:44px;">
                                <button type="button" onclick="changeCoQty(<?= $item['listing_id'] ?>, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark" id="cosubtotal-<?= $item['listing_id'] ?>"><?= number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>

                    <tr class="bg-light border-top">
                        <td colspan="2" class="py-3 px-3">
                            <div class="d-flex align-items-center">
                                <label class="me-3 text-nowrap text-secondary" style="font-size:0.9rem;">Lời nhắn:</label>
                                <input type="text" name="shippingNote" class="form-control form-control-sm" placeholder="Lưu ý cho Người bán..." style="border-radius:4px;">
                            </div>
                        </td>
                        <td colspan="2" class="py-3 px-3 text-end">
                            <span class="text-success me-3" style="font-size:0.9rem;"><i class="bi bi-truck me-1"></i>Giao hàng nhanh</span>
                            Phí: <span class="fw-bold text-dark" id="shippingFeeDisplay"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                            <span id="shippingNote" class="text-secondary d-block" style="font-size:11px;">(<?= ($shippingFee == 30000 ? 'Nội thành TP.HCM' : 'Ngoài TP.HCM') ?>)</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ✅ VOUCHER -->
        <div class="checkout-block">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-ticket-perforated-fill fs-5 me-2" style="color: var(--btn-primary);"></i>
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">2Life Voucher</span>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" id="voucherInput" placeholder="Nhập mã voucher..." 
                       style="padding:9px 13px;border:1px solid #ddd;border-radius:6px;font-size:14px;text-transform:uppercase;flex:1;min-width:160px;max-width:280px;">
                <button type="button" id="voucherBtn" onclick="applyVoucher()" 
                        style="padding:9px 18px;border:1px solid var(--btn-secondary);border-radius:6px;color:var(--btn-secondary);background:#fff;font-weight:600;font-size:13px;cursor:pointer;">
                    Áp dụng
                </button>
                <span id="voucherMsg" style="font-size:13px;"></span>
            </div>
            <div id="voucherDiscountRow" style="display:none;margin-top:10px;font-size:14px;color:#388E3C;font-weight:600;">
                <i class="bi bi-check-circle me-1"></i>Đã áp dụng: <span id="voucherDiscountLabel"></span>
            </div>
            <input type="hidden" name="voucherCode" id="voucherCodeInput" value="">
            <input type="hidden" name="voucherDiscount" id="voucherDiscountInput" value="0">
        </div>

        <!-- PHƯƠNG THỨC THANH TOÁN -->
        <div class="checkout-block">
            <h6 class="fw-bold text-dark mb-3" style="font-size: 1rem;">Phương thức thanh toán</h6>
            <div class="d-flex gap-3 flex-wrap">
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payCOD" value="1" class="d-none" checked>
                    <label for="payCOD"><i class="bi bi-cash-coin me-1"></i>Thanh toán khi nhận hàng (COD)</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payVNPAY" value="2" class="d-none">
                    <label for="payVNPAY"><img src="https://sandbox.vnpayment.vn/paymentv2/Assets/Images/icon/vnpay.svg" height="20" class="me-1" onerror="this.style.display='none'"> VNPay</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="paymentMethod" id="payMomo" value="3" class="d-none">
                    <label for="payMomo"><i class="bi bi-phone me-1"></i>MoMo</label>
                </div>
            </div>
        </div>

        <!-- TỔNG KẾT -->
        <div class="checkout-block" style="background-color: #fffaf8; border: 1px solid #ffe3d5;">
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Tổng tiền hàng</span>
                        <span class="text-dark" id="summaryMerchandise"><?= number_format($totalMerchandise, 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-dark" id="summaryShipping"><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                    </div>
                    <div id="summaryDiscountRow" class="d-flex justify-content-between mb-2" style="font-size: 0.95rem; display:none !important;">
                        <span class="text-secondary">Giảm giá voucher</span>
                        <span style="color:#388E3C;font-weight:600;" id="summaryDiscount">-0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 mt-3 border-top pt-3">
                        <span class="text-dark fs-5 fw-semibold">Tổng thanh toán</span>
                        <span class="fs-4 fw-bold" style="color: var(--btn-primary);" id="summaryTotal"><?= number_format($totalFinal, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <input type="hidden" name="totalFinal" id="totalFinalInput" value="<?= $totalFinal ?>">
                    <input type="hidden" name="merchandiseTotal" id="merchandiseTotalInput" value="<?= $totalMerchandise ?>">
                    <input type="hidden" name="shippingFee" id="shippingFeeInput" value="<?= $shippingFee ?>">

                    <button type="submit" id="submitBtn"
                            class="btn w-100 fw-bold py-2 fs-5 text-white border-0 shadow-sm" 
                            style="background-color: var(--btn-primary); border-radius: 4px;">
                        <i class="bi bi-bag-check-fill me-2"></i>Đặt Hàng
                    </button>
                    <p class="text-center mt-2" style="font-size:12px;color:#aaa;">
                        <i class="bi bi-shield-check me-1" style="color:var(--btn-primary)"></i>
                        Đơn hàng được bảo vệ bởi 2Life
                    </p>
                </div>
            </div>
        </div>
    </form>
</main>

<!-- ✅ MODAL ĐỊA CHỈ - Shopee style -->
<div class="modal fade" id="addrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--btn-primary);color:#fff;">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt me-2"></i>Địa chỉ của tôi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height:480px;overflow-y:auto;" id="addrListContainer">
                <div class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="showAddAddrForm()" 
                        style="color:var(--btn-primary);border-color:var(--btn-primary);">
                    <i class="bi bi-plus-lg me-1"></i>Thêm địa chỉ mới
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÊM ĐỊA CHỈ MỚI -->
<div class="modal fade" id="addAddrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Họ tên người nhận *</label>
                        <input type="text" id="newName" class="form-control form-control-sm" placeholder="Nguyễn Văn A">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Số điện thoại *</label>
                        <input type="text" id="newPhone" class="form-control form-control-sm" placeholder="0901234567">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tỉnh / Thành phố *</label>
                        <select id="newProvince" class="form-select form-select-sm" onchange="onProvinceChange()">
                            <option value="">-- Chọn tỉnh/thành --</option>
                            <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                            <option value="Hà Nội">Hà Nội</option>
                            <option value="Đà Nẵng">Đà Nẵng</option>
                            <option value="Cần Thơ">Cần Thơ</option>
                            <option value="An Giang">An Giang</option>
                            <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                            <option value="Bắc Giang">Bắc Giang</option>
                            <option value="Bắc Kạn">Bắc Kạn</option>
                            <option value="Bạc Liêu">Bạc Liêu</option>
                            <option value="Bắc Ninh">Bắc Ninh</option>
                            <option value="Bến Tre">Bến Tre</option>
                            <option value="Bình Định">Bình Định</option>
                            <option value="Bình Dương">Bình Dương</option>
                            <option value="Bình Phước">Bình Phước</option>
                            <option value="Bình Thuận">Bình Thuận</option>
                            <option value="Cà Mau">Cà Mau</option>
                            <option value="Cao Bằng">Cao Bằng</option>
                            <option value="Đắk Lắk">Đắk Lắk</option>
                            <option value="Đắk Nông">Đắk Nông</option>
                            <option value="Điện Biên">Điện Biên</option>
                            <option value="Đồng Nai">Đồng Nai</option>
                            <option value="Đồng Tháp">Đồng Tháp</option>
                            <option value="Gia Lai">Gia Lai</option>
                            <option value="Hà Giang">Hà Giang</option>
                            <option value="Hà Nam">Hà Nam</option>
                            <option value="Hà Tĩnh">Hà Tĩnh</option>
                            <option value="Hải Dương">Hải Dương</option>
                            <option value="Hải Phòng">Hải Phòng</option>
                            <option value="Hậu Giang">Hậu Giang</option>
                            <option value="Hòa Bình">Hòa Bình</option>
                            <option value="Hưng Yên">Hưng Yên</option>
                            <option value="Khánh Hòa">Khánh Hòa</option>
                            <option value="Kiên Giang">Kiên Giang</option>
                            <option value="Kon Tum">Kon Tum</option>
                            <option value="Lai Châu">Lai Châu</option>
                            <option value="Lâm Đồng">Lâm Đồng</option>
                            <option value="Lạng Sơn">Lạng Sơn</option>
                            <option value="Lào Cai">Lào Cai</option>
                            <option value="Long An">Long An</option>
                            <option value="Nam Định">Nam Định</option>
                            <option value="Nghệ An">Nghệ An</option>
                            <option value="Ninh Bình">Ninh Bình</option>
                            <option value="Ninh Thuận">Ninh Thuận</option>
                            <option value="Phú Thọ">Phú Thọ</option>
                            <option value="Phú Yên">Phú Yên</option>
                            <option value="Quảng Bình">Quảng Bình</option>
                            <option value="Quảng Nam">Quảng Nam</option>
                            <option value="Quảng Ngãi">Quảng Ngãi</option>
                            <option value="Quảng Ninh">Quảng Ninh</option>
                            <option value="Quảng Trị">Quảng Trị</option>
                            <option value="Sóc Trăng">Sóc Trăng</option>
                            <option value="Sơn La">Sơn La</option>
                            <option value="Tây Ninh">Tây Ninh</option>
                            <option value="Thái Bình">Thái Bình</option>
                            <option value="Thái Nguyên">Thái Nguyên</option>
                            <option value="Thanh Hóa">Thanh Hóa</option>
                            <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
                            <option value="Tiền Giang">Tiền Giang</option>
                            <option value="Trà Vinh">Trà Vinh</option>
                            <option value="Tuyên Quang">Tuyên Quang</option>
                            <option value="Vĩnh Long">Vĩnh Long</option>
                            <option value="Vĩnh Phúc">Vĩnh Phúc</option>
                            <option value="Yên Bái">Yên Bái</option>
                        </select>
                        <div id="shippingFeePreview" style="font-size:12px;margin-top:4px;color:var(--btn-primary);"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Quận / Huyện *</label>
                        <input type="text" id="newDistrict" class="form-control form-control-sm" placeholder="Quận 1">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Phường / Xã *</label>
                        <input type="text" id="newWard" class="form-control form-control-sm" placeholder="Phường Bến Nghé">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Địa chỉ cụ thể (số nhà, tên đường) *</label>
                        <input type="text" id="newStreet" class="form-control form-control-sm" placeholder="123 Đường ABC">
                    </div>
                </div>
                <div id="addAddrMsg" style="font-size:13px;margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="backToAddrList()">Quay lại</button>
                <button type="button" class="btn btn-sm text-white fw-bold" onclick="submitNewAddr()" 
                        style="background:var(--btn-primary);border:none;">
                    <i class="bi bi-save me-1"></i>Lưu địa chỉ
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── State ─────────────────────────────────────────────────
let currentShippingFee   = <?= (int)$shippingFee ?>;
let currentMerchandise   = <?= (int)$totalMerchandise ?>;
let currentDiscount      = 0;
let addrModal, addAddrModal;

document.addEventListener('DOMContentLoaded', () => {
    addrModal    = new bootstrap.Modal(document.getElementById('addrModal'));
    addAddrModal = new bootstrap.Modal(document.getElementById('addAddrModal'));
    recalcTotal();
});

// ── Tính lại tổng ────────────────────────────────────────
function recalcTotal() {
    // Tính lại merchandise từ các row
    let merch = 0;
    document.querySelectorAll('[id^="coqty-"]').forEach(inp => {
        merch += parseInt(inp.value) * parseInt(inp.dataset.price);
    });
    currentMerchandise = merch;

    const total = merch + currentShippingFee - currentDiscount;
    document.getElementById('summaryMerchandise').textContent = merch.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryShipping').textContent    = currentShippingFee.toLocaleString('vi-VN') + 'đ';
    document.getElementById('summaryTotal').textContent       = Math.max(0, total).toLocaleString('vi-VN') + 'đ';
    document.getElementById('totalFinalInput').value          = Math.max(0, total);
    document.getElementById('merchandiseTotalInput').value    = merch;
    document.getElementById('shippingFeeInput').value         = currentShippingFee;

    const discRow = document.getElementById('summaryDiscountRow');
    if (currentDiscount > 0) {
        discRow.style.display = 'flex';
        document.getElementById('summaryDiscount').textContent = '-' + currentDiscount.toLocaleString('vi-VN') + 'đ';
    } else {
        discRow.style.display = 'none';
    }
}

// ── Tăng/Giảm SL trong checkout ──────────────────────────
function changeCoQty(listingId, delta) {
    const inp   = document.getElementById('coqty-' + listingId);
    let qty     = parseInt(inp.value) + delta;
    const stock = parseInt(inp.dataset.stock);
    if (qty < 1) return;
    if (qty > stock) {
        showToast('Chỉ còn ' + stock + ' sản phẩm trong kho!', 'error');
        qty = stock;
    }
    inp.value = qty;
    updateCoRow(listingId);
    recalcTotal();

    // Đồng bộ về DB
    fetch('index.php?controller=checkout&action=updateQtyAjax', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ listingId: listingId, quantity: qty })
    });
}

function syncQty(listingId) {
    const inp   = document.getElementById('coqty-' + listingId);
    const stock = parseInt(inp.dataset.stock);
    let qty     = parseInt(inp.value) || 1;
    if (qty < 1) qty = 1;
    if (qty > stock) { qty = stock; showToast('Chỉ còn ' + stock + ' trong kho!', 'error'); }
    inp.value = qty;
    updateCoRow(listingId);
    recalcTotal();
}

function updateCoRow(listingId) {
    const inp   = document.getElementById('coqty-' + listingId);
    const qty   = parseInt(inp.value);
    const price = parseInt(inp.dataset.price);
    document.getElementById('cosubtotal-' + listingId).textContent = (qty * price).toLocaleString('vi-VN') + 'đ';
}

// ── Địa chỉ modal ────────────────────────────────────────
async function openAddrModal() {
    addrModal.show();
    loadAddrList();
}

async function loadAddrList() {
    const container = document.getElementById('addrListContainer');
    container.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
    try {
        const res  = await fetch('index.php?controller=checkout&action=getAddresses');
        const data = await res.json();

        if (!data.addresses || data.addresses.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-muted">
                <i class="bi bi-geo-alt fs-3"></i><p class="mt-2">Bạn chưa có địa chỉ nào</p>
                <button class="btn btn-sm btn-primary" onclick="showAddAddrForm()" style="background:var(--btn-primary);border:none;">Thêm ngay</button>
            </div>`;
            return;
        }

        container.innerHTML = data.addresses.map(addr => {
            const fullAddrStr = [addr.street, addr.ward, addr.district, addr.province].filter(Boolean).join(', ');
            const isDefault   = addr.is_default == 1;
            return `<div class="addr-card ${isDefault ? 'selected' : ''}" onclick="selectAddr(this, '${escJs(fullAddrStr)}', '${escJs(addr.full_name || '')}', '${escJs(addr.phone || '')}', '${escJs(addr.province || '')}', ${addr.id}, ${addr.shipping_fee || 0})">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-bold">${escHtml(addr.full_name || 'Người nhận')}</span>
                        <span class="text-secondary ms-2" style="font-size:13px;">${escHtml(addr.phone || '')}</span>
                        ${isDefault ? '<span class="addr-default-badge">Mặc định</span>' : ''}
                    </div>
                </div>
                <div class="text-secondary mt-1" style="font-size:13px;">${escHtml(fullAddrStr)}</div>
            </div>`;
        }).join('');
    } catch(e) {
        container.innerHTML = '<div class="text-danger text-center py-3">Không thể tải địa chỉ</div>';
    }
}

function selectAddr(el, fullAddr, name, phone, province, addrId, shippingFeeOverride) {
    // Update display
    document.getElementById('selectedAddrDisplay').innerHTML = `
        <div>
            <span class="fw-bold text-dark me-2">${escHtml(name)}</span>
            <span class="text-secondary me-2">${escHtml(phone)}</span>
            <span class="text-secondary">${escHtml(fullAddr)}</span>
        </div>
        <button type="button" class="ms-auto btn btn-sm btn-outline-primary" style="color:var(--btn-secondary);border-color:var(--btn-secondary);font-size:0.85rem;" onclick="openAddrModal()">
            <i class="bi bi-pencil me-1"></i>Thay đổi
        </button>`;
    document.getElementById('streetAddressInput').value  = fullAddr;
    document.getElementById('selectedAddressId').value   = addrId;
    document.getElementById('shippingProvince').value    = province;

    // ✅ Tính phí ship theo tỉnh
    updateShippingByProvince(province);
    addrModal.hide();
}

function updateShippingByProvince(province) {
    const hcmKw = ['hồ chí minh', 'ho chi minh', 'hcm', 'tp.hcm', 'sài gòn', 'saigon'];
    const lower = province.toLowerCase();
    const isHcm = hcmKw.some(k => lower.includes(k));
    currentShippingFee = isHcm ? 30000 : 50000;

    document.getElementById('shippingFeeDisplay').textContent = currentShippingFee.toLocaleString('vi-VN') + 'đ';
    document.getElementById('shippingNote').textContent       = isHcm ? '(Nội thành TP.HCM)' : '(Ngoài TP.HCM)';
    document.getElementById('summaryShipping').textContent    = currentShippingFee.toLocaleString('vi-VN') + 'đ';
    recalcTotal();
}

// ── Thêm địa chỉ mới ─────────────────────────────────────
function showAddAddrForm() {
    addrModal.hide();
    setTimeout(() => addAddrModal.show(), 300);
}

function backToAddrList() {
    addAddrModal.hide();
    setTimeout(() => addrModal.show(), 300);
}

function onProvinceChange() {
    const prov  = document.getElementById('newProvince').value;
    const hcmKw = ['hồ chí minh', 'tp. hồ chí minh'];
    const isHcm = hcmKw.some(k => prov.toLowerCase().includes(k));
    const fee   = isHcm ? 30000 : 50000;
    document.getElementById('shippingFeePreview').textContent = 
        prov ? `Phí vận chuyển: ${fee.toLocaleString('vi-VN')}đ (${isHcm ? 'Nội thành TP.HCM' : 'Ngoài TP.HCM'})` : '';
}

async function submitNewAddr() {
    const name     = document.getElementById('newName').value.trim();
    const phone    = document.getElementById('newPhone').value.trim();
    const province = document.getElementById('newProvince').value;
    const district = document.getElementById('newDistrict').value.trim();
    const ward     = document.getElementById('newWard').value.trim();
    const street   = document.getElementById('newStreet').value.trim();
    const msgEl    = document.getElementById('addAddrMsg');

    if (!name || !phone || !province || !district || !ward || !street) {
        msgEl.innerHTML = '<span style="color:red;">Vui lòng điền đầy đủ tất cả thông tin.</span>';
        return;
    }

    const btn = event.target;
    btn.disabled = true; btn.textContent = 'Đang lưu...';

    try {
        const res  = await fetch('index.php?controller=checkout&action=addAddress', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ full_name: name, phone, province, district, ward, street })
        });
        const data = await res.json();

        if (data.status === 'success') {
            msgEl.innerHTML = '<span style="color:green;"><i class="bi bi-check-circle me-1"></i>Thêm địa chỉ thành công!</span>';
            
            // Tự động chọn địa chỉ vừa thêm
            const fullAddr = [street, ward, district, province].filter(Boolean).join(', ');
            document.getElementById('selectedAddrDisplay').innerHTML = `
                <div>
                    <span class="fw-bold text-dark me-2">${escHtml(name)}</span>
                    <span class="text-secondary me-2">${escHtml(phone)}</span>
                    <span class="text-secondary">${escHtml(fullAddr)}</span>
                </div>
                <button type="button" class="ms-auto btn btn-sm btn-outline-primary" style="color:var(--btn-secondary);border-color:var(--btn-secondary);font-size:0.85rem;" onclick="openAddrModal()">
                    <i class="bi bi-pencil me-1"></i>Thay đổi
                </button>`;
            document.getElementById('streetAddressInput').value = fullAddr;
            document.getElementById('selectedAddressId').value  = data.id;
            document.getElementById('shippingProvince').value   = province;
            updateShippingByProvince(province);

            setTimeout(() => { addAddrModal.hide(); }, 1200);
        } else {
            msgEl.innerHTML = `<span style="color:red;">${escHtml(data.msg)}</span>`;
        }
    } catch(e) {
        msgEl.innerHTML = '<span style="color:red;">Lỗi kết nối!</span>';
    }

    btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Lưu địa chỉ';
}

// ── Voucher ───────────────────────────────────────────────
async function applyVoucher() {
    const code  = document.getElementById('voucherInput').value.trim();
    const btn   = document.getElementById('voucherBtn');
    const msgEl = document.getElementById('voucherMsg');

    if (!code) { msgEl.innerHTML = '<span style="color:red;">Vui lòng nhập mã voucher.</span>'; return; }
    if (currentMerchandise <= 0) { msgEl.innerHTML = '<span style="color:red;">Không có sản phẩm nào.</span>'; return; }

    btn.disabled = true; btn.textContent = '...';

    try {
        const res  = await fetch('index.php?controller=cart&action=applyVoucher', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: code, orderTotal: currentMerchandise })
        });
        const data = await res.json();

        if (data.status === 'success') {
            currentDiscount = data.discount;
            document.getElementById('voucherDiscountRow').style.display  = 'block';
            document.getElementById('voucherDiscountLabel').textContent  = code.toUpperCase() + ' ' + data.discountFormat;
            document.getElementById('voucherCodeInput').value            = code;
            document.getElementById('voucherDiscountInput').value        = data.discount;
            btn.textContent  = 'Bỏ';
            btn.onclick      = removeVoucher;
            btn.style.cssText = 'padding:9px 18px;border:1px solid #e53935;border-radius:6px;color:#e53935;background:#fff;font-weight:600;font-size:13px;cursor:pointer;';
            msgEl.innerHTML  = `<span style="color:#388E3C;"><i class="bi bi-check-circle me-1"></i>${data.msg}</span>`;
            document.getElementById('voucherInput').disabled = true;
        } else {
            msgEl.innerHTML = `<span style="color:red;">${escHtml(data.msg)}</span>`;
        }
    } catch(e) {
        msgEl.innerHTML = '<span style="color:red;">Lỗi kết nối!</span>';
    }

    btn.disabled = false;
    if (currentDiscount === 0) btn.textContent = 'Áp dụng';
    recalcTotal();
}

function removeVoucher() {
    currentDiscount = 0;
    document.getElementById('voucherDiscountRow').style.display = 'none';
    document.getElementById('voucherCodeInput').value           = '';
    document.getElementById('voucherDiscountInput').value       = 0;
    document.getElementById('voucherInput').disabled            = false;
    document.getElementById('voucherInput').value               = '';
    document.getElementById('voucherMsg').innerHTML             = '';
    const btn = document.getElementById('voucherBtn');
    btn.textContent = 'Áp dụng'; btn.onclick = applyVoucher;
    btn.style.cssText = 'padding:9px 18px;border:1px solid var(--btn-secondary);border-radius:6px;color:var(--btn-secondary);background:#fff;font-weight:600;font-size:13px;cursor:pointer;';
    recalcTotal();
}

// ── Utilities ────────────────────────────────────────────
function escHtml(s) { const d = document.createElement('div'); d.appendChild(document.createTextNode(String(s||''))); return d.innerHTML; }
function escJs(s)   { return String(s||'').replace(/'/g, "\\'").replace(/\n/g, ' '); }

function showToast(msg, type = 'info') {
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;color:#fff;background:${type==='error'?'#e53935':'#43a047'};box-shadow:0 4px 12px rgba(0,0,0,.2);transition:opacity .3s`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(), 300); }, 3000);
}

// Validate trước khi submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const addr = document.getElementById('streetAddressInput').value.trim();
    if (!addr) {
        e.preventDefault();
        showToast('Vui lòng chọn địa chỉ giao hàng!', 'error');
        return;
    }
    const total = parseInt(document.getElementById('totalFinalInput').value);
    if (total <= 0) {
        e.preventDefault();
        showToast('Tổng đơn hàng không hợp lệ!', 'error');
        return;
    }
    // Show loading
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang đặt hàng...';
});
</script>
</body>
</html>