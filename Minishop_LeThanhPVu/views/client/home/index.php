<?php include __DIR__ . "/../layouts/header.php"; ?>

<!-- Hero Banner - Minimalist Dark & Silver -->
<section class="hero-banner text-white py-5">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-secondary mb-3 px-3 py-2 text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.75rem;">Premium Tech Store</span>
                <h1 class="display-5 fw-bold mb-3 text-white">CÔNG NGHỆ ĐỈNH CAO.<br><span class="text-light opacity-75">THIẾT KẾ TINH TẾ.</span></h1>
                <p class="lead text-light opacity-75 mb-4" style="font-size: 1.05rem;">Khám phá các thiết bị và phụ kiện công nghệ cao cấp chính hãng với mức giá tối ưu nhất.</p>
                <div class="d-flex gap-3">
                    <a href="<?= $baseUrl ?>/products" class="btn btn-silver px-4 py-2 text-decoration-none">
                        <i class="bi bi-grid me-2"></i>Khám phá sản phẩm
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <i class="bi bi-laptop display-1 text-white opacity-25" style="font-size: 9rem;"></i>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- Danh mục nổi bật -->
    <section class="mb-5">
        <div class="text-center mb-4">
            <h2 class="section-title">Danh mục nổi bật</h2>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php foreach ($featuredCategories as $cat): ?>
            <div class="col">
                <a href="<?= $baseUrl ?>/category/<?= urlencode($cat->slug) ?>" class="text-decoration-none">
                    <div class="category-card text-center h-100">
                        <i class="bi bi-box-seam display-6 mb-2 d-block"></i>
                        <h6 class="fw-semibold text-dark mb-0 small"><?= htmlspecialchars($cat->name) ?></h6>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sản phẩm giảm giá -->
    <?php if (!empty($discountedProducts)): ?>
    <section class="mb-5">
        <div class="text-center mb-4">
            <h2 class="section-title">Ưu đãi nổi bật</h2>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($discountedProducts as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sản phẩm mới nhất -->
    <section class="mb-5">
        <div class="text-center mb-4">
            <h2 class="section-title">Sản phẩm mới nhất</h2>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($newestProducts as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?= $baseUrl ?>/products" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                Xem tất cả sản phẩm <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </section>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
