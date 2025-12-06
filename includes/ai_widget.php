<div class="ai-widget-container">
    
    <button id="aiToggleBtn" class="ai-toggle-btn">
        <i class="fa-solid fa-robot"></i>
        <span class="notification-dot"></span>
    </button>

    <div id="aiChatBox" class="ai-chat-box">
        
        <div class="chat-header">
            <div class="bot-info">
                <div class="bot-avatar">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h4>EduBot AI</h4>
                    <span class="status"> Đang trực tuyến</span>
                </div>
            </div>
            <button id="aiCloseBtn" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="chat-body" id="chatBody">
            
            <div class="msg-row bot-msg">
                <div class="msg-bubble">
                    Chào bạn! 👋 Tôi là trợ lý AI của EduTech. Tôi có thể giúp gì cho bạn hôm nay?
                </div>
            </div>

            <div class="msg-row bot-msg">
                <div class="quick-options">
                    <button class="opt-btn" onclick="sendDemoMsg('Tìm khóa học phù hợp')">🔍 Tìm khóa học</button>
                    <button class="opt-btn" onclick="sendDemoMsg('Lên lộ trình học tập')">🗺️ Lộ trình học</button>
                    <button class="opt-btn" onclick="sendDemoMsg('Tư vấn nghề nghiệp')">💼 Tư vấn nghề</button>
                </div>
            </div>

            </div>

        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Nhập câu hỏi của bạn...">
            <button id="chatSendBtn"><i class="fa-solid fa-paper-plane"></i></button>
        </div>

    </div>
</div>