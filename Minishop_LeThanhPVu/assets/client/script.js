// MiniShop Client JS
document.addEventListener('DOMContentLoaded', function () {
    // Qty +/- buttons on detail page
    document.querySelectorAll('.btn-qty').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById('qty-input');
            if (!input) return;
            var val = parseInt(input.value) || 1;
            if (this.dataset.action === 'increase') input.value = val + 1;
            else if (val > 1) input.value = val - 1;
        });
    });
});
