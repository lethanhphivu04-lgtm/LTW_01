<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>" class="text-secondary text-decoration-none">Trang chủ</a></li>
            <?php if (!empty($product['category_name'])): ?>
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/category/<?= urlencode($product['category_slug']) ?>" class="text-secondary text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active text-dark fw-semibold"><?= htmlspecialchars($product['proname']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Ảnh sản phẩm -->
        <div class="col-lg-5">
            <div class="card border rounded-3 p-4 bg-white text-center" style="border-color: #e2e8f0 !important;">
                <img src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" 
                     class="img-fluid mx-auto" 
                     alt="<?= htmlspecialchars($product['proname']) ?>" 
                     style="max-height:350px; object-fit:contain;"
                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="product-placeholder-icon py-5" style="display:none; align-items:center; justify-content:center; font-size: 6rem; color:#cbd5e1;">
                    <i class="bi bi-cpu"></i>
                </div>
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-lg-7">
            <h1 class="fw-bold h3 mb-3 text-dark"><?= htmlspecialchars($product['proname']) ?></h1>

            <div class="d-flex align-items-center gap-2 mb-3">
                <?php if (!empty($product['category_name'])): ?>
                <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($product['category_name']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['brand_name'])): ?>
                <span class="badge bg-dark text-white px-2 py-1"><?= htmlspecialchars($product['brand_name']) ?></span>
                <?php endif; ?>
            </div>

            <?php
            $hasDiscount = isset($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
            ?>
            <div class="p-3 mb-4 rounded-3" style="background:#f1f5f9; border: 1px solid #e2e8f0;">
                <?php if ($hasDiscount): ?>
                    <span class="fw-bold fs-3 text-dark"><?= number_format($product['discount_price'], 0, ',', '.') ?> đ</span>
                    <span class="text-muted text-decoration-line-through fs-5 ms-2"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                    <?php $percent = round((1 - $product['discount_price'] / $product['price']) * 100); ?>
                    <span class="badge bg-dark ms-2">-<?= $percent ?>%</span>
                <?php else: ?>
                    <span class="fw-bold fs-3 text-dark"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark">Mô tả sản phẩm:</h6>
                <p class="text-secondary" style="line-height:1.7;"><?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả chi tiết.')) ?></p>
            </div>

            <div class="d-flex align-items-center gap-3 mb-4">
                <label class="fw-semibold text-dark">Số lượng:</label>
                <div class="input-group" style="width:130px;">
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="decrease" <?= (($product['quantity'] ?? 0) <= 0) ? 'disabled' : '' ?>>−</button>
                    <input type="number" class="form-control text-center bg-white" id="qty-input" value="<?= (($product['quantity'] ?? 0) > 0) ? 1 : 0 ?>" min="1" max="<?= (int)($product['quantity'] ?? 0) ?>" readonly>
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="increase" <?= (($product['quantity'] ?? 0) <= 0) ? 'disabled' : '' ?>>+</button>
                </div>
                <?php if (($product['quantity'] ?? 0) > 0): ?>
                    <span class="text-muted small ms-2">(Còn <strong class="text-dark"><?= (int)$product['quantity'] ?></strong> sản phẩm trong kho)</span>
                <?php else: ?>
                    <span class="badge bg-danger ms-2 px-2 py-1">Hết hàng</span>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-3">
                <?php if (($product['quantity'] ?? 0) > 0): ?>
                    <button type="button" class="btn btn-outline-dark btn-lg flex-fill btn-add-cart fw-semibold" data-productid="<?= $product['id'] ?>">
                        <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ
                    </button>
                    <button type="button" class="btn btn-dark btn-lg flex-fill btn-add-cart btn-buy-now fw-semibold" data-productid="<?= $product['id'] ?>" onclick="setTimeout(() => window.location.href='<?= $baseUrl ?>/cart', 300)">
                        <i class="bi bi-bag-check me-2"></i>Mua ngay
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-lg flex-fill fw-semibold" disabled>
                        <i class="bi bi-x-circle me-2"></i>Tạm hết hàng
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="mt-5 pt-5 border-top">
        <div class="text-center mb-4">
            <h3 class="section-title">Sản phẩm tương tự</h3>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($relatedProducts as $p): ?>
                <?php if ($p['slug'] !== $product['slug']): ?>
                    <?php include __DIR__ . "/../layouts/product-card.php"; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
