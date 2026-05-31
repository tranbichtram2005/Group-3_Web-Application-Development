<?php require_once __DIR__ . '/../partials/user-header.php'; ?>

<main class="container py-4">
    <div class="row g-0 chat-layout shadow-sm">
        
        <div class="col-12 col-md-4 chat-sidebar" id="user-sidebar">
            <div class="p-2 border-bottom bg-white">
                <ul class="nav nav-pills nav-fill gap-2 p-1 bg-light rounded-pill">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill fw-bold py-1" data-bs-toggle="tab" data-bs-target="#trade-chat" onclick="actChatType='trade'">Mua Bán</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill fw-bold py-1" data-bs-toggle="tab" data-bs-target="#support-chat" onclick="actChatType='support'">Hỗ Trợ</button>
                    </li>
                </ul>
            </div>

            <div class="tab-content flex-grow-1 overflow-y-auto bg-white">
                <div class="tab-pane fade show active" id="trade-chat">
                    <?php if(!empty($tradeConvs)): foreach($tradeConvs as $conv): 
                        $isBuyer = ($conv['buyer_id'] == $_SESSION['user_id']);
                        $pName = $isBuyer ? $conv['seller_name'] : $conv['buyer_name'];
                        $pAva = $isBuyer ? $conv['seller_avatar'] : $conv['buyer_avatar'];
                    ?>
                    <div id="trade-item-<?= $conv['id'] ?>" class="chat-list-item p-3 border-bottom d-flex align-items-center"
                         data-conv-id="<?= $conv['id'] ?>" data-type="trade" data-partner-name="<?= htmlspecialchars($pName) ?>"
                         data-is-buyer="<?= $isBuyer ? 1 : 0 ?>" data-listing-id="<?= $conv['listing_id'] ?>" data-buyer-id="<?= $conv['buyer_id'] ?>"
                         data-prod-title="<?= htmlspecialchars($conv['product_title']) ?>" data-prod-img="<?= htmlspecialchars($conv['product_image'] ?? '') ?>"
                         data-prod-price="<?= number_format($conv['product_price'], 0, ',', '.') ?>đ">
                        <img src="<?= $pAva ?? 'https://ui-avatars.com/api/?name='.$pName ?>" class="rounded-circle me-3 object-fit-cover" width="50" height="50">
                        <div class="text-truncate w-100">
                            <div class="fw-bold fs-6"><?= htmlspecialchars($pName) ?></div>
                            <small class="text-muted text-truncate d-block">SP: <?= htmlspecialchars($conv['product_title']) ?></small>
                        </div>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 badge bg-danger rounded-pill unread-badge d-none" style="font-size: 10px;">0</span>
                    </div>
                    <?php endforeach; else: ?>
                        <div class="p-4 text-center text-muted">Chưa có cuộc trò chuyện.</div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="support-chat">
                    <?php if(!empty($supportConvs)): foreach($supportConvs as $conv): ?>
                    <div id="support-item-<?= $conv['id'] ?>" class="chat-list-item p-3 border-bottom d-flex align-items-center"
                         data-conv-id="<?= $conv['id'] ?>" data-type="support" data-partner-name="Admin (<?= htmlspecialchars($conv['category_name']) ?>)">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;"><i class="bi bi-headset fs-4"></i></div>
                        <div class="text-truncate w-100">
                            <div class="fw-bold fs-6">Admin 2Life</div>
                            <small class="text-muted text-truncate d-block">Chủ đề: <?= htmlspecialchars($conv['category_name']) ?></small>
                        </div>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 badge bg-danger rounded-pill unread-badge d-none" style="font-size: 10px;">0</span>
                    </div>
                    <?php endforeach; else: ?>
                        <div class="p-4 text-center text-muted">Chưa có yêu cầu hỗ trợ.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 chat-body position-relative" id="user-chatbox">
            
            <div id="chat-empty" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                <i class="bi bi-chat-heart display-1 opacity-25 mb-3"></i>
                <h5 class="fw-bold">Bắt đầu trò chuyện</h5>
            </div>

            <div id="chat-content" class="d-none flex-column h-100 w-100">
                
                <!-- ĐÃ THÊM CLASS chat-header-zone VÀO ĐÂY ĐỂ CSS BẮT ĐƯỢC -->
                <div class="p-3 border-bottom shadow-sm z-1 bg-white d-flex justify-content-between align-items-center chat-header-zone" style="flex-shrink: 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold fs-5" id="chat-partner-name">...</div>
                        <div id="chat-product-info" class="d-none border-start ps-3 ms-2 d-flex align-items-center gap-2">
                            <img id="chat-prod-img" src="" width="35" height="35" class="rounded object-fit-cover border">
                            <div style="line-height: 1.2;">
                                <div class="text-truncate fw-semibold" style="font-size: 12px; max-width: 150px;" id="chat-prod-title"></div>
                                <div class="fw-bold text-danger" style="font-size: 12px;" id="chat-prod-price"></div>
                            </div>
                        </div>
                    </div>
                    <button id="btn-deal-price" class="btn btn-sm fw-bold text-white rounded-pill px-3 d-none" style="background-color: #FF7A3D;" onclick="openDealModal('create')">
                        <i class="bi bi-tags me-1"></i> Thương lượng
                    </button>
                </div>

                <div id="dedicated-deal-zone" class="deal-banner-zone"></div>

                <div class="chat-messages" id="chat-messages">
                    <div id="chat-bubbles" class="d-flex flex-column w-100 mt-auto"></div>
                </div>

                <div class="chat-input-area">
                    <form id="form-chat" class="d-flex align-items-center gap-2 m-0 w-100">
                        <label class="btn btn-light border rounded-circle text-secondary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; cursor: pointer; margin: 0;">
                            <i class="bi bi-image fs-5"></i>
                            <input type="file" name="file" id="file-input" accept="image/*,video/*" class="d-none" onchange="document.getElementById('chat-input').placeholder = 'Đã đính kèm file: ' + this.files[0].name">
                        </label>
                        <input type="text" id="chat-input" name="content" class="form-control rounded-pill px-4 bg-light w-100" style="height: 45px;" placeholder="Nhập tin nhắn..." autocomplete="off">
                        <button type="submit" class="btn rounded-circle text-white shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #1F3C5A; border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="dealModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning py-2">
        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-tags"></i> Nhập đề xuất</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <label class="small fw-bold text-muted mb-1">Mức giá đề xuất (VNĐ)</label>
        <input type="number" id="deal-price-input" class="form-control mb-3 text-center fw-bold" placeholder="VD: 150000">
        <div id="deal-qty-wrapper">
            <label class="small fw-bold text-muted mb-1">Số lượng mua</label>
            <input type="number" id="deal-qty-input" class="form-control mb-4 text-center fw-bold" value="1" min="1">
        </div>
        <button class="btn btn-dark w-100 rounded-pill fw-bold" id="btn-submit-deal">Gửi Yêu Cầu</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Trạm trung chuyển ID từ PHP sang file script.js
    window.CHAT_USER_ID = <?= $_SESSION['user_id'] ?? 0 ?>;
</script>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>