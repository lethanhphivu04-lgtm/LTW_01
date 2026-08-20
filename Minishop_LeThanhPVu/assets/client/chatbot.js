/**
 * ============================================================================
 * MINISHOP - LOGIC WIDGET CHATBOT TƯ VẤN (JAVASCRIPT THUẦN)
 * Tác giả: Lê Thanh Phi Vũ
 * Chức năng: Đóng/mở khung chat, gửi câu hỏi qua AJAX, hiển thị hiệu ứng
 *            đang soạn tin (Typing Indicator), định dạng Markdown và render thẻ sản phẩm gợi ý.
 * ============================================================================
 */

document.addEventListener("DOMContentLoaded", function () {
    // 1. LẤY CÁC PHẦN TỬ GIAO DIỆN CỦA CHATBOT
    const toggler = document.getElementById("chatbotToggler");
    const container = document.getElementById("chatbotContainer");
    const closeBtn = document.getElementById("chatbotCloseBtn");
    const sendBtn = document.getElementById("chatbotSendBtn");
    const input = document.getElementById("chatbotInput");
    const messages = document.getElementById("chatbotMessages");

    if (!toggler || !container) return;

    const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';

    // 2. BẮT SỰ KIỆN MỞ / ĐÓNG CỬA SỔ CHAT
    toggler.addEventListener("click", () => {
        container.classList.toggle("active");
        if (container.classList.contains("active")) {
            input.focus();
        }
    });

    closeBtn.addEventListener("click", () => {
        container.classList.remove("active");
    });

    // 3. GỬI TIN NHẮN KHI BẤM NÚT HOẶC NHẤN PHÍM ENTER
    sendBtn.addEventListener("click", handleSendMessage);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    });

    // 4. BẮT SỰ KIỆN KHI BẤM VÀO CÁC NÚT GỢI Ý CÂU HỎI NHANH (SUGGESTION CHIPS)
    document.addEventListener("click", function (e) {
        const chip = e.target.closest(".suggestion-chip");
        if (chip) {
            const prompt = chip.getAttribute("data-prompt") || chip.textContent.trim();
            input.value = prompt;
            handleSendMessage();
        }
    });

    // 5. HÀM XỬ LÝ GỬI TIN NHẮN VÀ NHẬN PHẢN HỒI TỪ SERVER
    function handleSendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Thêm bong bóng chat của người dùng
        appendUserMessage(text);
        input.value = "";

        // Hiển thị hiệu ứng 3 chấm đang gõ (Typing Indicator)
        showTypingIndicator();

        // Gửi tin nhắn lên Backend PHP xử lý
        fetch(`${baseUrl}/index.php?area=client&controller=chatbot&action=send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            hideTypingIndicator();
            if (data.success) {
                appendBotMessage(data.reply, data.products || []);
            } else {
                appendBotMessage(data.reply || "Dạ, hiện em chưa thể xử lý yêu cầu này. Anh/chị thử lại câu hỏi khác nhé!", []);
            }
        })
        .catch(err => {
            hideTypingIndicator();
            appendBotMessage("Dạ em gặp chút trục trặc kết nối, anh/chị thử lại sau giây lát nhé!", []);
        });
    }

    // 6. THÊM TIN NHẮN CỦA KHÁCH VÀO KHUNG CHAT
    function appendUserMessage(text) {
        const div = document.createElement("div");
        div.className = "chat-bubble user";
        div.textContent = text;
        messages.appendChild(div);
        scrollToBottom();
    }

    // 7. THÊM CÂU TRẢ LỜI CỦA BOT KÈM DANH SÁCH SẢN PHẨM GỢI Ý (NẾU CÓ)
    function appendBotMessage(rawText, products = []) {
        const div = document.createElement("div");
        div.className = "chat-bubble bot";

        // Định dạng chữ in đậm, in nghiêng, xuống dòng
        let formatted = formatMarkdown(rawText);
        div.innerHTML = formatted;

        // Nếu có sản phẩm phù hợp được tìm thấy trong Database
        if (products && products.length > 0) {
            const prodListDiv = document.createElement("div");
            prodListDiv.className = "mt-2 pt-2 border-top";

            products.forEach(p => {
                const pImg = p.image ? `${baseUrl}/uploads/products/${p.image}` : `${baseUrl}/assets/client/images/no-image.png`;
                const price = formatCurrency(p.discount_price > 0 && p.discount_price < p.price ? p.discount_price : p.price);
                const pSlug = p.slug || '';
                const pUrl = `${baseUrl}/product/${pSlug}`;

                const pCard = document.createElement("div");
                pCard.className = "chatbot-product-card";
                pCard.innerHTML = `
                    <img src="${pImg}" alt="${escapeHtml(p.proname)}" class="chatbot-product-img" onerror="this.src='https://via.placeholder.com/60?text=SP'">
                    <div class="chatbot-product-info">
                        <h6 class="chatbot-product-name" title="${escapeHtml(p.proname)}">${escapeHtml(p.proname)}</h6>
                        <p class="chatbot-product-price">${price}</p>
                    </div>
                    <a href="${pUrl}" class="chatbot-product-btn" target="_blank">Xem ngay</a>
                `;
                prodListDiv.appendChild(pCard);
            });

            div.appendChild(prodListDiv);
        }

        messages.appendChild(div);
        scrollToBottom();
    }

    // 8. HIỆN VÀ ẨN HIỆU ỨNG ĐANG SOẠN TIN (3 DẤU CHẤM NHẤP NHÁY)
    function showTypingIndicator() {
        if (document.getElementById("chatbotTyping")) return;
        const ind = document.createElement("div");
        ind.id = "chatbotTyping";
        ind.className = "typing-indicator";
        ind.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        messages.appendChild(ind);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const ind = document.getElementById("chatbotTyping");
        if (ind) ind.remove();
    }

    // 9. TỰ ĐỘNG CUỘN XUỐNG DƯỚI CÙNG KHI CÓ TIN NHẮN MỚI
    function scrollToBottom() {
        setTimeout(() => {
            messages.scrollTop = messages.scrollHeight;
        }, 50);
    }

    // 10. HỖ TRỢ ĐỊNH DẠNG TEXT MARKDOWN
    function formatMarkdown(text) {
        if (!text) return "";
        let t = escapeHtml(text);
        t = t.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        t = t.replace(/\*(.*?)\*/g, '<em>$1</em>');
        t = t.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" style="color:#2563eb;text-decoration:underline;">$1</a>');
        t = t.replace(/\n/g, '<br>');
        return t;
    }

    // 11. HÀM LỌC KÝ TỰ ĐẶC BIỆT CHỐNG XSS
    function escapeHtml(str) {
        if (!str) return "";
        return str.replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    // 12. ĐỊNH DẠNG TIỀN TỆ VNĐ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
});
