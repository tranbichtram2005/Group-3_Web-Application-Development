<?php 
require_once __DIR__ . '/user-header.php'; 

// Xử lý hiển thị Avatar: Lấy từ DB, nếu trống thì dùng ảnh mặc định tạo từ tên
$avatarDisplay = !empty($user['avatar_url']) ? $user['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($user['username'] ?? 'U') . "&background=FF7A3D&color=fff&rounded=true&bold=true&size=128";
?>

<main class="container py-5" style="max-width: 1000px; min-height: 70vh;">
    <div class="row g-4">
        
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-3 bg-white border">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="<?= htmlspecialchars((string)$avatarDisplay) ?>" 
                         class="rounded-circle shadow-sm border border-2 border-white object-fit-cover" 
                         width="100" height="100" alt="Avatar" id="previewAvatar">
                         
                    <label for="avatarUpload" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transform: translate(10%, 10%);">
                        <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                    </label>
                </div>
                <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars((string)($user['full_name'] ?? 'Thành viên')) ?></h5>
                <p class="text-secondary small mb-3">@<?= htmlspecialchars((string)($user['username'] ?? 'username')) ?></p>
                <span class="badge bg-light text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 11px;">
                    <i class="bi bi-patch-check-fill me-1"></i>
<?= (isset($user['role_id']) && $user['role_id'] == 2) ? 'Quản trị viên' : ((isset($user['is_verified_seller']) && $user['is_verified_seller'] == 1) ? 'Người bán uy tín' : 'Thành viên 2Life') ?>                </span>
            </div>

            <div class="nav flex-column nav-pills shadow-sm p-2 bg-white rounded-4 border" role="tablist">
                <button class="nav-link active text-start py-2.5 px-3 mb-1 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-info" type="button">
                    <i class="bi bi-person-vcard fs-5"></i> Thông tin cá nhân
                </button>
                <button class="nav-link text-start py-2.5 px-3 mb-1 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-pass" type="button">
                    <i class="bi bi-shield-lock fs-5"></i> Đổi mật khẩu
                </button>
                <?php if (isset($user) && is_array($user) && $user['role_id'] == 1 && $user['is_verified_seller'] == 0): ?>
                <button class="nav-link text-start py-2.5 px-3 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-seller" type="button">
                    <i class="bi bi-shop fs-5"></i> Đăng ký bán hàng
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="tab-content bg-white p-4 p-md-5 shadow-sm rounded-4 border h-100">
                
                <div class="tab-pane fade show active" id="tab-info">
                    <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                        <h4 class="fw-bold mb-0 text-dark">Hồ sơ cá nhân</h4>
                    </div>
                    
                    <input type="file" id="avatarUpload" class="d-none" accept="image/*" onchange="previewImage(event)">

                    <form id="form-profile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Họ và tên</label>
                                <input type="text" class="form-control form-control-lg border-2 shadow-none rounded-3" id="fullName" value="<?= htmlspecialchars((string)($user['full_name'] ?? '')) ?>" required style="font-size: 15px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Số điện thoại</label>
                                <input type="text" class="form-control form-control-lg border-2 shadow-none rounded-3" id="phone" value="<?= htmlspecialchars((string)($user['phone'] ?? '')) ?>" style="font-size: 15px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Địa chỉ Email <i class="bi bi-lock-fill text-muted ms-1" title="Không thể thay đổi"></i></label>
                                <input type="email" class="form-control form-control-lg border-2 shadow-none rounded-3 bg-light text-muted" value="<?= htmlspecialchars((string)($user['email'] ?? '')) ?>" readonly style="font-size: 15px; cursor: not-allowed;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Tiểu sử (Bio)</label>
                                <textarea class="form-control border-2 shadow-none rounded-3 py-2" id="bio" rows="4" placeholder="Viết một vài điều giới thiệu về bản thân bạn..." style="font-size: 15px; resize: none;"><?= htmlspecialchars((string)($user['bio'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-2life-primary px-4 py-2.5 mt-4 rounded-3 fw-bold shadow-sm" onclick="saveProfile()">
                            <i class="bi bi-check2-circle me-1"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab-pass">
                    <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom"><h4 class="fw-bold mb-0 text-dark">Bảo mật tài khoản</h4></div>
                    <form id="form-password">
                        <div class="mb-3"><label class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu hiện tại</label><input type="password" class="form-control form-control-lg border-2 shadow-none rounded-3" id="oldPass" required style="font-size: 15px;"></div>
                        <div class="mb-3"><label class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu mới</label><input type="password" class="form-control form-control-lg border-2 shadow-none rounded-3" id="newPass" required style="font-size: 15px;"></div>
                        <div class="mb-4"><label class="form-label fw-bold text-secondary small text-uppercase">Xác nhận mật khẩu mới</label><input type="password" class="form-control form-control-lg border-2 shadow-none rounded-3" id="confirmPass" required style="font-size: 15px;"></div>
                        <button type="button" class="btn btn-2life-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" onclick="changePassword()"><i class="bi bi-key me-1"></i> Cập nhật mật khẩu</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="tab-seller">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom"><h4 class="fw-bold mb-0 text-dark">Đăng ký mở gian hàng</h4></div>
                    <div class="alert alert-warning border-0 rounded-3 small d-flex align-items-center gap-2 mb-4"><i class="bi bi-info-circle-fill fs-5"></i><span>Sau khi gửi yêu cầu, Ban quản trị sẽ duyệt trong 24h.</span></div>
                    <form id="form-seller">
                        <div class="mb-3"><label class="form-label fw-bold text-secondary small text-uppercase">Tên Shop của bạn <span class="text-danger">*</span></label><input type="text" class="form-control form-control-lg border-2 shadow-none rounded-3" id="shopName" required style="font-size: 15px;"></div>
                        <div class="mb-4"><label class="form-label fw-bold text-secondary small text-uppercase">Mô tả định hướng kinh doanh</label><textarea class="form-control border-2 shadow-none rounded-3 py-2" id="shopDesc" rows="4" style="font-size: 15px; resize: none;"></textarea></div>
                        <button type="button" class="btn btn-2life-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" onclick="registerSeller()"><i class="bi bi-send me-1"></i> Gửi yêu cầu duyệt</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
// Hàm báo lỗi chung cho form validation
function showError(msg) {
    Swal.fire({ icon: 'error', title: 'Úi, có lỗi nè!', text: msg, confirmButtonColor: '#FF7A3D' });
}

// 1. Gửi Form Cập nhật hồ sơ
async function saveProfile() {
    const formData = new FormData();
    formData.append('full_name', document.getElementById('fullName').value);
    formData.append('phone', document.getElementById('phone').value);
    formData.append('bio', document.getElementById('bio').value);
    
    const avatarFile = document.getElementById('avatarUpload').files[0];
    if (avatarFile) formData.append('avatar', avatarFile);

    try {
        const res = await fetch('index.php?controller=profile&action=updateAjax', { method: 'POST', body: formData });
        const result = await res.json();
        
        // Gọi bảng thông báo đẹp
        Swal.fire({
            icon: result.status, // success hoặc error
            title: result.status === 'success' ? 'Thành công!' : 'Thất bại!',
            text: result.msg,
            confirmButtonColor: '#FF7A3D'
        }).then((resAlert) => {
            if(result.status === 'success' && resAlert.isConfirmed) window.location.reload(); 
        });
    } catch (error) {
        showError("Có lỗi kết nối hệ thống!");
    }
}

// 2. Gửi Form Đổi mật khẩu
async function changePassword() {
    const oldP = document.getElementById('oldPass').value;
    const newP = document.getElementById('newPass').value;
    const confP = document.getElementById('confirmPass').value;

    if (newP !== confP) return showError("Mật khẩu xác nhận không khớp!");
    if (newP.length < 6) return showError("Mật khẩu phải từ 6 ký tự trở lên!");

    try {
        const res = await fetch('index.php?controller=profile&action=changePasswordAjax', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify({ old_password: oldP, new_password: newP, confirm_password: confP })
        });
        const result = await res.json();
        
        Swal.fire({ icon: result.status, title: 'Thông báo', text: result.msg, confirmButtonColor: '#FF7A3D' });
        if(result.status === 'success') document.getElementById('form-password').reset();
    } catch (error) {
        showError("Có lỗi kết nối hệ thống!");
    }
}

// 3. Gửi Form Đăng ký Shop
async function registerSeller() {
    const shopName = document.getElementById('shopName').value;
    if(shopName.trim() === '') return showError("Vui lòng nhập tên Shop!");

    const data = { shop_name: shopName, description: document.getElementById('shopDesc').value };
    try {
        const res = await fetch('index.php?controller=profile&action=registerSellerAjax', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
        });
        const result = await res.json();
        
        Swal.fire({ icon: result.status, title: 'Đăng ký bán hàng', text: result.msg, confirmButtonColor: '#FF7A3D' });
        if(result.status === 'success') document.getElementById('form-seller').reset();
    } catch (error) {
        showError("Có lỗi kết nối hệ thống!");
    }
}

// Preview ảnh
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() { document.getElementById('previewAvatar').src = reader.result; }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<?php require_once __DIR__ . '/user-footer.php'; ?>