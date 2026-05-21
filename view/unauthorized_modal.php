<?php
/**
 * Component: Hộp thoại cảnh báo chưa phân quyền
 */

// Đã sửa lại toàn bộ biến thành camelCase theo Rule
$title = $modalTitle ?? "Tính năng này bị hạn chế!";
$desc = $modalDesc ?? "Bạn không có quyền truy cập vào khu vực này. Vui lòng kiểm tra lại tài khoản hoặc liên hệ quản trị viên.";
$btnText = $modalBtnText ?? "Quay lại trang chủ";
$redirectUrl = $modalRedirectUrl ?? "index.php?controller=home";
?>

<div class="modal fade" id="unauthorizedAccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" onclick="history.back()" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" onclick="history.back()">Quay lại</button>
                    <a href="<?php echo $redirectUrl; ?>" class="btn btn-success rounded-pill px-4 fw-semibold" style="background-color: #28a745; border: none;">
                        <?php echo htmlspecialchars($btnText); ?>
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