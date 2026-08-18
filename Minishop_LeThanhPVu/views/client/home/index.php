<?php include __DIR__ . "/../layouts/header.php"; ?>

<!-- Hero Banner -->
<section class="hero-banner bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-3">MINI SHOP</h1>
                <p class="lead mb-4">Sản phẩm công nghệ chính hãng – Giá tốt nhất thị trường.<br>Miễn phí vận chuyển cho đơn hàng từ 500.000đ.</p>
                <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=index" class="btn btn-light btn-lg fw-semibold px-4"><i class="bi bi-grid me-2"></i>Xem tất cả sản phẩm</a>
            </div>
            <div class="col-lg-5 text-center">
                <i class="bi bi-laptop display-1" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- Danh mục nổi bật -->
    <section class="mb-5">
        <h2 class="section-title text-center mb-4">
            <span class="text-primary"><i class="bi bi-folder2-open me-2"></i></span>DANH MỤC NỔI BẬT
        </h2>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php foreach ($featuredCategories as $cat): ?>
            <div class="col">
                <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=category&slug=<?= urlencode($cat->slug) ?>" class="text-decoration-none">
                    <div class="card category-card text-center border-0 shadow-sm h-100 p-3">
                        <div class="card-body">
                            <i class="bi bi-box-seam display-6 text-primary mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($cat->name) ?></h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sản phẩm giảm giá -->
    <?php if (!empty($discountedProducts)): ?>
    <section class="mb-5">
        <h2 class="section-title text-center mb-4">
            <span class="text-danger"><i class="bi bi-lightning-fill me-2"></i></span>ĐANG GIẢM GIÁ
        </h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($discountedProducts as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sản phẩm mới nhất -->
    <section class="mb-5">
        <h2 class="section-title text-center mb-4">
            <span class="text-success"><i class="bi bi-stars me-2"></i></span>SẢN PHẨM MỚI NHẤT
        </h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($newestProducts as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=index" class="btn btn-outline-primary btn-lg"><i class="bi bi-arrow-right me-2"></i>Xem tất cả sản phẩm</a>
        </div>
    </section>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
