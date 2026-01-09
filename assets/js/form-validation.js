/**
 * ============================================
 * FORM VALIDATION MODULE
 * ============================================
 * 
 * Tập hợp tất cả các hàm validate form được tái sử dụng
 * Được sử dụng trong register, login, forgot password, reset password, v.v.
 */

class FormValidator {
    constructor() {
        this.emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/;
    }

    /**
     * Kiểm tra email hợp lệ
     */
    validateEmail(email) {
        return this.emailRegex.test(email);
    }

    /**
     * Kiểm tra mật khẩu (tối thiểu 6 ký tự)
     */
    validatePassword(password) {
        return password && password.length >= 6;
    }

    /**
     * Kiểm tra 2 mật khẩu có giống nhau
     */
    validatePasswordMatch(password, confirm) {
        return password === confirm;
    }

    /**
     * Kiểm tra xem field có rỗng không
     */
    isEmpty(value) {
        return !value || value.trim() === '';
    }

    /**
     * Clear error message
     */
    clearError(errorElement) {
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.color = '';
        }
    }

    /**
     * Show error message
     */
    showError(errorElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.color = 'red';
        }
    }

    /**
     * Validate Register Form
     */
    validateRegisterForm(formData) {
        const { name, email, password, confirm } = formData;
        const errors = [];

        // Kiểm tra trống
        if (this.isEmpty(name)) {
            errors.push('Vui lòng nhập tên!');
        }
        if (this.isEmpty(email)) {
            errors.push('Vui lòng nhập email!');
        }
        if (this.isEmpty(password)) {
            errors.push('Vui lòng nhập mật khẩu!');
        }
        if (this.isEmpty(confirm)) {
            errors.push('Vui lòng nhập lại mật khẩu!');
        }

        // Kiểm tra định dạng email
        if (!this.isEmpty(email) && !this.validateEmail(email)) {
            errors.push('Email không hợp lệ! Vui lòng nhập đúng định dạng.');
        }

        // Kiểm tra độ dài mật khẩu
        if (!this.isEmpty(password) && !this.validatePassword(password)) {
            errors.push('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        // Kiểm tra nhập lại mật khẩu
        if (!this.isEmpty(password) && !this.isEmpty(confirm) && !this.validatePasswordMatch(password, confirm)) {
            errors.push('Mật khẩu nhập lại không khớp.');
        }

        return errors;
    }

    /**
     * Validate Login Form
     */
    validateLoginForm(formData) {
        const { email, password } = formData;
        const errors = [];

        if (this.isEmpty(email)) {
            errors.push('Vui lòng nhập email!');
        }
        if (this.isEmpty(password)) {
            errors.push('Vui lòng nhập mật khẩu!');
        }

        if (!this.isEmpty(email) && !this.validateEmail(email)) {
            errors.push('Email không hợp lệ! Vui lòng nhập đúng định dạng.');
        }

        return errors;
    }

    /**
     * Validate Forgot Password Form
     */
    validateForgotForm(formData) {
        const { email } = formData;
        const errors = [];

        if (this.isEmpty(email)) {
            errors.push('Vui lòng nhập email');
        }

        if (!this.isEmpty(email) && !this.validateEmail(email)) {
            errors.push('Email không hợp lệ! Vui lòng nhập đúng định dạng.');
        }

        return errors;
    }

    /**
     * Validate Reset Password Form
     */
    validateResetForm(formData) {
        const { password, confirm } = formData;
        const errors = [];

        if (this.isEmpty(password)) {
            errors.push('Vui lòng nhập mật khẩu!');
        }
        if (this.isEmpty(confirm)) {
            errors.push('Vui lòng nhập lại mật khẩu!');
        }

        if (!this.isEmpty(password) && !this.validatePassword(password)) {
            errors.push('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        if (!this.isEmpty(password) && !this.isEmpty(confirm) && !this.validatePasswordMatch(password, confirm)) {
            errors.push('Mật khẩu nhập lại không khớp.');
        }

        return errors;
    }
}

// Khởi tạo global validator
const validator = new FormValidator();

// Auto-initialize form validation khi DOM sẵn sàng
document.addEventListener("DOMContentLoaded", function() {
    const registerForm = document.getElementById("registerForm");
    const loginForm = document.getElementById("loginForm");
    const forgotForm = document.getElementById("forgotForm");
    const resetForm = document.getElementById("resetForm");

    if (registerForm) {
        registerForm.addEventListener("submit", function(e) {
            const formData = {
                name: document.getElementById("name")?.value.trim() || '',
                email: document.getElementById("email")?.value.trim() || '',
                password: document.getElementById("password")?.value || '',
                confirm: document.getElementById("confirm")?.value || ''
            };

            const errorMsg = document.getElementById("errorMsg");
            const errors = validator.validateRegisterForm(formData);

            if (errors.length > 0) {
                e.preventDefault();
                validator.showError(errorMsg, errors[0]);
            } else {
                validator.clearError(errorMsg);
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener("submit", function(e) {
            const formData = {
                email: document.getElementById("email")?.value.trim() || '',
                password: document.getElementById("password")?.value || ''
            };

            const errorMsg = document.getElementById("errorMsg");
            const errors = validator.validateLoginForm(formData);

            if (errors.length > 0) {
                e.preventDefault();
                validator.showError(errorMsg, errors[0]);
            } else {
                validator.clearError(errorMsg);
            }
        });
    }

    if (forgotForm) {
        forgotForm.addEventListener("submit", function(e) {
            const formData = {
                email: document.getElementById("email")?.value.trim() || ''
            };

            const errorMsg = document.getElementById("errorMsg");
            const errors = validator.validateForgotForm(formData);

            if (errors.length > 0) {
                e.preventDefault();
                validator.showError(errorMsg, errors[0]);
            } else {
                validator.clearError(errorMsg);
            }
        });
    }

    if (resetForm) {
        resetForm.addEventListener("submit", function(e) {
            const formData = {
                password: document.getElementById("password")?.value || '',
                confirm: document.getElementById("confirm")?.value || ''
            };

            const errorMsg = document.getElementById("errorMsg");
            const errors = validator.validateResetForm(formData);

            if (errors.length > 0) {
                e.preventDefault();
                validator.showError(errorMsg, errors[0]);
            } else {
                validator.clearError(errorMsg);
            }
        });
    }
});
