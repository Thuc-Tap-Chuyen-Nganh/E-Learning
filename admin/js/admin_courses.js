document.addEventListener("DOMContentLoaded", function() {
    
    const deleteModal = document.getElementById("deleteModal");
    if (!deleteModal) return;

    const modalConfirmBtn = document.getElementById("modalConfirmBtn");
    const modalCancelBtn = document.getElementById("modalCancelBtn");
    const modalText = deleteModal.querySelector("p");
    
    let courseIdToDelete = null;
    let buttonToDeleteRow = null; // Biến lưu trữ hàng (tr) cần xóa

    // 1. Lắng nghe tất cả các nút 'Xóa'
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function() {
            courseIdToDelete = this.dataset.id;
            const courseTitle = this.dataset.title;
            
            // Lưu lại hàng (tr) cha của nút này
            buttonToDeleteRow = this.closest('tr'); 
            
            modalText.innerHTML = `Bạn có chắc chắn muốn xóa khóa học <br>"<strong>${courseTitle}</strong>"?<br>Hành động này không thể hoàn tác.`;
            deleteModal.classList.add("show");
        });
    });

    // Hàm để đóng modal
    function closeModal() {
        deleteModal.classList.remove("show");
        courseIdToDelete = null;
        buttonToDeleteRow = null;
        // Reset nút
        modalConfirmBtn.textContent = "Xóa";
        modalConfirmBtn.disabled = false;
    }

    // 2. Xử lý nút "Hủy"
    modalCancelBtn.addEventListener("click", closeModal);
    deleteModal.addEventListener("click", function(e) {
        if (e.target === deleteModal) {
            closeModal();
        }
    });

    // 3. === XỬ LÝ NÚT XÁC NHẬN (AJAX) ===
    modalConfirmBtn.addEventListener("click", function() {
        if (!courseIdToDelete) return;

        // Hiển thị trạng thái "Đang tải..."
        this.textContent = "Đang xóa...";
        this.disabled = true;

        // Gửi dữ liệu bằng FormData (giống như form POST)
        const formData = new FormData();
        formData.append('id', courseIdToDelete);

        fetch('../logic/admin/course_delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Mong đợi phản hồi JSON
        .then(data => {
            if (data.success) {
                // THÀNH CÔNG!
                // 1. Xóa hàng khỏi bảng ngay lập tức
                if (buttonToDeleteRow) {
                    buttonToDeleteRow.remove();
                }
                // 2. Đóng modal
                closeModal();
            } else {
                // THẤT BẠI (từ server)
                alert(data.message || 'Không thể xóa khóa học. Vui lòng thử lại.');
                closeModal();
            }
        })
        .catch(error => {
            // Lỗi mạng hoặc lỗi code
            console.error('Error:', error);
            alert('Đã xảy ra lỗi kết nối. Vui lòng kiểm tra console.');
            closeModal();
        });
    });
});