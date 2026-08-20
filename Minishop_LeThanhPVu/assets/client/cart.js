/**
 * ============================================================================
 * MINISHOP - XỬ LÝ GIỎ HÀNG & YÊU THÍCH BẰNG AJAX (JAVASCRIPT THUẦN)
 * Tác giả: Lê Thanh Phi Vũ
 * Chức năng: Thêm vào giỏ, cập nhật số lượng, xóa hàng, thả tim yêu thích,
 *            hiển thị thông báo Toast đẹp mắt mà không cần tải lại trang.
 * ============================================================================
 */

// 1. HÀM ĐỊNH DẠNG TIỀN TỆ VIỆT NAM ĐỒNG (VNĐ)
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
}

// 2. HIỂN THỊ THÔNG BÁO NỔI TOAST (POPUP GÓC PHẢI DƯỚI)
function showCartToast(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1090';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
    const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${iconClass} fs-5"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    if (window.bootstrap && bootstrap.Toast) {
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
        alert(message);
        toastEl.remove();
    }
}

// 3. CẬP NHẬT SỐ LƯỢNG HUY HIỆU GIỎ HÀNG TRÊN HEADER
function updateCartBadge(count) {
    const badge = document.querySelector('#cartCount');
    if (badge) {
        badge.textContent = count;
    }
}

// 4. BẮT SỰ KIỆN THÊM VÀO GIỎ HÀNG (AJAX FETCH)
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-add-cart');
        if (!button) return;

        e.preventDefault();
        const productId = button.dataset.productid;
        if (!productId) return;

        // Lấy số lượng từ ô input nếu đang ở trang chi tiết sản phẩm
        const qtyInput = document.getElementById('qty-input');
        const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

        const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';
        const formData = new FormData();
        formData.append('productid', productId);
        formData.append('quantity', quantity);

        // Gửi request ngầm lên server
        fetch(baseUrl + '/cart/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartBadge(data.cartCount);
                showCartToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
            } else {
                showCartToast(data.message || 'Có lỗi xảy ra khi thêm vào giỏ.', 'error');
            }
        })
        .catch(error => {
            console.error('Lỗi Cart Add:', error);
            showCartToast('Lỗi kết nối máy chủ khi thêm vào giỏ.', 'error');
        });
    });
});

// 5. CẬP NHẬT SỐ LƯỢNG TRONG TRANG GIỎ HÀNG (TĂNG / GIẢM SỐ LƯỢNG)
function updateCart(productId, quantity) {
    const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';
    const formData = new FormData();
    formData.append('productid', productId);
    formData.append('quantity', quantity);

    fetch(baseUrl + '/cart/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cartCount);

            if (quantity <= 0) {
                const row = document.getElementById('cart-row-' + productId);
                if (row) row.remove();
            } else {
                const qtyEl = document.getElementById('qty-' + productId);
                if (qtyEl) {
                    if (qtyEl.tagName === 'INPUT') qtyEl.value = quantity;
                    else qtyEl.textContent = quantity;
                }

                const subtotalEl = document.getElementById('subtotal-' + productId);
                if (subtotalEl) subtotalEl.textContent = formatCurrency(data.subtotal);
            }

            // Cập nhật lại tổng tiền giỏ hàng
            document.querySelectorAll('.cart-total-text').forEach(el => {
                el.textContent = formatCurrency(data.cartTotal);
            });

            // Nếu giỏ trống, chuyển sang giao diện thông báo giỏ trống
            if (data.cartCount <= 0) {
                const cartContent = document.getElementById('cart-content');
                const cartEmpty = document.getElementById('cart-empty');
                if (cartContent) cartContent.classList.add('d-none');
                if (cartEmpty) cartEmpty.classList.remove('d-none');
            }

            showCartToast(data.message, 'success');
        } else {
            showCartToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi Cart Update:', error);
        showCartToast('Lỗi kết nối khi cập nhật giỏ hàng.', 'error');
    });
}

// 6. XÓA SẢN PHẨM KHỎI GIỎ HÀNG
function removeCart(productId) {
    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        return;
    }

    const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';
    const formData = new FormData();
    formData.append('productid', productId);

    fetch(baseUrl + '/cart/remove', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cartCount);

            const row = document.getElementById('cart-row-' + productId);
            if (row) row.remove();

            document.querySelectorAll('.cart-total-text').forEach(el => {
                el.textContent = formatCurrency(data.cartTotal);
            });

            if (data.cartCount <= 0) {
                const cartContent = document.getElementById('cart-content');
                const cartEmpty = document.getElementById('cart-empty');
                if (cartContent) cartContent.classList.add('d-none');
                if (cartEmpty) cartEmpty.classList.remove('d-none');
            }

            showCartToast(data.message, 'success');
        } else {
            showCartToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi Cart Remove:', error);
        showCartToast('Lỗi kết nối khi xóa sản phẩm.', 'error');
    });
}

// 7. XỬ LÝ NÚT THẢ TIM YÊU THÍCH (WISHLIST TOGGLE)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-wishlist-toggle');
    if (!btn) return;

    e.preventDefault();
    const productId = btn.getAttribute('data-product-id');
    if (!productId) return;

    const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch(baseUrl + '/index.php?area=client&controller=wishlist&action=toggle', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Đổi màu và icon tim đỏ / xám
            const icon = btn.querySelector('i');
            if (data.is_wishlisted) {
                if (icon) {
                    icon.className = 'bi bi-heart-fill text-danger';
                }
                btn.style.color = '#e11d48';
                btn.setAttribute('title', 'Bỏ thích');
            } else {
                if (icon) {
                    icon.className = 'bi bi-heart';
                }
                btn.style.color = '#94a3b8';
                btn.setAttribute('title', 'Yêu thích');
                
                // Nếu đang ở trang Wishlist thì tự xóa card sản phẩm
                const col = document.getElementById('wishlist-col-' + productId);
                if (col) col.remove();
            }

            // Cập nhật số lượng đếm trên Header
            const badge = document.getElementById('wishlistCount');
            if (badge) {
                badge.textContent = data.count;
            }

            showCartToast(data.message, 'success');
        } else {
            showCartToast(data.message || 'Lỗi xử lý yêu thích', 'error');
        }
    })
    .catch(err => {
        console.error('Wishlist error:', err);
    });
});
