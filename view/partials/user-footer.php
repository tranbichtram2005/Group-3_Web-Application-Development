<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
    require_once __DIR__ . '/admin-footer.php';
    return; 
}
?>

<footer class="text-center py-4 text-white mt-auto" style="background-color: var(--nav-color); border-top: 3px solid var(--btn-primary);">
    <div class="container">
        <h5 class="fw-bold mb-1" style="color: var(--btn-primary); letter-spacing: -0.5px; font-size: 20px;">2Life MARKETPLACE</h5>
        <p class="small text-white-50 mb-3" style="font-size: 13px;">Nền tảng trao đổi đồ cũ uy tín và tiết kiệm dành cho sinh viên.</p>
        
        <div class="d-flex justify-content-center gap-4 mb-3" style="font-size: 13.5px;">
            <a href="index.php?controller=home" class="text-white-50 text-decoration-none nav-icon-hover">Trang chủ</a>
            <a href="index.php?controller=info&action=index&tab=rules" class="text-white-50 text-decoration-none nav-icon-hover">Quy chế hoạt động</a>
            <a href="index.php?controller=info&action=index&tab=privacy" class="text-white-50 text-decoration-none nav-icon-hover">Chính sách bảo mật</a>
            <a href="index.php?controller=info&action=index&tab=contact" class="text-white-50 text-decoration-none nav-icon-hover">Liên hệ hỗ trợ</a>
        </div>

        <div class="border-top border-secondary my-3 opacity-25"></div>
        
        <p class="text-white-50 mb-0" style="font-size: 12px; opacity: 0.8;">
            © 2026 2Life. Phát triển bởi Nhóm 3 (Phát triển ứng dụng Web UEH).
        </p>
    </div>
</footer>

<button onclick="openSupportModal()" 
   class="btn shadow-lg d-flex align-items-center justify-content-center text-white border-0" 
   style="position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; background-color: #FF7A3D; border-radius: 50%; z-index: 9999; transition: transform 0.2s;" 
   onmouseover="this.style.transform='scale(1.1)'" 
   onmouseout="this.style.transform='scale(1)'" 
   title="Chat hỗ trợ">
    <i class="bi bi-headset fs-4"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="layout/script.js?v=<?= time() ?>"></script>

<?php if (isset($_SESSION['show_unauth_modal']) && $_SESSION['show_unauth_modal'] === true): ?>
    
    <?php         require_once __DIR__ . '/../Auth/unauthorized_modal.php';  ?> 
    <?php unset($_SESSION['show_unauth_modal']); ?>

<?php endif; ?>
</body>
</html>