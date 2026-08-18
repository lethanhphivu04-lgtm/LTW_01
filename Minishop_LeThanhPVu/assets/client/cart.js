/**
 * MiniShop Cart JavaScript - Lab 13
 * AJAX Cart operations: Add, Update, Remove, Count & Toast Notifications
 */

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
}

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

function updateCartBadge(count) {
    const badge = document.querySelector('#cartCount');
    if (badge) {
        badge.textContent = count;
    }
}

// Bắt sự kiện Thêm vào giỏ hàng
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-add-cart');
        if (!button) return;

        e.preventDefault();
        const productId = button.dataset.productid;
        if (!productId) return;

        // Kiểm tra xem có input số lượng không (trên trang chi tiết sản phẩm)
        const qtyInput = document.getElementById('qty-input');
        const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

        const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';
        const formData = new FormData();
        formData.append('productid', productId);
        formData.append('quantity', quantity);

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

// Cập nhật số lượng sản phẩm trong giỏ hàng
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

            // Cập nhật tổng tiền
            document.querySelectorAll('.cart-total-text').forEach(el => {
                el.textContent = formatCurrency(data.cartTotal);
            });

            // Nếu giỏ hết hàng
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

// Xóa sản phẩm khỏi giỏ hàng
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
