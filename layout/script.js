document.addEventListener("DOMContentLoaded", function () {

    // ================================================================
    // DROPDOWN MENU — dùng class .open thay vì inline style
    // Fix lỗi mất hover khi di chuột từ button xuống menu
    // ================================================================
    const dropdowns = document.querySelectorAll('.nav-dropdown');

    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.nav-avatar-btn');
        if (!btn) return;

        // Desktop: CSS hover đã xử lý, JS chỉ lo mobile
        btn.addEventListener('click', function (e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                // Đóng tất cả dropdown khác
                dropdowns.forEach(d => { if (d !== dropdown) d.classList.remove('open'); });
                dropdown.classList.toggle('open');
            }
        });
    });

    // Đóng khi bấm ra ngoài (mobile)
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            dropdowns.forEach(d => d.classList.remove('open'));
        }
    });

});