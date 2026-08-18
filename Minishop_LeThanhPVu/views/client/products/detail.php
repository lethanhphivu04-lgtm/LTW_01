<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/index.php" class="text-decoration-none">Trang chủ</a></li>
            <?php if (!empty($product['category_name'])): ?>
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=category&slug=<?= urlencode($product['category_slug']) ?>" class="text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['proname']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Ảnh sản phẩm -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <img src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" class="card-img-top p-4" alt="<?= htmlspecialchars($product['proname']) ?>" style="max-height:400px; object-fit:contain;">
            </div>
        </div>
        <!-- Thông tin sản phẩm -->
        <div class="col-lg-7">
            <h1 class="fw-bold h2 mb-3"><?= htmlspecialchars($product['proname']) ?></h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <?php if (!empty($product['category_name'])): ?>
                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-folder me-1"></i><?= htmlspecialchars($product['category_name']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['brand_name'])): ?>
                <span class="badge bg-info-subtle text-info"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($product['brand_name']) ?></span>
                <?php endif; ?>
            </div>

            <?php
            $hasDiscount = isset($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
            ?>
            <div class="price-block bg-light rounded p-3 mb-4">
                <?php if ($hasDiscount): ?>
                    <span class="text-danger fw-bold fs-3"><?= number_format($product['discount_price'], 0, ',', '.') ?> đ</span>
                    <span class="text-muted text-decoration-line-through fs-5 ms-2"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                    <?php $percent = round((1 - $product['discount_price'] / $product['price']) * 100); ?>
                    <span class="badge bg-danger ms-2">Giảm <?= $percent ?>%</span>
                <?php else: ?>
                    <span class="text-danger fw-bold fs-3"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">Mô tả sản phẩm:</h6>
                <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả.')) ?></p>
            </div>

            <div class="d-flex align-items-center gap-3 mb-4">
                <label class="fw-semibold">Số lượng:</label>
                <div class="input-group" style="width:140px;">
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="decrease">−</button>
                    <input type="number" class="form-control text-center" id="qty-input" value="1" min="1">
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="increase">+</button>
                </div>
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-primary btn-lg flex-fill"><i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng</button>
                <button class="btn btn-danger btn-lg flex-fill"><i class="bi bi-bag-check me-2"></i>Mua ngay</button>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="mt-5 pt-4 border-top">
        <h3 class="section-title text-center mb-4"><i class="bi bi-collection me-2 text-primary"></i>SẢN PHẨM LIÊN QUAN</h3>
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
