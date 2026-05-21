document.addEventListener("DOMContentLoaded", function() {
    
    // XỬ LÝ DROPDOWN MENU TRÊN MOBILE
    const dropdowns = document.querySelectorAll('.nav-dropdown');

    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            // Chỉ can thiệp khi ở màn hình nhỏ (Tablet/Mobile)
            if (window.innerWidth < 992) {
                if(e.target.closest('.nav-link-text') && e.target.closest('.nav-link-text').getAttribute('href') === '#') {
                    e.preventDefault(); 
                }

                const menu = this.querySelector('.nav-dropdown-menu');
                if (menu) {
                    const isShowing = menu.style.display === 'block';
                    document.querySelectorAll('.nav-dropdown-menu').forEach(m => m.style.display = '');
                    if (!isShowing) menu.style.display = 'block';
                }
            }
        });
    });

    // Đóng Dropdown khi bấm ra ngoài
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown-menu').forEach(m => m.style.display = '');
        }
    });

});