<?php require_once __DIR__ . '/../partials/admin-header.php'; ?>

<style>
    /* =========================================
       CSS BẢO ĐẢM GIAO DIỆN ADMIN CHUẨN 100%
       ========================================= */
    .chat-layout { 
        height: calc(100vh - 120px); 
        min-height: 500px; 
        border: 1px solid #dee2e6; 
        border-radius: 12px; 
        overflow: hidden; 
        background: #fff; 
        margin-top: 15px;
        display: flex; /* Bật Flexbox để layout full height, không bị lùn */
    }
    .chat-sidebar { border-right: 1px solid #dee2e6; background: #f8f9fa; display: flex; flex-direction: column; height: 100%; width: 33.333%; }
    .chat-body { background: #fff; display: flex; flex-direction: column; height: 100%; width: 66.666%; overflow: hidden; }
    .chat-messages { 
        background: #f1f4f6; 
        overflow-y: auto; 
        flex: 1 1 auto; 
        padding: 1.5rem; 
        scroll-behavior: smooth; 
        height: 0; /* Ép thanh cuộn hiển thị */
    }
    .chat-input-area { background: #fff; border-top: 1px solid #eee; padding: 15px; flex-shrink: 0; }
    .chat-list-item { transition: background-color 0.2s; cursor: pointer; position: relative;}
    .chat-list-item:hover, .chat-list-item.active { background-color: #e9ecef; border-left: 4px solid #FF7A3D;}
    .msg-bubble { max-width: 85%; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); word-wrap: break-word; }
    
    /* TIN NHẮN ADMIN LUÔN MÀU CAM */
    .msg-me { background-color: #FF7A3D !important; color: white !important; border-radius: 15px 15px 0 15px; margin-left: auto; }
    .msg-partner { background-color: #ffffff; color: #212529; border: 1px solid #e9ecef; border-radius: 15px 15px 15px 0; }
    .msg-time { font-size: 10px; opacity: 0.7; margin-top: 4px; display: block; text-align: right; }
    .ticket-zone { background-color: #fff3cd; border-bottom: 1px solid #ffe69c; padding: 10px 20px; display: none; align-items: center; justify-content: space-between;}

    /* RESPONSIVE CHO ĐIỆN THOẠI */
    @media (max-width: 768px) {
        .chat-layout { flex-direction: column !important; }
        .chat-sidebar { width: 100% !important; height: 35% !important; border-right: none !important; border-bottom: 4px solid #dee2e6 !important; }
        .chat-body { width: 100% !important; height: 65% !important; }
        .chat-sidebar.mobile-hide { display: none !important; }
        .chat-body.mobile-hide { display: none !important; }
        .btn-back-mobile { display: block !important; }
        .ticket-zone { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
</style>

<div class="container-fluid px-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
        <h4 class="fw-bold text-dark"><i class="bi bi-headset text-primary"></i> 2Life Helpdesk</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i> Quay về Web</a>
    </div>

    <div class="chat-layout shadow">
        
        <div class="chat-sidebar" id="admin-sidebar">
            <div class="p-3 border-bottom bg-light fw-bold text-secondary">DANH SÁCH YÊU CẦU HỖ TRỢ</div>
            <div class="flex-grow-1 overflow-y-auto">
                <?php if(!empty($supportConvs)): foreach($supportConvs as $conv): 
                    $badge = '';
                    if ($conv['status_id'] == 1) $badge = '<span class="badge bg-danger">Mới</span>';
                    elseif ($conv['status_id'] == 2) $badge = '<span class="badge bg-warning text-dark">Đang XL</span>';
                    elseif ($conv['status_id'] == 3) $badge = '<span class="badge bg-secondary">Đã Đóng</span>';
                ?>
                <div class="chat-list-item p-3 border-bottom d-flex align-items-center" 
                     data-conv-id="<?= $conv['id'] ?>" 
                     data-user-name="<?= htmlspecialchars((string)$conv['user_name']) ?>" 
                     data-category="<?= htmlspecialchars((string)$conv['category_name']) ?>" 
                     onclick="openChatRoom(this)">
                    <img src="<?= $conv['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode((string)$conv['user_name']) ?>" class="rounded-circle me-3 object-fit-cover" width="50" height="50">
                    <div class="text-truncate w-100 pe-3">
                        <div class="fw-bold fs-6 d-flex justify-content-between">
                            <?= htmlspecialchars((string)$conv['user_name']) ?> <?= $badge ?>
                        </div>
                        <small class="text-muted d-block text-truncate">Chủ đề: <?= htmlspecialchars((string)$conv['category_name']) ?></small>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <div class="p-4 text-center text-muted">Chưa có yêu cầu hỗ trợ nào.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-body position-relative mobile-hide" id="admin-chatbox">
            <div id="chat-empty" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                <i class="bi bi-inbox display-1 opacity-25 mb-3"></i>
                <h5 class="fw-bold">Chọn một Ticket để xử lý</h5>
            </div>

            <div id="chat-content" class="d-none flex-column h-100 w-100">
                <div class="p-3 border-bottom shadow-sm z-1 bg-white d-flex align-items-center" style="flex-shrink: 0;">
                    <button class="btn btn-sm btn-light me-2 d-none btn-back-mobile" onclick="backToSidebar()"><i class="bi bi-chevron-left"></i></button>
                    <div class="d-flex align-items-center gap-3 w-100">
                        <div class="fw-bold fs-5 text-truncate" id="chat-user-name">...</div>
                        <div class="border-start ps-3 ms-2"><span class="badge bg-info text-dark" id="chat-category">...</span></div>
                    </div>
                </div>

                <div id="ticket-action-zone" class="ticket-zone">
                    <div><strong class="text-danger" id="ticket-status-text">Đang tải...</strong></div>
                    <button id="btn-ticket-action" class="btn btn-sm btn-dark fw-bold" onclick="handleTicketAction()"></button>
                </div>

                <div class="chat-messages" id="chat-messages">
                    <div id="chat-bubbles" class="d-flex flex-column w-100 mt-auto"></div>
                </div>

                <div class="chat-input-area" id="admin-input-area">
                    <form id="form-chat" class="d-flex align-items-center gap-2 m-0 w-100">
                        <input type="text" id="chat-input" name="content" class="form-control rounded-pill px-4 bg-light w-100" style="height: 45px;" placeholder="Nhập câu trả lời hỗ trợ..." autocomplete="off">
                        <button type="submit" id="btn-send-chat" class="btn rounded-circle text-white shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #FF7A3D; border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentAdminId = <?= $_SESSION['user_id'] ?>;
    let actConv = 0, lastMsgId = 0, pollInterval = null;
    let currentTicketStatus = 0, currentTicketAdmin = null;

    // 🚨 ĐÃ FIX: HÀM MỞ PHÒNG CHAT ĐƯỢC GỌI TRỰC TIẾP TỪ ONCLICK (Chống lỗi click 100%)
    function openChatRoom(element) {
        actConv = element.dataset.convId;
        document.querySelectorAll('.chat-list-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        // Layout responsive di động
        if(window.innerWidth <= 768) {
            document.getElementById('admin-sidebar').classList.add('mobile-hide');
            document.getElementById('admin-chatbox').classList.remove('mobile-hide');
        }

        document.getElementById('chat-empty').classList.add('d-none');
        document.getElementById('chat-content').classList.remove('d-none');
        document.getElementById('chat-content').classList.add('d-flex');
        
        document.getElementById('chat-user-name').innerText = "KH: " + element.dataset.userName;
        document.getElementById('chat-category').innerText = element.dataset.category;
        document.getElementById('chat-bubbles').innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>';
        
        lastMsgId = 0;
        if (pollInterval) clearInterval(pollInterval);
        fetchAdminMessages();
        pollInterval = setInterval(fetchAdminMessages, 3000);
    }

    function backToSidebar() {
        document.getElementById('admin-sidebar').classList.remove('mobile-hide');
        document.getElementById('admin-chatbox').classList.add('mobile-hide');
    }

    async function fetchAdminMessages() {
        if(!actConv) return;
        let requestedConvId = actConv; 
        
        try {
            let res = await fetch(`index.php?controller=admin_chat&action=getMessagesAjax&conv_id=${actConv}&last_id=${lastMsgId}`);
            let json = await res.json();
            if (requestedConvId !== actConv) return; 
            
            if(json.status === 'success') {
                currentTicketStatus = json.conv_info.status_id;
                currentTicketAdmin = json.conv_info.admin_id;
                updateTicketUI();

                if(json.data.length > 0) {
                    let box = document.getElementById('chat-bubbles');
                    if (lastMsgId === 0) box.innerHTML = ''; 
                    let scrollArea = document.getElementById('chat-messages');
                    let isAtBottom = (scrollArea.scrollHeight - scrollArea.scrollTop - scrollArea.clientHeight) < 100;

                    json.data.forEach(msg => {
                        let msgId = parseInt(msg.id, 10);
                        if (msgId > lastMsgId) {
                            lastMsgId = msgId; 
                            let isMe = (msg.sender_type_id == 2); 
                            let align = isMe ? 'justify-content-end' : 'justify-content-start';
                            let bubbleClass = isMe ? 'msg-me' : 'msg-partner';
                            let timeStr = new Date(msg.sent_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                            
                            box.insertAdjacentHTML('beforeend', `
                                <div class="d-flex mb-3 ${align}">
                                    <div class="msg-bubble shadow-sm ${bubbleClass}">
                                        ${msg.content}
                                        <div class="msg-time" style="color: ${isMe?'#fff':'#666'}">${timeStr}</div>
                                    </div>
                                </div>
                            `);
                        }
                    });
                    
                    if (isAtBottom || document.querySelectorAll('.msg-bubble').length <= json.data.length) {
                        scrollArea.scrollTop = scrollArea.scrollHeight;
                    }
                } else if (lastMsgId === 0) {
                    document.getElementById('chat-bubbles').innerHTML = '<div class="text-center text-muted mt-4">Chưa có tin nhắn nào.</div>';
                }
            }
        } catch(e) { console.error(e); }
    }

    // KHÓA / MỞ FORM CHAT ADMIN
    function updateTicketUI() {
        let zone = document.getElementById('ticket-action-zone');
        let text = document.getElementById('ticket-status-text');
        let btn = document.getElementById('btn-ticket-action');
        let inputEl = document.getElementById('chat-input');
        let btnSendEl = document.querySelector('#admin-input-area button[type="submit"]');

        zone.style.display = 'flex';
        document.getElementById('admin-input-area').classList.remove('d-none'); // Luôn hiện

        if (currentTicketStatus == 1) { 
            text.innerHTML = '⚠️ Khách hàng đang chờ hỗ trợ. Hãy tiếp nhận để chat!';
            btn.innerHTML = '<i class="bi bi-person-raised-hand"></i> Tiếp Nhận';
            btn.className = 'btn btn-sm btn-danger fw-bold';
            btn.style.display = 'block';
            inputEl.disabled = true; btnSendEl.disabled = true;
            inputEl.placeholder = "⚠️ Bấm Tiếp nhận để bắt đầu chat...";
        } 
        else if (currentTicketStatus == 2) { 
            if (currentTicketAdmin == currentAdminId) {
                text.innerHTML = '✅ Bạn đang xử lý Ticket này.';
                btn.innerHTML = '<i class="bi bi-lock-fill"></i> Đóng Ticket';
                btn.className = 'btn btn-sm btn-success fw-bold';
                btn.style.display = 'block';
                inputEl.disabled = false; btnSendEl.disabled = false;
                inputEl.placeholder = "Nhập câu trả lời hỗ trợ...";
            } else {
                text.innerHTML = '🔒 Một Admin khác đang xử lý Ticket này.';
                btn.style.display = 'none';
                inputEl.disabled = true; btnSendEl.disabled = true;
                inputEl.placeholder = "🔒 Admin khác đang xử lý...";
            }
        } 
        else if (currentTicketStatus == 3) { 
            text.innerHTML = '📁 Ticket này đã được giải quyết và đóng lại.';
            btn.style.display = 'none';
            inputEl.disabled = true; btnSendEl.disabled = true;
            inputEl.placeholder = "📁 Ticket đã đóng.";
        }
    }

    async function handleTicketAction() {
        let action = (currentTicketStatus == 1) ? 'claimAjax' : 'closeAjax';
        let confirmText = (action === 'claimAjax') ? "Bạn muốn tiếp nhận xử lý yêu cầu này?" : "Xác nhận đóng Ticket?";
        
        let result = await Swal.fire({
            title: 'Xác nhận thao tác?',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy bỏ'
        });

        if (result.isConfirmed) {
            let fd = new FormData(); fd.append('conv_id', actConv);
            let res = await fetch(`index.php?controller=admin_chat&action=${action}`, { method: 'POST', body: fd });
            let json = await res.json();
            if(json.status === 'success') { window.location.reload(); }
        }
    }

    document.getElementById('form-chat').addEventListener('submit', async function(e) {
        e.preventDefault();
        let input = document.getElementById('chat-input');
        if(!actConv || input.value.trim() === '') return;

        let fd = new FormData(this); fd.append('conv_id', actConv);
        input.value = ''; input.placeholder = "Đang gửi...";
        
        try {
            let res = await fetch('index.php?controller=admin_chat&action=sendAjax', { method: 'POST', body: fd });
            let json = await res.json();
            if(json.status === 'success') {
                input.placeholder = "Nhập câu trả lời hỗ trợ...";
                fetchAdminMessages(); 
            }
        } catch(e) { }
    });
</script>

<?php require_once __DIR__ . '/../partials/admin-footer.php'; ?>