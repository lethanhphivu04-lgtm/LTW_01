<?php
// $p = associative array from DAO (product row with joins)
$baseUrl = $baseUrl ?? '/LTW_01/Minishop_LeThanhPVu';
$hasDiscount = isset($p['discount_price']) && $p['discount_price'] > 0 && $p['discount_price'] < $p['price'];
$discountPercent = $hasDiscount ? round((1 - $p['discount_price'] / $p['price']) * 100) : 0;
$imgFile = htmlspecialchars($p['image'] ?? 'default.png');
$isWishlisted = in_array((int)($p['id'] ?? 0), $_SESSION['wishlist'] ?? []);
?>
<div class="col">
    <div class="card product-card h-100 position-relative">
        <!-- Nút Thả Tim Yêu Thích -->
        <button type="button" class="btn btn-sm btn-wishlist-toggle position-absolute top-0 start-0 m-2 rounded-circle shadow-sm border-0" 
                data-product-id="<?= $p['id'] ?>" 
                title="<?= $isWishlisted ? 'Bỏ thích' : 'Yêu thích' ?>"
                style="width:34px; height:34px; z-index:10; background:rgba(255,255,255,0.9); color: <?= $isWishlisted ? '#e11d48' : '#94a3b8' ?>;">
            <i class="bi <?= $isWishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
        </button>

        <?php if (isset($p['quantity']) && (int)$p['quantity'] <= 0): ?>
            <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Hết hàng</span>
        <?php elseif ($hasDiscount && $discountPercent > 0): ?>
            <span class="badge-discount position-absolute top-0 end-0 m-2">-<?= $discountPercent ?>%</span>
        <?php endif; ?>

        <a href="<?= $baseUrl ?>/product/<?= urlencode($p['slug']) ?>" class="product-img-wrapper text-decoration-none">
            <img src="<?= $baseUrl ?>/uploads/products/<?= $imgFile ?>" 
                 alt="<?= htmlspecialchars($p['proname']) ?>" 
                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="product-placeholder-icon" style="display:none; width:100%; height:100%; align-items:center; justify-content:center;">
                <i class="bi bi-cpu"></i>
            </div>
        </a>
        <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <p class="text-secondary small mb-0" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;"><?= htmlspecialchars($p['category_name'] ?? 'Công nghệ') ?></p>
                <div class="small text-warning" style="font-size:0.75rem;">
                    <i class="bi bi-star-fill"></i> 5.0
                </div>
            </div>
            <h6 class="mb-2">
                <a href="<?= $baseUrl ?>/product/<?= urlencode($p['slug']) ?>" class="product-title"><?= htmlspecialchars($p['proname']) ?></a>
            </h6>
            <div class="mt-auto pt-2">
                <?php if ($hasDiscount): ?>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="price-current"><?= number_format($p['discount_price'], 0, ',', '.') ?> đ</span>
                        <span class="price-old"><?= number_format($p['price'], 0, ',', '.') ?> đ</span>
                    </div>
                <?php else: ?>
                    <div class="price-current"><?= number_format($p['price'], 0, ',', '.') ?> đ</div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="<?= $baseUrl ?>/product/<?= urlencode($p['slug']) ?>" class="btn btn-action-view flex-fill text-center text-decoration-none">Chi tiết</a>
                <button type="button" class="btn btn-action-buy flex-fill btn-add-cart" data-productid="<?= $p['id'] ?>">+ Giỏ hàng</button>
            </div>
        </div>
    </div>
</div>
