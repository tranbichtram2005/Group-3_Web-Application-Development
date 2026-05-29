<?php
/**
 * Component: Hộp thoại cảnh báo chưa phân quyền
 */

$title = $modalTitle ?? "Tính năng này bị hạn chế!";
$desc = $modalDesc ?? "Tài khoản hiện tại của bạn không có quyền truy cập vào khu vực này. Bạn có muốn đăng nhập bằng tài khoản khác không?";
$btnHomeText = "Về trang chủ";
$btnHomeUrl = "index.php?controller=home";
$btnLoginText = "Đến trang đăng nhập";
$btnLoginUrl = "index.php?controller=auth&action=login";
?>

<div class="modal fade" id="unauthorizedAccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4 px-4">
                <div class="mb-3">
                    <i class="bi bi-shield-lock text-warning" style="font-size: 4rem; display: inline-block;"></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($title); ?></h4>
                <p class="text-secondary px-2" style="font-size: 0.95rem; line-height: 1.6;">
                    <?php echo htmlspecialchars($desc); ?>
                </p>
                
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <a href="<?php echo $btnHomeUrl; ?>" class="btn btn-light rounded-pill px-4 fw-semibold text-dark">
                        <?php echo htmlspecialchars($btnHomeText); ?>
                    </a>
                    <a href="<?php echo $btnLoginUrl; ?>" class="btn text-white rounded-pill px-4 fw-semibold shadow-sm" style="background-color: #FF7A3D; border: none;">
                        <?php echo htmlspecialchars($btnLoginText); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var element = document.getElementById('unauthorizedAccessModal');
        if (element) {
            var myModal = new bootstrap.Modal(element);
            myModal.show();
        }
    });
</script>