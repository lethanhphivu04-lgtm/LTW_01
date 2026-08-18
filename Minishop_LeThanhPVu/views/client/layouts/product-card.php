<?php
// $p = associative array from DAO (product row with joins)
$baseUrl = $baseUrl ?? '/LTW_01/Minishop_LeThanhPVu';
$hasDiscount = isset($p['discount_price']) && $p['discount_price'] > 0 && $p['discount_price'] < $p['price'];
$discountPercent = $hasDiscount ? round((1 - $p['discount_price'] / $p['price']) * 100) : 0;
?>
<div class="col">
    <div class="card product-card h-100 border-0 shadow-sm">
        <?php if ($hasDiscount && $discountPercent > 0): ?>
        <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-2 py-1">-<?= $discountPercent ?>%</span>
        <?php endif; ?>
        <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=detail&slug=<?= urlencode($p['slug']) ?>">
            <img src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($p['image'] ?? 'default.png') ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($p['proname']) ?>" style="height:200px; object-fit:contain;">
        </a>
        <div class="card-body d-flex flex-column">
            <p class="text-muted small mb-1"><?= htmlspecialchars($p['category_name'] ?? '') ?></p>
            <h6 class="card-title fw-bold mb-2">
                <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=detail&slug=<?= urlencode($p['slug']) ?>" class="text-dark text-decoration-none product-title"><?= htmlspecialchars($p['proname']) ?></a>
            </h6>
            <div class="mt-auto">
                <?php if ($hasDiscount): ?>
                    <span class="text-danger fw-bold fs-6"><?= number_format($p['discount_price'], 0, ',', '.') ?> đ</span>
                    <span class="text-muted text-decoration-line-through small ms-1"><?= number_format($p['price'], 0, ',', '.') ?> đ</span>
                <?php else: ?>
                    <span class="text-danger fw-bold fs-6"><?= number_format($p['price'], 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=detail&slug=<?= urlencode($p['slug']) ?>" class="btn btn-outline-primary btn-sm flex-fill"><i class="bi bi-eye me-1"></i>Chi tiết</a>
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-cart-plus me-1"></i>Mua</button>
            </div>
        </div>
    </div>
</div>
