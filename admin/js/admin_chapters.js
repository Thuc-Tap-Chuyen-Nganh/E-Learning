document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addChapterModal');
    const openBtn = document.getElementById('addChapterBtn'); 
    const closeBtn = document.getElementById('closeModalBtn');

    // Mở Modal Thêm
    if(openBtn) {
        openBtn.addEventListener('click', function() {
            modal.classList.add('show');
        });
    }

    // Đóng Modal Thêm (Nút Hủy)
    if(closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.classList.remove('show');
        });
    }

    // Đóng Modal khi click ra ngoài
    window.addEventListener('click', function(e) {
        if (e.target == modal) {
            modal.classList.remove('show');
        }
    });

    // -------------------------------------------
    // XỬ LÝ XÓA CHƯƠNG (AJAX)
    // -------------------------------------------

    const deleteModal = document.getElementById('deleteChapterModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const modalText = document.getElementById('deleteModalText');
    
    let chapterIdToDelete = null;
    let chapterElementToRemove = null;

    // 1. Bắt sự kiện click vào các nút thùng rác
    const deleteButtons = document.querySelectorAll('.delete-chapter-btn');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            chapterIdToDelete = this.dataset.id;
            const title = this.dataset.title;
            
            // Lưu lại cái thẻ div .chapter-item cha để lát nữa xóa
            chapterElementToRemove = this.closest('.chapter-item');

            // Cập nhật text và hiện modal
            modalText.innerHTML = `Bạn có chắc muốn xóa chương: <strong>${title}</strong>?`;
            deleteModal.classList.add('show');
        });
    });

    // 2. Xử lý nút Hủy Xóa
    if(cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            deleteModal.classList.remove('show');
            chapterIdToDelete = null;
        });
    }

    // 3. Xử lý nút Xác nhận Xóa (AJAX)
    if(confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if(!chapterIdToDelete) return;

            // Hiệu ứng nút đang bấm
            const originalText = confirmBtn.textContent;
            confirmBtn.textContent = 'Đang xóa...';
            confirmBtn.disabled = true;

            // Gửi AJAX
            const formData = new FormData();
            formData.append('chapter_id', chapterIdToDelete);

            // Lưu ý: Đường dẫn này tính từ file PHP gọi nó (admin_course_details.php)
            fetch('../src/admin_handlers/delete_chapter_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Thành công: Xóa dòng đó khỏi giao diện
                    if(chapterElementToRemove) {
                        chapterElementToRemove.remove();
                    }
                    deleteModal.classList.remove('show');
                } else {
                    alert(data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối đến máy chủ.');
            })
            .finally(() => {
                // Reset nút
                confirmBtn.textContent = originalText;
                confirmBtn.disabled = false;
            });
        });
    }

    // Đóng modal xóa khi click ra ngoài
    window.addEventListener('click', function(e) {
        if (e.target == deleteModal) {
            deleteModal.classList.remove('show');
        }
    });

    // -------------------------------------------
    // XỬ LÝ SỬA CHƯƠNG (MỞ MODAL & ĐIỀN DATA)
    // -------------------------------------------
    const editModal = document.getElementById('editChapterModal');
    const closeEditBtn = document.getElementById('closeEditModalBtn');
    const editButtons = document.querySelectorAll('.edit-chapter-btn');

    // Các ô input trong modal sửa
    const editIdInput = document.getElementById('edit_chapter_id');
    const editTitleInput = document.getElementById('edit_chapter_title');
    const editDescInput = document.getElementById('edit_chapter_desc');
    const editOrderInput = document.getElementById('edit_chapter_order');

    if (editModal) {
        // 1. Bắt sự kiện click nút Sửa
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Lấy dữ liệu từ data attributes của nút
                const id = this.dataset.id;
                const title = this.dataset.title;
                const desc = this.dataset.desc;
                const order = this.dataset.order;

                // Điền vào form
                editIdInput.value = id;
                editTitleInput.value = title;
                editDescInput.value = desc;
                editOrderInput.value = order;

                // Mở modal
                editModal.classList.add('show');
            });
        });

        // 2. Đóng modal
        if (closeEditBtn) {
            closeEditBtn.addEventListener('click', function() {
                editModal.classList.remove('show');
            });
        }

        // Đóng khi click ra ngoài
        window.addEventListener('click', function(e) {
            if (e.target == editModal) {
                editModal.classList.remove('show');
            }
        });
    }

    // -------------------------------------------
    // TỰ ĐỘNG TẮT THÔNG BÁO (ALERT)
    // -------------------------------------------
    const alertBox = document.getElementById('successAlert');

    if (alertBox) {
        setTimeout(function() {
            alertBox.style.opacity = '0';
            setTimeout(function() {
                alertBox.remove();
                // Xóa query param trên URL để clean
                const url = new URL(window.location);
                url.searchParams.delete('status');
                window.history.replaceState({}, '', url);
            }, 500); 
        }, 3000);
    }
});