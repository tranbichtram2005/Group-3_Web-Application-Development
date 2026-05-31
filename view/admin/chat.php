<?php require_once __DIR__ . '/../partials/admin-header.php'; ?>

<style>
    /* =========================================
       CSS BẢO ĐẢM GIAO DIỆN ADMIN CHUẨN 100%
       ========================================= */
   .chat-layout { height: calc(100vh - 120px); min-height: 500px; border: 1px solid #dee2e6; border-radius: 12px; overflow: hidden; background: #fff; margin-top: 15px; }
.chat-sidebar { background: #f8f9fa; display: flex; flex-direction: column; height: 100%; }
.chat-body { background: #fff; display: flex; flex-direction: column; height: 100%; overflow: hidden; }    .chat-messages { 
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
        .chat-sidebar.mobile-hide { display: none !important; }
        .chat-body.mobile-hide { display: none !important; }
        .btn-back-mobile { display: block !important; }
        .ticket-zone { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
</style>

<div class="container-fluid px-4 mb-0">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
        <h4 class="fw-bold text-dark"><i class="bi bi-headset text-primary"></i> 2Life Helpdesk</h4>
    </div>

    <div class="row g-0 chat-layout shadow">
        
        <div class="col-12 col-md-4 chat-sidebar border-end" id="admin-sidebar">
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

        <div class="col-12 col-md-8 chat-body position-relative mobile-hide" id="admin-chatbox">
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
    // Trạm trung chuyển ID từ PHP sang file script.js
    window.CHAT_ADMIN_ID = <?= $_SESSION['user_id'] ?? 0 ?>;
</script>

<?php require_once __DIR__ . '/../partials/admin-footer.php'; ?>