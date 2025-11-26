document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. SETUP CKEDITOR ---
    let editorAddInstance;
    let editorEditInstance;

    if (document.querySelector('#content_text')) {
        ClassicEditor
            .create(document.querySelector('#content_text'))
            .then(editor => { editorAddInstance = editor; })
            .catch(error => { console.error(error); });
    }

    if (document.querySelector('#edit_content_text')) {
        ClassicEditor
            .create(document.querySelector('#edit_content_text'))
            .then(editor => { editorEditInstance = editor; })
            .catch(error => { console.error(error); });
    }

    // --- 2. KHAI BÁO CÁC ELEMENT ---
    const addModal = document.getElementById('addLessonModal');
    const editModal = document.getElementById('editLessonModal');
    const deleteModal = document.getElementById('deleteLessonModal'); // Modal Xóa

    const openAddBtn = document.getElementById('addLessonBtn');
    const closeAddBtn = document.getElementById('closeModalBtn');
    const closeEditBtn = document.getElementById('closeEditLessonBtn');
    
    // Elements cho Xóa
    const confirmDeleteBtn = document.getElementById('confirmDeleteLessonBtn');
    const cancelDeleteBtn = document.getElementById('cancelDeleteLessonBtn');
    const deleteLessonText = document.getElementById('deleteLessonText');
    let lessonIdToDelete = null;
    let lessonElementToRemove = null;

    // --- 3. LOGIC MỞ MODAL THÊM ---
    if(openAddBtn) {
        openAddBtn.addEventListener('click', () => addModal.classList.add('show'));
    }
    if(closeAddBtn) {
        closeAddBtn.addEventListener('click', () => addModal.classList.remove('show'));
    }

    // Logic ẩn/hiện form Thêm
    const typeSelectAdd = document.getElementById('lesson_type');
    if (typeSelectAdd) {
        typeSelectAdd.addEventListener('change', function() {
            const value = this.value;
            document.getElementById('group_video_url').style.display = (value === 'video') ? 'block' : 'none';
            document.getElementById('group_content_text').style.display = (value === 'text') ? 'block' : 'none';
            document.getElementById('group_quiz_info').style.display = (value === 'quiz') ? 'block' : 'none';
            
            const duration = document.getElementById('duration');
            const url = document.getElementById('content_url');
            if(value === 'video') {
                duration.required = true; url.required = true;
            } else {
                duration.required = false; url.required = false;
            }
        });
    }

    // --- 4. LOGIC MỞ MODAL SỬA ---
    const editBtns = document.querySelectorAll('.edit-lesson-btn');
    // Các input form sửa
    const editId = document.getElementById('edit_lesson_id');
    const editTitle = document.getElementById('edit_lesson_title');
    const editType = document.getElementById('edit_lesson_type');
    const editVideo = document.getElementById('edit_content_url');
    const editDuration = document.getElementById('edit_duration');

    function toggleEditFields() {
        const value = editType.value;
        document.getElementById('edit_group_video').style.display = (value === 'video') ? 'block' : 'none';
        document.getElementById('edit_group_text').style.display = (value === 'text') ? 'block' : 'none';
        document.getElementById('edit_group_quiz').style.display = (value === 'quiz') ? 'block' : 'none';
    }

    if(editType) {
        editType.addEventListener('change', toggleEditFields);
    }

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const type = this.dataset.type;
            const video = this.dataset.video;
            const duration = this.dataset.duration;
            
            const contentDiv = document.getElementById('lesson-content-data-' + id);
            const contentRaw = contentDiv ? contentDiv.innerHTML.trim() : '';

            editId.value = id;
            editTitle.value = title;
            editType.value = type;
            editVideo.value = video;
            editDuration.value = duration;

            if (editorEditInstance) {
                editorEditInstance.setData(contentRaw);
            }
            toggleEditFields();
            editModal.classList.add('show');
        });
    });

    if(closeEditBtn) {
        closeEditBtn.addEventListener('click', () => editModal.classList.remove('show'));
    }

    // --- 5. LOGIC MỞ MODAL XÓA ---
    const deleteLessonBtns = document.querySelectorAll('.delete-lesson-btn');
    
    deleteLessonBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Debug: Kiểm tra xem click có chạy vào đây không
            console.log("Click nút xóa", this.dataset.id);

            lessonIdToDelete = this.dataset.id;
            const title = this.dataset.title;
            lessonElementToRemove = this.closest('.lesson-item');

            if(deleteLessonText) {
                deleteLessonText.innerHTML = `Bạn có chắc muốn xóa bài học: <strong>${title}</strong>?`;
            }
            
            if(deleteModal) {
                deleteModal.classList.add('show');
            } else {
                console.error("Không tìm thấy modal xóa!");
            }
        });
    });

    if(cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.remove('show');
            lessonIdToDelete = null;
        });
    }

    if(confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if(!lessonIdToDelete) return;

            const originalText = confirmDeleteBtn.textContent;
            confirmDeleteBtn.textContent = "Đang xóa...";
            confirmDeleteBtn.disabled = true;

            const formData = new FormData();
            formData.append('lesson_id', lessonIdToDelete);

            fetch('../src/admin_handlers/delete_lesson_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    if(lessonElementToRemove) lessonElementToRemove.remove();
                    deleteModal.classList.remove('show');
                } else {
                    alert(data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi kết nối server.');
            })
            .finally(() => {
                confirmDeleteBtn.textContent = originalText;
                confirmDeleteBtn.disabled = false;
            });
        });
    }

    // --- 6. XỬ LÝ ĐÓNG MODAL KHI CLICK RA NGOÀI (GỘP CHUNG) ---
    window.addEventListener('click', (e) => {
        if (e.target == addModal) addModal.classList.remove('show');
        if (e.target == editModal) editModal.classList.remove('show');
        if (e.target == deleteModal) deleteModal.classList.remove('show');
    });

});