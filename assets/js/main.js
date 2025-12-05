// Lấy các phần tử (element) từ DOM
const hamburgerBtn = document.getElementById('hamburger-btn');
const mobileNavContainer = document.getElementById('mobile-nav-container');
const closeBtn = document.getElementById('mobile-nav-close-btn');
const overlay = document.getElementById('mobile-nav-overlay');

// Hàm để mở menu
function openMenu() {
    mobileNavContainer.classList.add('is-active');
    document.body.classList.add('no-scroll'); // Ngăn cuộn trang nền
}

// Hàm để đóng menu
function closeMenu() {
    mobileNavContainer.classList.remove('is-active');
    document.body.classList.remove('no-scroll'); // Cho phép cuộn lại
}

// Thêm sự kiện click
hamburgerBtn.addEventListener('click', openMenu);
closeBtn.addEventListener('click', closeMenu);
overlay.addEventListener('click', closeMenu);