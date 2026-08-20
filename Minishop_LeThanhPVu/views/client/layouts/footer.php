<!-- Footer - Minimalist Dark Theme -->
<footer class="site-footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white mb-3 fs-5"><i class="bi bi-cpu text-white me-2"></i>MINISHOP</h6>
                <p class="small text-secondary" style="line-height: 1.7;">Cửa hàng thiết bị và phụ kiện công nghệ cao cấp chính hãng. Cam kết chất lượng, bảo hành chuẩn mực và trải nghiệm dịch vụ hàng đầu.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1" target="_blank" rel="noopener noreferrer" class="footer-social-btn" title="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="mb-3 text-uppercase small text-white">Chính sách & Hỗ trợ</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=warranty"><i class="bi bi-chevron-right me-1 small"></i>Chính sách bảo hành chính hãng</a></li>
                    <li><a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=return"><i class="bi bi-chevron-right me-1 small"></i>Chính sách đổi trả trong 30 ngày</a></li>
                    <li><a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=shipping"><i class="bi bi-chevron-right me-1 small"></i>Chính sách giao hàng toàn quốc</a></li>
                    <li><a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=payment"><i class="bi bi-chevron-right me-1 small"></i>Hướng dẫn mua hàng và thanh toán</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12">
                <h6 class="mb-3 text-uppercase small text-white">Liên hệ</h6>
                <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                    <li><i class="bi bi-geo-alt me-2 text-white"></i>123 Đường ABC, Quận 1, TP. Hồ Chí Minh</li>
                    <li><i class="bi bi-telephone me-2 text-white"></i>0123-456-789 (8:00 - 22:00)</li>
                    <li><i class="bi bi-envelope me-2 text-white"></i>contact@minishop.vn</li>
                </ul>
            </div>
        </div>
        <hr style="border-color: rgba(255, 255, 255, 0.08);" class="mt-4 mb-3">
        <p class="text-center text-secondary mb-0 small">&copy; <?= date('Y') ?> MiniShop - Le Thanh Phi Vu. All rights reserved.</p>
    </div>
</footer>

<!-- AI Chatbot Floating Widget -->
<div class="chatbot-toggler" id="chatbotToggler" title="Trò chuyện với AI MiniShop">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="badge-pulse"></span>
</div>

<div class="chatbot-container" id="chatbotContainer">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div>
                <h6 class="chatbot-title">Trợ lý AI MiniShop</h6>
                <p class="chatbot-status"><span class="dot"></span> Trực tuyến 24/7</p>
            </div>
        </div>
        <button class="chatbot-close-btn" id="chatbotCloseBtn" title="Đóng">&times;</button>
    </div>

    <!-- Messages -->
    <div class="chatbot-messages" id="chatbotMessages">
        <div class="chat-bubble bot">
            Xin chào anh/chị! Em là <strong>Trợ lý AI MiniShop</strong> 🤖.<br>
            Em có thể tư vấn chọn linh kiện, tìm kiếm sản phẩm hoặc giải đáp chính sách bán hàng. Anh/chị cần em hỗ trợ gì ạ?
            
            <div class="chatbot-suggestions">
                <span class="suggestion-chip" data-prompt="Tư vấn chuột gaming">🖱️ Chuột gaming</span>
                <span class="suggestion-chip" data-prompt="Tìm bàn phím cơ tốt nhất">⌨️ Bàn phím cơ</span>
                <span class="suggestion-chip" data-prompt="Sản phẩm nào đang giảm giá?">🔥 Đang giảm giá</span>
                <span class="suggestion-chip" data-prompt="Chính sách bảo hành và đổi trả">📦 Chính sách bảo hành</span>
                <span class="suggestion-chip" data-prompt="Hướng dẫn thanh toán VNPay">💳 Thanh toán VNPay</span>
            </div>
        </div>
    </div>

    <!-- Footer Input -->
    <div class="chatbot-footer">
        <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Nhập câu hỏi hoặc tên sản phẩm..." autocomplete="off">
        <button class="chatbot-send-btn" id="chatbotSendBtn" title="Gửi">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseUrl ?? '/LTW_01/Minishop_LeThanhPVu' ?>/assets/client/script.js"></script>
<script src="<?= $baseUrl ?? '/LTW_01/Minishop_LeThanhPVu' ?>/assets/client/cart.js"></script>
<script src="<?= $baseUrl ?? '/LTW_01/Minishop_LeThanhPVu' ?>/assets/client/chatbot.js?v=<?= time() ?>"></script>
</body>
</html>
