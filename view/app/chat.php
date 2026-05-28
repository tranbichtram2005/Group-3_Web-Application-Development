<?php require_once __DIR__ . '/../partials/user-header.php'; ?>

<style>
/* CSS ĐỊNH HÌNH LAYOUT */
.chat-layout { height: 80vh; border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background: #fff; }
.chat-sidebar { border-right: 1px solid #dee2e6; background: #f8f9fa; display: flex; flex-direction: column; height: 100%; }
.chat-body { background: #fff; display: flex; flex-direction: column; height: 100%; min-width: 0; }

/* Khu vực Chat & Thanh cuộn */
.chat-messages { background: #f1f4f6; overflow-y: auto; flex: 1 1 0; padding: 1.5rem; scroll-behavior: smooth; }
.chat-input-area { background: #fff; border-top: 1px solid #eee; padding: 15px; flex-shrink: 0; }
.chat-list-item { transition: background-color 0.2s; cursor: pointer; position: relative; }.chat-list-item:hover { background-color: #e9ecef; }

/* Bong bóng tin nhắn */
.msg-bubble { max-width: 75%; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); word-wrap: break-word; }
.msg-me { background-color: #FF7A3D; color: white; border-radius: 15px 15px 0 15px; margin-left: auto; }
.msg-partner { background-color: #ffffff; color: #212529; border: 1px solid #e9ecef; border-radius: 15px 15px 15px 0; }
.msg-time { font-size: 10px; opacity: 0.7; margin-top: 4px; display: block; text-align: right; }

/* UI MỚI: THANH DEAL BANNER NGANG (Nằm dưới Header) */
.deal-banner-zone { background-color: #fffaf0; border-bottom: 1px solid #ffeed2; padding: 10px 20px; flex-shrink: 0; display: none; }
.deal-banner { display: flex; align-items: center; justify-content: space-between; gap: 15px; }
.deal-banner-info { display: flex; flex-direction: column; }
.deal-banner-price { font-size: 18px; font-weight: 800; color: #FF7A3D; margin-bottom: 2px; }
.deal-banner-desc { font-size: 12px; color: #666; }
.deal-banner-actions { display: flex; gap: 8px; flex-wrap: nowrap; }
</style>

<main class="container py-4">
    <div class="row g-0 chat-layout shadow-sm">
        
        <div class="col-12 col-md-4 chat-sidebar">
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

        <div class="col-12 col-md-8 chat-body position-relative">
            
            <div id="chat-empty" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                <i class="bi bi-chat-heart display-1 opacity-25 mb-3"></i>
                <h5 class="fw-bold">Bắt đầu trò chuyện</h5>
            </div>

            <div id="chat-content" class="d-none flex-column h-100 w-100">
                
                <div class="p-3 border-bottom shadow-sm z-1 bg-white d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
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
    let currentUserId = <?= $_SESSION['user_id'] ?? 0 ?>;
    let actConv = 0, actListing = 0, actBuyer = 0, actChatType = 'trade';
    let actOfferId = 0, actOfferPrice = 0, actOfferQty = 1;
    let lastMsgId = 0;
    let pollInterval = null;
    let currentDealAction = 'create';
    let isFirstLoad = true; 

  // LẮNG NGHE RADAR TỪ HEADER ĐỂ HIỆN CHẤM ĐỎ CẢ MUA BÁN VÀ HỖ TRỢ
    window.addEventListener('unreadCountsUpdated', (e) => {
        let perConv = e.detail; 
        
        document.querySelectorAll('.chat-list-item').forEach(item => {
            let cId = item.dataset.convId;
            let cType = item.dataset.type; // 'trade' hoặc 'support'
            let badge = item.querySelector('.unread-badge');
            if(!badge) return;
            
            let jsonKey = cType + '_' + cId; // VD: trade_1 hoặc support_1
            
            // Đang mở đúng phòng này thì tắt luôn chấm đỏ
            if (cId == actConv && actChatType == cType) {
                badge.classList.add('d-none');
                return;
            }

            // Phòng khác có tin thì bật lên
            if (perConv[jsonKey] && perConv[jsonKey] > 0) {
                badge.textContent = perConv[jsonKey];
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
    });

    // XỬ LÝ CLICK MỞ CHAT
    document.querySelectorAll('.chat-list-item').forEach(item => {
        item.addEventListener('click', function() {
            actConv = this.dataset.convId;
            actChatType = this.dataset.type;
            let partnerName = this.dataset.partnerName;
            
            document.getElementById('chat-empty').classList.add('d-none');
            document.getElementById('chat-content').classList.remove('d-none');
            document.getElementById('chat-content').classList.add('d-flex');
            document.getElementById('chat-partner-name').innerText = partnerName;
            
            document.getElementById('chat-bubbles').innerHTML = ''; 
            document.getElementById('chat-bubbles').innerHTML = '<div id="chat-loading-spinner" class="d-flex justify-content-center align-items-center h-100 mt-5"><div class="spinner-border text-primary"></div></div>';
            document.getElementById('dedicated-deal-zone').style.display = 'none'; 
            lastMsgId = 0;
            isFirstLoad = true;
            // Tắt ngay chấm đỏ khi người dùng vừa click vào
            let badgeEl = this.querySelector('.unread-badge');
            if (badgeEl) badgeEl.classList.add('d-none');

            if (actChatType === 'trade') {
                actListing = this.dataset.listingId;
                actBuyer = this.dataset.buyerId;
                let isBuyer = this.dataset.isBuyer === '1';
                
                document.getElementById('chat-prod-title').innerText = this.dataset.prodTitle;
                document.getElementById('chat-prod-price').innerText = this.dataset.prodPrice;
                document.getElementById('chat-prod-img').src = this.dataset.prodImg || 'https://ui-avatars.com/api/?name=SP';
                
                document.getElementById('chat-product-info').classList.remove('d-none');
                isBuyer ? document.getElementById('btn-deal-price').classList.remove('d-none') : document.getElementById('btn-deal-price').classList.add('d-none');
                
                window.history.pushState({}, '', `index.php?controller=chat&active_trade=${actConv}&listing_id=${actListing}&seller_id=${isBuyer ? 0 : actBuyer}`);
            } else {
                document.getElementById('chat-product-info').classList.add('d-none');
                document.getElementById('btn-deal-price').classList.add('d-none');
                window.history.pushState({}, '', `index.php?controller=chat&active_support=${actConv}`);
            }

            if (pollInterval) clearInterval(pollInterval);
            fetchMessages();
            pollInterval = setInterval(fetchMessages, 3000);
        });
    });

    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('active_trade')) {
            let target = document.getElementById('trade-item-' + urlParams.get('active_trade'));
            if (target) {
                target.click();
                if (urlParams.has('deal')) setTimeout(() => openDealModal('create'), 500); 
            }
        }
    });

  // =========================================================
    // LẤY TIN NHẮN TỪ SERVER (ĐÃ FIX LỖI NHIỄU TIN NHẮN GIỮA CÁC PHÒNG)
    // =========================================================
    async function fetchMessages() {
        if(!actConv) return;
        
        // 🚨 1. CHỐT ID PHÒNG CHAT HIỆN TẠI TRƯỚC KHI GỌI MẠNG (Phát thẻ tên)
        let requestedConvId = actConv; 
        
        let actionUrl = actChatType === 'trade' ? 'getTradeMessagesAjax' : 'getSupportMessagesAjax';
        
        try {
            let res = await fetch(`index.php?controller=chat&action=${actionUrl}&conv_id=${actConv}&last_id=${lastMsgId}&listing_id=${actListing}&buyer_id=${actBuyer}`);
            let json = await res.json();
            
            // 🚨 2. CỔNG KIỂM SOÁT: Nếu User đã chuyển sang phòng khác trong lúc chờ mạng -> Vứt data này đi!
            if (requestedConvId !== actConv) return; 
            
            if(json.status === 'success') {
                if (actChatType === 'trade' && json.offer) {
                    renderDealCard(json.offer);
                } else {
                    document.getElementById('dedicated-deal-zone').style.display = 'none';
                    if(actChatType === 'trade' && document.getElementById('btn-deal-price')) document.getElementById('btn-deal-price').disabled = false;
                }

                if(json.data.length > 0) {
                    let box = document.getElementById('chat-bubbles');
                    let scrollArea = document.getElementById('chat-messages');
                    let isAtBottom = (scrollArea.scrollHeight - scrollArea.scrollTop - scrollArea.clientHeight) < 100;

                    // BẮT ĐẦU VÒNG LẶP IN TIN NHẮN
                    json.data.forEach(msg => {
                        let msgId = parseInt(msg.id, 10);
                        
                        if (msgId > lastMsgId) {
                            lastMsgId = msgId; 
                            
                            let isMe = (actChatType === 'trade') ? (msg.sender_id == currentUserId) : (msg.sender_type_id == 1); 
                            let align = isMe ? 'justify-content-end' : 'justify-content-start';
                            let bubbleClass = isMe ? 'msg-me' : 'msg-partner';
                            
                            let timeStr = new Date(msg.sent_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                            
                            let tickIcon = '';
                            if (isMe) {
                                tickIcon = (msg.is_read == 1) 
                                    ? `<i class="bi bi-check2-all text-primary ms-1 msg-check" data-msg-id="${msgId}"></i>` 
                                    : `<i class="bi bi-check2 text-secondary ms-1 msg-check" data-msg-id="${msgId}"></i>`; 
                            }
                            
                            let msgContent = msg.content;
                            if(msg.attachment_url) {
                                let ext = msg.attachment_url.split('.').pop().toLowerCase();
                                if(['mp4', 'webm', 'ogg'].includes(ext)) {
                                    msgContent = `<video controls class="img-fluid rounded mt-1" style="max-height:200px"><source src="${msg.attachment_url}"></video>`;
                                } else {
                                    msgContent = `<img src="${msg.attachment_url}" class="img-fluid rounded mt-1" style="max-height:200px">`;
                                }
                            }

                            let htmlStr = `
                                <div class="d-flex mb-3 ${align}" id="msg-${msgId}">
                                    <div class="msg-bubble ${bubbleClass}">
                                        ${msgContent}
                                        <div class="msg-time d-flex align-items-center justify-content-end" style="color: ${isMe?'#fff':'#666'}">
                                            ${timeStr} ${tickIcon}
                                        </div>
                                    </div>
                                </div>
                            `;

                            box.insertAdjacentHTML('beforeend', htmlStr);
                        }
                    });
                    
                    if (json.read_until_id > 0) {
                        document.querySelectorAll('.msg-check').forEach(el => {
                            if (parseInt(el.dataset.msgId) <= json.read_until_id) {
                                el.classList.remove('bi-check2', 'text-secondary');
                                el.classList.add('bi-check2-all', 'text-primary'); 
                            }
                        });
                    }
                    
                    document.getElementById('chat-loading-spinner')?.remove();
                    
                    if (isFirstLoad || isAtBottom) {
                        scrollArea.scrollTop = scrollArea.scrollHeight;
                        isFirstLoad = false;
                    }
                }
            }
        } catch(e) { console.error(e); }
    }

    // HIỂN THỊ BANNER DEAL
    function renderDealCard(offer) {
        actOfferId = offer.id;
        actOfferPrice = offer.proposed_price;
        actOfferQty = offer.quantity;
        let isMyProposal = (offer.proposed_by == currentUserId);
        let formatPrice = new Intl.NumberFormat('vi-VN').format(actOfferPrice) + 'đ';
        
        let zone = document.getElementById('dedicated-deal-zone');
        let btnDeal = document.getElementById('btn-deal-price');
        let html = '';

        if (offer.status_id == 1 || offer.status_id == 5) {
            // TRẠNG THÁI: ĐANG DEAL HOẶC TRẢ GIÁ LẠI
            if(btnDeal) btnDeal.disabled = true; 
            
            let buttonsHtml = isMyProposal ? 
                `<span class="badge bg-secondary px-3 py-2">Đang chờ đối phương phản hồi...</span>` : 
                `<button class="btn btn-sm btn-outline-danger fw-bold" onclick="submitDealAPI('reject')">Từ chối</button>
                 <button class="btn btn-sm btn-outline-primary fw-bold" onclick="openDealModal('counter')">Trả giá lại</button>
                 <button class="btn btn-sm btn-success fw-bold" onclick="submitDealAPI('accept')">Đồng ý</button>`;
            
            html = `
            <div class="deal-banner">
                <div class="deal-banner-info">
                    <div class="deal-banner-price">🤝 Trả giá: ${formatPrice}</div>
                    <div class="deal-banner-desc">Đề xuất mua <b>${actOfferQty}</b> sản phẩm. (Hủy sau 24h)</div>
                </div>
                <div class="deal-banner-actions">
                    ${buttonsHtml}
                </div>
            </div>`;
        } 
       else if (offer.status_id == 2) {
            // TRẠNG THÁI: DEAL THÀNH CÔNG
            if(btnDeal) btnDeal.disabled = true;
            let updatedTime = new Date(offer.updated_at).getTime();
            let expireTime = updatedTime + (24 * 60 * 60 * 1000); 
            let now = new Date().getTime();
            
            // 🚨 BẮT BỆNH Ở ĐÂY: Phân biệt chính xác Người Mua / Người Bán
            let isBuyer = false;
            // Ưu tiên 1: Lấy trực tiếp buyer_id từ cục offer để so sánh với người đang đăng nhập
            if (offer.buyer_id) {
                isBuyer = (currentUserId == offer.buyer_id);
            } 
            // Ưu tiên 2: Nếu API không trả offer.buyer_id, xài biến toàn cục actBuyer của phòng chat
            else if (typeof actBuyer !== 'undefined') {
                isBuyer = (currentUserId == actBuyer);
            }

            if (now > expireTime) {
                html = `<div class="deal-banner opacity-75"><div class="fw-bold text-secondary">⏰ Deal đã hết hạn (Quá 24h)</div></div>`;
                if(btnDeal) btnDeal.disabled = false; 
           } else { 
                
                // 1. CẬP NHẬT GIÁ HEADER (GẠCH GIÁ CŨ, HIỆN GIÁ ĐỎ)
                let headerPriceEl = document.getElementById('chat-prod-price');
                if (!headerPriceEl.dataset.origPrice) headerPriceEl.dataset.origPrice = headerPriceEl.innerText; 
                headerPriceEl.innerHTML = `<del class="text-muted small">${headerPriceEl.dataset.origPrice}</del> <strong class="text-danger fs-6">${formatPrice}</strong>`;

                // 2. HIỂN THỊ NÚT ĐÚNG THEO VAI TRÒ
                if (isBuyer) {
                    // Cấp quyền cho NGƯỜI MUA: Hiện nút Mua / Thêm Giỏ
                    html = `
                    <div class="deal-banner">
                        <div class="deal-banner-info">
                            <div class="deal-banner-price text-success">🎉 Thành Công: ${formatPrice}</div>
                            <div class="deal-banner-desc">Giá áp dụng cho <b>${actOfferQty}</b> sản phẩm. Hạn: 24h</div>
                        </div>
                        <div class="deal-banner-actions">
                            <button class="btn btn-sm btn-warning fw-bold text-dark" onclick="addDealToCart(${actListing}, ${actOfferId}, ${actOfferQty}, false)"><i class="bi bi-cart-plus"></i> Thêm Giỏ</button>
                            <button class="btn btn-sm btn-danger fw-bold text-white" onclick="addDealToCart(${actListing}, ${actOfferId}, ${actOfferQty}, true)"><i class="bi bi-bag-check"></i> Mua Ngay</button>
                        </div>
                    </div>`;
                } else {
                    // Tước quyền NGƯỜI BÁN: Ẩn tịt nút đi, chỉ hiện chữ chờ thanh toán
                    html = `
                    <div class="deal-banner" style="background-color: #e8f5e9; border-color: #c8e6c9;">
                        <div class="deal-banner-info">
                            <div class="deal-banner-price text-success">🎉 Deal Thành Công: ${formatPrice}</div>
                            <div class="deal-banner-desc text-dark">Đang chờ người mua thanh toán cho <b>${actOfferQty}</b> sản phẩm.</div>
                        </div>
                    </div>`;
                }
            }
        }
        
        zone.innerHTML = html;
        zone.style.display = 'block'; 
    }

    function openDealModal(actionType) {
        currentDealAction = actionType;
        let qtyWrapper = document.getElementById('deal-qty-wrapper');
        if(actionType === 'counter') {
            document.getElementById('deal-price-input').value = actOfferPrice; 
            qtyWrapper.classList.add('d-none'); 
        } else {
            document.getElementById('deal-price-input').value = '';
            document.getElementById('deal-qty-input').value = 1;
            qtyWrapper.classList.remove('d-none');
        }
        new bootstrap.Modal(document.getElementById('dealModal')).show();
    }

   // HÀM XỬ LÝ API DEAL (Đã fix chống spam click và tối ưu UX)
    async function submitDealAPI(action) {
        let price = (action === 'create' || action === 'counter') ? document.getElementById('deal-price-input').value : actOfferPrice;
        let qty = (action === 'create') ? document.getElementById('deal-qty-input').value : actOfferQty;
        
        if ((action === 'create' || action === 'counter') && (!price || price <= 0)) {
            Swal.fire('Lỗi', 'Vui lòng nhập mức giá hợp lệ!', 'error'); return;
        }

        // ==========================================
        // 1. CHỐNG SPAM: Khóa tất cả các nút ngay khi vừa click
        // ==========================================
        let actionButtons = document.querySelectorAll('.deal-banner-actions button, #btn-submit-deal');
        actionButtons.forEach(btn => {
            btn.disabled = true; // Làm mờ nút, cấm click lần 2
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
        });

        let fd = new FormData();
        fd.append('listing_id', actListing); fd.append('buyer_id', actBuyer);
        fd.append('conv_id', actConv); fd.append('offer_id', actOfferId);
        fd.append('price', price); fd.append('quantity', qty); fd.append('action', action);

        try {
            let res = await fetch('index.php?controller=chat&action=dealAjax', { method: 'POST', body: fd });
            let json = await res.json();
            
            if(json.status === 'success') {
                // Đóng Modal nếu nó đang mở
                let dealModalEl = document.getElementById('dealModal');
                if (dealModalEl && dealModalEl.classList.contains('show')) {
                    bootstrap.Modal.getInstance(dealModalEl)?.hide();
                }
                
                // ==========================================
                // 2. ĐÓNG DEAL NGAY LẬP TỨC: Không cần chờ Server báo về
                // ==========================================
                if (action === 'reject') {
                    // Giấu luôn bảng Deal
                    document.getElementById('dedicated-deal-zone').style.display = 'none';
                    // Mở lại nút "Thương lượng" góc trên cùng bên phải
                    if(document.getElementById('btn-deal-price')) document.getElementById('btn-deal-price').disabled = false;
                }
                
                // Kéo tin nhắn mới (sẽ chứa tin nhắn hệ thống báo Từ chối/Chấp nhận) về
                fetchMessages(); 
            }
        } catch(e) { 
            console.error(e); 
        } finally {
            // Nhả khóa cho nút trong Modal để dùng cho những lần sau
            let btnSubmitModal = document.getElementById('btn-submit-deal');
            if(btnSubmitModal) {
                btnSubmitModal.disabled = false;
                btnSubmitModal.innerHTML = 'Gửi Yêu Cầu';
            }
        }
    }

    // Gắn sự kiện cho nút "Gửi Yêu Cầu" ở trong Modal
    document.getElementById('btn-submit-deal').addEventListener('click', () => submitDealAPI(currentDealAction));

   // =========================================================
    // GỬI TIN NHẮN (GỌN GÀNG, KHÔNG DÙNG TIN TẠM ĐỂ CHỐNG NHÁY)
    // =========================================================
    document.getElementById('form-chat').addEventListener('submit', async function(e) {
        e.preventDefault();
        let input = document.getElementById('chat-input');
        let fileInput = document.getElementById('file-input');
        let content = input.value.trim();
        
        // Kiểm tra nếu rỗng thì không làm gì
        if(!actConv || (content === '' && (!fileInput || !fileInput.files[0]))) return;

        // Lấy dữ liệu form
        let formData = new FormData(this);
        formData.append('conv_id', actConv);
        formData.append('chat_type', actChatType);
        
        // Cực kỳ quan trọng: Xóa ngay ô input để người dùng biết là đã bấm gửi
        input.value = ''; 
        if(fileInput) fileInput.value = '';
        input.placeholder = "Đang gửi..."; // Đổi chữ mờ mờ cho có cảm giác chờ
        
        try {
            // Đẩy dữ liệu thật lên server
            let res = await fetch('index.php?controller=chat&action=sendAjax', { method: 'POST', body: formData });
            let json = await res.json();
            
            if(json.status === 'success') {
                input.placeholder = "Nhập tin nhắn..."; // Trả lại chữ cũ
                
                // Gọi hàm lấy tin nhắn về in ra màn hình CÙNG MỘT LÚC (Chỉ in 1 lần duy nhất)
                await fetchMessages(); 
                
                // Tự động cuộn xuống đáy
                let scrollArea = document.getElementById('chat-messages');
                scrollArea.scrollTop = scrollArea.scrollHeight;
            }
        } catch(e) { 
            console.error(e); 
            input.placeholder = "Lỗi khi gửi, thử lại sau!";
        }
    });

    // =========================================================
    // HÀM BẤM THÊM GIỎ HÀNG NGAY TẠI CHAT (POPUP SWEETALERT2)
    // =========================================================
    // HÀM BẤM THÊM GIỎ HÀNG / MUA NGAY (Tích hợp Deal)
    async function addDealToCart(listingId, offerId, qty, isBuyNow) {
        let fd = new FormData();
        fd.append('listing_id', listingId);
        fd.append('quantity', qty);
        fd.append('offer_id', offerId); 

        try {
            let res = await fetch('index.php?controller=cart&action=addAjax', { method: 'POST', body: fd });
            let json = await res.json();
            
            if(json.status === 'success') {
                if (isBuyNow) {
                   // Gắn ID sản phẩm vào URL để Checkout biết mà lọc
window.location.href = `index.php?controller=checkout&selected_ids=${listingId}`;                } else {
                    // Nếu bấm Thêm Giỏ Hàng: Hiện Popup SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã thêm vào giỏ!',
                        text: 'Sản phẩm áp dụng giá Deal đã nằm trong giỏ.',
                        showCancelButton: true,
                        confirmButtonText: 'Đến giỏ hàng',
                        cancelButtonText: 'Ở lại chat',
                        confirmButtonColor: '#FF7A3D',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?controller=cart';
                        }
                    });
                }
            } else {
                Swal.fire('Lỗi', json.msg || 'Không thể thêm vào giỏ hàng.', 'error');
            }
        } catch (e) { console.error(e); }
    }
    
</script>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>