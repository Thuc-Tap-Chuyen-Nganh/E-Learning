document.addEventListener("DOMContentLoaded", function() {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");
  const forgotForm = document.getElementById("forgotForm");
  const resetForm = document.getElementById("resetForm");

  if (registerForm) {
    registerForm.addEventListener("submit", function(e) {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;
      const confirm = document.getElementById("confirm").value;
      const errorMsg = document.getElementById("errorMsg");

      errorMsg.textContent = "";

      // Kiểm tra trống
      if (!name || !email || !password || !confirm) {
        e.preventDefault(); // Ngăn gửi form
        errorMsg.textContent = "Vui lòng điền đầy đủ thông tin!";
        return;
      }

      // Kiểm tra định dạng email
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/;
      if (!emailRegex.test(email)) {
        e.preventDefault(); 
        errorMsg.textContent = "Email không hợp lệ! Vui lòng nhập đúng định dạng.";
        return;
      }

      // Kiểm tra độ dài mật khẩu
      if (password.length < 6) {
        e.preventDefault(); 
        errorMsg.textContent = "Mật khẩu phải có ít nhất 6 ký tự.";
        return;
      }

      // Kiểm tra nhập lại mật khẩu có đúng với mật khẩu không
      if (password != confirm) {
        e.preventDefault(); 
        errorMsg.textContent = "Mật khẩu nhập lại không khớp.";
        return;
      }

      // Nếu không có lỗi, form sẽ tự động được submit
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;
      const errorMsg = document.getElementById("errorMsg");

      errorMsg.textContent = "";

      if (!email || !password) {
        e.preventDefault();
        errorMsg.textContent = "Vui lòng điền đầy đủ thông tin!";
        return;
      }

      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        errorMsg.textContent = "Email không hợp lệ! Vui lòng nhập đúng định dạng.";
        return;
      }
    });
  }

  if(forgotForm) {
    forgotForm.addEventListener("submit", function(e){
      const email = document.getElementById("email").value.trim();
      const errorMsg = document.getElementById("errorMsg");

      errorMsg.textContent="";

      if(!email){
        e.preventDefault();
        errorMsg.style.color="red";
        errorMsg.textContent = "Vui lòng nhập email";
        return;
      }
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        errorMsg.style.color="red";
        errorMsg.textContent = "Email không hợp lệ! Vui lòng nhập đúng định dạng.";
        return;
      }
    })
  }

  if (resetForm) {
    resetForm.addEventListener("submit", function(e) {
      const password = document.getElementById("password").value;
      const confirm = document.getElementById("confirm").value;
      const errorMsg = document.getElementById("errorMsg");

      errorMsg.textContent = "";

      if (!password || !confirm) {
        e.preventDefault();
        errorMsg.textContent = "Vui lòng nhập đầy đủ.";
        return;
      }
      if (password.length < 6) {
        e.preventDefault();
        errorMsg.textContent = "Mật khẩu phải có ít nhất 6 ký tự.";
        return;
      }
      if (password !== confirm) {
        e.preventDefault();
        errorMsg.textContent = "Mật khẩu nhập lại không khớp.";
        return;
      }
    });
  }
});