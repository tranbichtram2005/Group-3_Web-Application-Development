<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Helpdesk - 2Life</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .chat-layout { height: 85vh; border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background: #fff; margin-top: 20px;}
        .chat-sidebar { border-right: 1px solid #dee2e6; background: #fff; display: flex; flex-direction: column; height: 100%; }
        .chat-body { background: #fff; display: flex; flex-direction: column; height: 100%; min-width: 0; }
        .chat-messages { background: #f1f4f6; overflow-y: auto; flex: 1 1 0; padding: 1.5rem; scroll-behavior: smooth; }
        .chat-input-area { background: #fff; border-top: 1px solid #eee; padding: 15px; flex-shrink: 0; }
        .chat-list-item { transition: background-color 0.2s; cursor: pointer; position: relative;}
        .chat-list-item:hover { background-color: #f8f9fa; }
        .msg-bubble { max-width: 75%; padding: 10px 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); word-wrap: break-word; }
        .msg-me { background-color: #1F3C5A; color: white; border-radius: 15px 15px 0 15px; margin-left: auto; }
        .msg-partner { background-color: #ffffff; color: #212529; border: 1px solid #e9ecef; border-radius: 15px 15px 15px 0; }
        .msg-time { font-size: 10px; opacity: 0.7; margin-top: 4px; display: block; text-align: right; }
        .ticket-zone { background-color: #fff3cd; border-bottom: 1px solid #ffe69c; padding: 10px 20px; display: none; align-items: center; justify-content: space-between;}
    </style>
</head>
<body>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h4 class="fw-bold text-dark"><i class="bi bi-headset text-primary"></i> 2Life Helpdesk (Admin)</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i> Quay về Web</a>
    </div>

    <div class="row g-0 chat-layout shadow">
        <div class="col-12 col-md-4 chat-sidebar">
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
                     data-user-name="<?= htmlspecialchars($conv['user_name']) ?>"
                     data-category="<?= htmlspecialchars($conv['category_name']) ?>">
                    <img src="<?= $conv['user_avatar'] ?? 'https://ui-avatars.com/api/?name='.$conv['user_name'] ?>" class="rounded-circle me-3 object-fit-cover" width="50" height="50">
                    <div class="text-truncate w-100 pe-3">
                        <div class="fw-bold fs-6 d-flex justify-content-between">
                            <?= htmlspecialchars($conv['user_name']) ?> <?= $badge ?>
                        </div>
                        <small class="text-muted d-block text-truncate">Chủ đề: <?= htmlspecialchars($conv['category_name']) ?></small>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <div class="p-4 text-center text-muted">Chưa có yêu cầu hỗ trợ nào.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-8 chat-body position-relative">
            <div id="chat-empty" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                <i class="bi bi-inbox display-1 opacity-25 mb-3"></i>
                <h5 class="fw-bold">Chọn một Ticket để xử lý</h5>
            </div>

            <div id="chat-content" class="d-none flex-column h-100 w-100">
                <div class="p-3 border-bottom shadow-sm z-1 bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold fs-5" id="chat-user-name">...</div>
                        <div class="border-start ps-3 ms-2">
                            <span class="badge bg-info text-dark" id="chat-category">...</span>
                        </div>
                    </div>
                </div>

                <div id="ticket-action-zone" class="ticket-zone">
                    <div><strong class="text-danger" id="ticket-status-text">Đang tải...</strong></div>
                    <button id="btn-ticket-action" class="btn btn-sm btn-dark fw-bold" onclick="handleTicketAction()"></button>
                </div>

                <div class="chat-messages" id="chat-messages">
                    <div id="chat-bubbles" class="d-flex flex-column w-100 mt-auto"></div>
                </div>

                <div class="chat-input-area d-none" id="admin-input-area">
                    <form id="form-chat" class="d-flex align-items-center gap-2 m-0 w-100">
                        <input type="text" id="chat-input" name="content" class="form-control rounded-pill px-4 bg-light w-100" style="height: 45px;" placeholder="Nhập câu trả lời hỗ trợ..." autocomplete="off">
                        <button type="submit" class="btn rounded-circle text-white shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #1F3C5A; border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAdminId = <?= $_SESSION['user_id'] ?>;
    let actConv = 0, lastMsgId = 0, pollInterval = null;
    let currentTicketStatus = 0; 
    let currentTicketAdmin = null;

    document.querySelectorAll('.chat-list-item').forEach(item => {
        item.addEventListener('click', function() {
            actConv = this.dataset.convId;
            document.getElementById('chat-empty').classList.add('d-none');
            document.getElementById('chat-content').classList.remove('d-none');
            document.getElementById('chat-content').classList.add('d-flex');
            
            document.getElementById('chat-user-name').innerText = "KH: " + this.dataset.userName;
            document.getElementById('chat-category').innerText = this.dataset.category;
            document.getElementById('chat-bubbles').innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div></div>';
            
            lastMsgId = 0;
            if (pollInterval) clearInterval(pollInterval);
            fetchAdminMessages();
            pollInterval = setInterval(fetchAdminMessages, 3000);
        });
    });

    async function fetchAdminMessages() {
        if(!actConv) return;
        let requestedConvId = actConv; 
        
        try {
            let res = await fetch(`index.php?controller=admin_chat&action=getMessagesAjax&conv_id=${actConv}&last_id=${lastMsgId}`);
            let json = await res.json();
            if (requestedConvId !== actConv) return; 
            
            if(json.status === 'success') {
                // CẬP NHẬT GIAO DIỆN TICKET
                currentTicketStatus = json.conv_info.status_id;
                currentTicketAdmin = json.conv_info.admin_id;
                updateTicketUI();

                // IN TIN NHẮN
                if(json.data.length > 0) {
                    let box = document.getElementById('chat-bubbles');
                    if (lastMsgId === 0) box.innerHTML = ''; 
                    let scrollArea = document.getElementById('chat-messages');
                    let isAtBottom = (scrollArea.scrollHeight - scrollArea.scrollTop - scrollArea.clientHeight) < 100;

                    json.data.forEach(msg => {
                        let msgId = parseInt(msg.id, 10);
                        if (msgId > lastMsgId) {
                            lastMsgId = msgId; 
                            let isMe = (msg.sender_type_id == 2); // 2 là Admin
                            let align = isMe ? 'justify-content-end' : 'justify-content-start';
                            let bubbleClass = isMe ? 'msg-me' : 'msg-partner';
                            let timeStr = new Date(msg.sent_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                            
                            box.insertAdjacentHTML('beforeend', `
                                <div class="d-flex mb-3 ${align}">
                                    <div class="msg-bubble ${bubbleClass}">
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
                    document.getElementById('chat-bubbles').innerHTML = '';
                }
            }
        } catch(e) { console.error(e); }
    }

    // KIỂM SOÁT QUYỀN TRUY CẬP TICKET
    function updateTicketUI() {
        let zone = document.getElementById('ticket-action-zone');
        let text = document.getElementById('ticket-status-text');
        let btn = document.getElementById('btn-ticket-action');
        let inputArea = document.getElementById('admin-input-area');

        zone.style.display = 'flex';

        if (currentTicketStatus == 1) { // MỚI
            text.innerHTML = '⚠️ Khách hàng đang chờ hỗ trợ. Hãy tiếp nhận để chat!';
            btn.innerHTML = '<i class="bi bi-person-raised-hand"></i> Tiếp Nhận';
            btn.className = 'btn btn-sm btn-danger fw-bold';
            btn.style.display = 'block';
            inputArea.classList.add('d-none'); // Khóa form chat
        } 
        else if (currentTicketStatus == 2) { // ĐANG XỬ LÝ
            if (currentTicketAdmin == currentAdminId) {
                text.innerHTML = '✅ Bạn đang xử lý Ticket này.';
                btn.innerHTML = '<i class="bi bi-lock-fill"></i> Đóng Ticket';
                btn.className = 'btn btn-sm btn-success fw-bold';
                btn.style.display = 'block';
                inputArea.classList.remove('d-none'); // Mở form chat
            } else {
                text.innerHTML = '🔒 Một Admin khác đang xử lý Ticket này.';
                btn.style.display = 'none';
                inputArea.classList.add('d-none');
            }
        } 
        else if (currentTicketStatus == 3) { // ĐÃ ĐÓNG
            text.innerHTML = '📁 Ticket này đã được giải quyết và đóng lại.';
            btn.style.display = 'none';
            inputArea.classList.add('d-none');
        }
    }

    // BẤM NÚT (TIẾP NHẬN HOẶC ĐÓNG)
    async function handleTicketAction() {
        let action = (currentTicketStatus == 1) ? 'claimAjax' : 'closeAjax';
        let confirmText = (action === 'claimAjax') ? "Bạn muốn tiếp nhận xử lý yêu cầu này?" : "Xác nhận đóng Ticket?";
        
        if(confirm(confirmText)) {
            let fd = new FormData(); fd.append('conv_id', actConv);
            let res = await fetch(`index.php?controller=admin_chat&action=${action}`, { method: 'POST', body: fd });
            let json = await res.json();
            if(json.status === 'success') {
                lastMsgId = 0; 
                document.getElementById('chat-bubbles').innerHTML = '';
                fetchAdminMessages();
            }
        }
    }

    // GỬI TIN NHẮN TỪ ADMIN
    document.getElementById('form-chat').addEventListener('submit', async function(e) {
        e.preventDefault();
        let input = document.getElementById('chat-input');
        if(!actConv || input.value.trim() === '') return;

        let fd = new FormData(this);
        fd.append('conv_id', actConv);
        input.value = '';
        input.placeholder = "Đang gửi...";
        
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
</body>
</html>