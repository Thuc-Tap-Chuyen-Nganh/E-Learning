// file: assets/js/ai_widget.js

document.addEventListener("DOMContentLoaded", function () {
    // 1. Tạo HTML cho Widget
    const widgetHTML = `
        <div id="ai-widget-container">
            <button id="ai-toggle-btn">
                <i class="fa-solid fa-robot"></i>
            </button>
            <div id="ai-chat-box" class="hidden">
                <div class="ai-header">
                    <div class="ai-info">
                        <strong>EduBot AI</strong>
                        <span>Tư vấn khóa học</span>
                    </div>
                    <button id="ai-close-btn">&times;</button>
                </div>
                <div id="ai-messages">
                    <div class="message bot">
                        Xin chào! 👋 Tôi có thể giúp bạn tìm khóa học nào hôm nay?
                    </div>
                </div>
                <div class="ai-input-area">
                    <input type="text" id="ai-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                    <button id="ai-send-btn"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', widgetHTML);

    // Kiểm tra để tránh tạo trùng nếu file js được gọi nhiều lần
    if (!document.getElementById('ai-widget-container')) {
        document.body.insertAdjacentHTML('beforeend', widgetHTML);
    }

    // 2. Định nghĩa CSS ngay trong JS (hoặc tách ra file css riêng)
    const style = document.createElement('style');
    style.innerHTML = `
        #ai-widget-container { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Segoe UI', sans-serif; }
        #ai-toggle-btn { width: 60px; height: 60px; border-radius: 50%; background: #007bff; color: white; border: none; font-size: 24px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: transform 0.3s; }
        #ai-toggle-btn:hover { transform: scale(1.1); }
        
        #ai-chat-box { position: absolute; bottom: 80px; right: 0; width: 350px; height: 500px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); display: flex; flex-direction: column; overflow: hidden; transition: all 0.3s ease; }
        #ai-chat-box.hidden { opacity: 0; visibility: hidden; transform: translateY(20px); }
        
        .ai-header { background: #007bff; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .ai-header strong { display: block; font-size: 16px; }
        .ai-header span { font-size: 12px; opacity: 0.8; }
        #ai-close-btn { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
        
        #ai-messages { flex: 1; padding: 15px; overflow-y: auto; background: #f8f9fa; display: flex; flex-direction: column; gap: 10px; }
        .message { max-width: 80%; padding: 10px 14px; border-radius: 10px; font-size: 14px; line-height: 1.4; word-wrap: break-word; }
        .message.bot { align-self: flex-start; background: #e9ecef; color: #333; border-bottom-left-radius: 2px; }
        .message.user { align-self: flex-end; background: #007bff; color: white; border-bottom-right-radius: 2px; }
        .message.loading { font-style: italic; color: #888; background: none; }

        /* Course Card Style */
        .ai-course-card { background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-top: 5px; cursor: pointer; transition: transform 0.2s; width: 220px; align-self: flex-start; }
        .ai-course-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card-img { width: 100%; height: 120px; object-fit: cover; }
        .card-body { padding: 10px; }
        .card-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-price { color: #dc3545; font-weight: bold; font-size: 13px; }
        
        .ai-input-area { padding: 10px; border-top: 1px solid #eee; display: flex; gap: 10px; background: white; }
        #ai-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none; }
        #ai-send-btn { background: none; border: none; color: #007bff; font-size: 18px; cursor: pointer; }
    `;
    document.head.appendChild(style);

    // 3. Logic JS
    const chatBox = document.getElementById('ai-chat-box');
    const toggleBtn = document.getElementById('ai-toggle-btn');
    const closeBtn = document.getElementById('ai-close-btn');
    const sendBtn = document.getElementById('ai-send-btn');
    const input = document.getElementById('ai-input');
    const msgContainer = document.getElementById('ai-messages');

    let history = []; // Lưu lịch sử ngắn gọn cho phiên chat

    // Toggle Chat
    toggleBtn.onclick = () => chatBox.classList.remove('hidden');
    closeBtn.onclick = () => chatBox.classList.add('hidden');

    function appendMessage(text, sender) {
        const div = document.createElement('div');
        div.className = `message ${sender}`;
        div.innerHTML = text;
        msgContainer.appendChild(div);
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    function appendCard(cardData) {
        const cardHTML = `
            <div class="ai-course-card" onclick="window.location.href='${cardData.link}'">
                <img src="${cardData.image}" class="card-img" onerror="this.src='assets/images/main_icon.png'">
                <div class="card-body">
                    <div class="card-title">${cardData.title}</div>
                    <div class="card-price">${cardData.price}</div>
                </div>
            </div>
        `;
        // Card luôn là của bot
        const div = document.createElement('div');
        div.style.alignSelf = 'flex-start'; // Căn trái
        div.innerHTML = cardHTML;
        msgContainer.appendChild(div);
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    async function loadHistory() {
        try {
            const res = await fetch('logic/ai/chat_process.php'); // Gọi GET
            const data = await res.json();
            
            msgContainer.innerHTML = ''; // Xóa trắng trước khi load
            
            if (data.history && data.history.length > 0) {
                data.history.forEach(item => {
                    if (item.type === 'text') {
                        // role 'user' -> class 'user', role 'bot' -> class 'bot'
                        appendMessage(item.content, item.role === 'user' ? 'user' : 'bot');
                    } else if (item.type === 'card') {
                        appendCard(item.content);
                    }
                });
            } else {
                appendMessage("Xin chào! 👋 Tôi là EduBot. Tôi có thể giúp gì cho bạn?", "bot");
            }
        } catch (e) {
            console.error("Lỗi load history:", e);
        }
    }

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // 1. Hiện tin nhắn User
        appendMessage(text, 'user');
        input.value = '';
        history.push({ role: 'user', content: text });

        // 2. Hiện loading
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message loading';
        loadingDiv.innerText = 'EduBot đang soạn tin...';
        msgContainer.appendChild(loadingDiv);

        try {
            // 3. Gửi lên Server
            const res = await fetch('logic/ai/chat_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text, history: history.slice(-6) }) // Chỉ gửi 6 tin gần nhất
            });
            
            const data = await res.json();
            
            // 4. Xử lý phản hồi
            msgContainer.removeChild(loadingDiv); // Xóa loading
            
            // Hiện tin nhắn Bot
            appendMessage(data.reply, 'bot');
            history.push({ role: 'model', content: data.reply });

            // Hiện Card (nếu có)
            if (data.card) {
                appendCard(data.card);
            }

        } catch (err) {
            msgContainer.removeChild(loadingDiv);
            appendMessage("Lỗi kết nối. Vui lòng thử lại!", 'bot');
        }
    }

    sendBtn.onclick = sendMessage;
    input.onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };
    // Tự động load lịch sử khi bật trang
    loadHistory();
});