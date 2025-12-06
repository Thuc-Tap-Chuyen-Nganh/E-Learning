document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('aiToggleBtn');
    const chatBox = document.getElementById('aiChatBox');
    const closeBtn = document.getElementById('aiCloseBtn');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('chatSendBtn');
    const chatBody = document.getElementById('chatBody');

    // Khởi tạo lịch sử từ sessionStorage (nếu có)
    let chatHistory = JSON.parse(sessionStorage.getItem('eduChatHistory')) || [];

    // --- HÀM 1: KHỞI TẠO & RENDER LỊCH SỬ CŨ ---
    function initChat() {
        // Nếu có lịch sử, xóa nội dung mặc định và render lại
        if (chatHistory.length > 0) {
            // Giữ lại tin nhắn chào mừng đầu tiên (nếu muốn), hoặc xóa sạch
            // Ở đây ta xóa các tin nhắn cũ trừ msg chào mừng mặc định nếu muốn
            // Nhưng để đơn giản, ta chỉ append thêm vào dưới
            
            chatHistory.forEach(item => {
                if (item.type === 'text') {
                    renderMessage(item.content, item.isUser);
                } else if (item.type === 'card') {
                    renderCourseCard(item.content);
                }
            });
            // Cuộn xuống cuối
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }

    // --- HÀM 2: LƯU LỊCH SỬ ---
    function saveHistory(type, content, isUser = false) {
        chatHistory.push({ type, content, isUser });
        sessionStorage.setItem('eduChatHistory', JSON.stringify(chatHistory));
    }

    // --- HÀM 3: TOGGLE CHAT ---
    function toggleChat() {
        chatBox.classList.toggle('active');
        const dot = toggleBtn.querySelector('.notification-dot');
        if (dot) dot.style.display = 'none';
    }

    if(toggleBtn) toggleBtn.addEventListener('click', toggleChat);
    if(closeBtn) closeBtn.addEventListener('click', toggleChat);

    // --- HÀM 4: FORMAT TIN NHẮN (MARKDOWN) ---
    function formatMessage(text) {
        let html = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    // --- HÀM 5: VẼ TIN NHẮN (RENDER UI) ---
    function renderMessage(text, isUser = true) {
        const row = document.createElement('div');
        row.className = `msg-row ${isUser ? 'user-msg' : 'bot-msg'}`;
        const formattedText = isUser ? text : formatMessage(text);
        let contentHtml = `<div class="msg-bubble">${formattedText}</div>`;
        row.innerHTML = contentHtml;
        chatBody.appendChild(row);
    }

    // Wrapper để vừa vẽ vừa lưu
    function appendMessage(text, isUser = true) {
        renderMessage(text, isUser);
        saveHistory('text', text, isUser);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // --- HÀM 6: VẼ CARD KHÓA HỌC ---
    function renderCourseCard(course) {
        const row = document.createElement('div');
        row.className = 'msg-row bot-msg';
        const detailLink = BASE_URL + 'course_detail.php?id=' + course.course_id;
        
        // Đảm bảo đường dẫn ảnh đúng (nếu load lại trang ở thư mục con)
        // Vì trong DB lưu 'assets/uploads...', cần thêm BASE_URL nếu chưa có
        let imgUrl = course.image_url;
        if (!imgUrl.startsWith('http')) {
             // Nếu ảnh trong DB là đường dẫn tương đối, hàm PHP đã xử lý rồi
             // Nhưng để chắc chắn khi lưu vào storage, ta giữ nguyên URL đã xử lý
        }

        const html = `
            <a href="${detailLink}" class="mini-course-card">
                <div class="mini-thumb">
                    <img src="${course.image_url}" alt="Course">
                </div>
                <div class="mini-info">
                    <div class="mini-title">${course.title}</div>
                    <div class="mini-price">
                        ${course.price_fmt} <i class="fa-solid fa-arrow-right" style="font-size:10px; margin-left:5px;"></i>
                    </div>
                </div>
            </a>
        `;
        row.innerHTML = html;
        chatBody.appendChild(row);
    }

    // Wrapper để vừa vẽ vừa lưu card
    function appendCourseCard(course) {
        renderCourseCard(course);
        saveHistory('card', course, false);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // --- XỬ LÝ GỬI TIN ---
    async function handleSend() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage(text, true); // Lưu & hiện tin user
        chatInput.value = '';

        // Loading (Không lưu loading vào lịch sử)
        const loadingId = 'loading-' + Date.now();
        const loadingRow = document.createElement('div');
        loadingRow.className = 'msg-row bot-msg';
        loadingRow.id = loadingId;
        loadingRow.innerHTML = `<div class="msg-bubble"><i class="fa-solid fa-ellipsis fa-fade"></i></div>`;
        chatBody.appendChild(loadingRow);
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            const response = await fetch(BASE_URL + 'logic/ai/chat.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            document.getElementById(loadingId).remove();

            // Lưu & hiện trả lời
            appendMessage(data.reply, false);

            if (data.course) {
                appendCourseCard(data.course);
            }

        } catch (error) {
            document.getElementById(loadingId).remove();
            // Không lưu tin nhắn lỗi vào lịch sử để tránh rác
            const row = document.createElement('div');
            row.className = 'msg-row bot-msg';
            row.innerHTML = `<div class="msg-bubble">Lỗi kết nối.</div>`;
            chatBody.appendChild(row);
        }
    }

    if(sendBtn) sendBtn.addEventListener('click', handleSend);
    if(chatInput) chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleSend();
    });

    window.sendDemoMsg = function(msg) {
        chatInput.value = msg;
        handleSend();
    };

    // --- CHẠY KHỞI TẠO ---
    initChat();
});