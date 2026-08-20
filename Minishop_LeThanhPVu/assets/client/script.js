/**
 * ============================================================================
 * MINISHOP - JAVASCRIPT GIAO DIỆN CLIENT
 * Tác giả: Lê Thanh Phi Vũ
 * Chức năng: Nút tăng/giảm số lượng chi tiết sản phẩm,
 *            bộ đếm ngược Flash Sale thời gian thực đồng bộ qua localStorage.
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. XỬ LÝ NÚT TĂNG / GIẢM SỐ LƯỢNG (+) (-) TRÊN TRANG CHI TIẾT SẢN PHẨM
    document.querySelectorAll('.btn-qty').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById('qty-input');
            if (!input) return;
            var val = parseInt(input.value) || 1;
            var maxAttr = input.getAttribute('max');
            var max = maxAttr ? parseInt(maxAttr) : 999;
            if (this.dataset.action === 'increase') {
                if (val < max) input.value = val + 1;
            } else if (val > 1) {
                input.value = val - 1;
            }
        });
    });

    // 2. KHỞI CHẠY BỘ ĐẾM NGƯỢC FLASH SALE THỜI GIAN THỰC
    initFlashSaleCountdown();
});

/**
 * Hàm khởi tạo và chạy bộ đếm ngược Flash Sale
 * Đồng bộ mốc thời gian kết thúc qua localStorage để không bị reset khi chuyển trang
 */
function initFlashSaleCountdown() {
    // Lấy mốc thời gian kết thúc từ bộ nhớ trình duyệt
    let targetTime = localStorage.getItem('minishop_flashsale_target');
    const now = new Date().getTime();

    // Nếu chưa có mốc hoặc đã hết giờ, tạo mới: 2 ngày 18 giờ 45 phút kể từ lúc vào
    if (!targetTime || parseInt(targetTime) <= now) {
        targetTime = now + (2 * 24 * 3600 + 18 * 3600 + 45 * 60) * 1000;
        localStorage.setItem('minishop_flashsale_target', targetTime);
    } else {
        targetTime = parseInt(targetTime);
    }

    // Hàm cập nhật số giây lùi và hiển thị ra màn hình
    function updateCountdown() {
        const currentTime = new Date().getTime();
        let distance = targetTime - currentTime;

        if (distance < 0) {
            distance = 0;
        }

        // Tính toán Ngày - Giờ - Phút - Giây
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Định dạng 2 chữ số (ví dụ: 05, 09)
        const pad = (n) => String(n).padStart(2, '0');

        // Cập nhật trên Trang chủ (Flash Sale Section)
        const dEl = document.getElementById('fs-days');
        const hEl = document.getElementById('fs-hours');
        const mEl = document.getElementById('fs-minutes');
        const sEl = document.getElementById('fs-seconds');

        if (dEl && hEl && mEl && sEl) {
            dEl.textContent = pad(days);
            hEl.textContent = pad(hours);
            mEl.textContent = pad(minutes);
            sEl.textContent = pad(seconds);
        }

        // Cập nhật trên Trang Chi tiết sản phẩm (Banner đỏ)
        const detailTimer = document.getElementById('detail-countdown');
        if (detailTimer) {
            detailTimer.textContent = `${pad(days)} ngày ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}
