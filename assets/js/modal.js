/**
 * ============================================
 * MODAL MANAGEMENT MODULE
 * ============================================
 * 
 * Tập hợp tất cả logic xử lý modal (delete, confirm, etc.)
 * Được tái sử dụng trong nhiều trang quản lý
 */

class ModalManager {
    constructor(options = {}) {
        this.modalSelector = options.modalSelector || '#deleteModal';
        this.confirmBtnSelector = options.confirmBtnSelector || '#modalConfirmBtn';
        this.cancelBtnSelector = options.cancelBtnSelector || '#modalCancelBtn';
        this.deleteButtonSelector = options.deleteButtonSelector || '.delete-btn';
        this.apiEndpoint = options.apiEndpoint || '';
        this.idDataAttribute = options.idDataAttribute || 'id';
        this.titleDataAttribute = options.titleDataAttribute || 'title';

        this.modal = document.querySelector(this.modalSelector);
        this.confirmBtn = document.querySelector(this.confirmBtnSelector);
        this.cancelBtn = document.querySelector(this.cancelBtnSelector);

        if (!this.modal) return;

        this.itemIdToDelete = null;
        this.itemRowToDelete = null;
        this.init();
    }

    /**
     * Khởi tạo các event listener
     */
    init() {
        // Thêm sự kiện cho tất cả delete buttons
        this.attachDeleteButtonListeners();

        // Event listener cho cancel button
        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.close());
        }

        // Event listener cho modal overlay (click outside để đóng)
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.close();
                }
            });
        }

        // Event listener cho confirm button
        if (this.confirmBtn) {
            this.confirmBtn.addEventListener('click', () => this.confirm());
        }
    }

    /**
     * Gắn sự kiện click cho tất cả delete buttons
     */
    attachDeleteButtonListeners() {
        document.querySelectorAll(this.deleteButtonSelector).forEach(button => {
            button.addEventListener('click', () => this.openForItem(button));
        });
    }

    /**
     * Mở modal cho một item
     */
    openForItem(button) {
        this.itemIdToDelete = button.dataset[this.idDataAttribute];
        const itemTitle = button.dataset[this.titleDataAttribute];
        this.itemRowToDelete = button.closest('tr');

        const modalText = this.modal.querySelector('p');
        if (modalText) {
            modalText.innerHTML = `Bạn có chắc chắn muốn xóa <br>"<strong>${itemTitle}</strong>"?<br>Hành động này không thể hoàn tác.`;
        }

        this.open();
    }

    /**
     * Mở modal
     */
    open() {
        if (this.modal) {
            this.modal.classList.add('show');
        }
    }

    /**
     * Đóng modal
     */
    close() {
        if (this.modal) {
            this.modal.classList.remove('show');
        }
        this.itemIdToDelete = null;
        this.itemRowToDelete = null;
        this.resetConfirmButton();
    }

    /**
     * Reset confirm button
     */
    resetConfirmButton() {
        if (this.confirmBtn) {
            this.confirmBtn.textContent = 'Xóa';
            this.confirmBtn.disabled = false;
        }
    }

    /**
     * Xác nhận xóa (gọi API)
     */
    async confirm() {
        if (!this.itemIdToDelete || !this.apiEndpoint) {
            console.error('Item ID or API endpoint not set');
            return;
        }

        if (this.confirmBtn) {
            this.confirmBtn.textContent = 'Đang xóa...';
            this.confirmBtn.disabled = true;
        }

        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${encodeURIComponent(this.itemIdToDelete)}`
            });

            const data = await response.json();

            if (data.success) {
                // Xóa hàng từ bảng
                if (this.itemRowToDelete) {
                    this.itemRowToDelete.remove();
                }
                this.close();
            } else {
                alert(data.message || 'Không thể xóa item. Vui lòng thử lại.');
                this.close();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi kết nối. Vui lòng kiểm tra console.');
            this.close();
        }
    }

    /**
     * Reinitialize - gọi lại nếu DOM thay đổi (dùng cho infinite scroll, etc.)
     */
    reinitialize() {
        this.attachDeleteButtonListeners();
    }
}

// Global instance cho delete modal (default configuration)
let deleteModal = null;

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo delete modal nếu tồn tại
    if (document.querySelector('#deleteModal')) {
        deleteModal = new ModalManager({
            modalSelector: '#deleteModal',
            confirmBtnSelector: '#modalConfirmBtn',
            cancelBtnSelector: '#modalCancelBtn',
            deleteButtonSelector: '.delete-btn',
            apiEndpoint: '../logic/admin/course_delete.php',
            idDataAttribute: 'id',
            titleDataAttribute: 'title'
        });
    }
});
