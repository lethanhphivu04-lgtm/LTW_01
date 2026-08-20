<?php include __DIR__ . "/../layouts/header.php"; ?>

<!-- Hero Banner Carousel - Minimalist Dark & Silver -->
<?php $banners = $banners ?? []; ?>
<div id="homeHeroCarousel" class="carousel slide hero-banner" data-bs-ride="carousel" data-bs-interval="5000">
    <?php if (count($banners) > 1): ?>
    <div class="carousel-indicators">
        <?php foreach ($banners as $idx => $b): ?>
            <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-current="<?= $idx === 0 ? 'true' : 'false' ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="carousel-inner">
        <?php foreach ($banners as $idx => $b): ?>
        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?> py-5 text-white">
            <div class="container py-3">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="badge bg-secondary mb-3 px-3 py-2 text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.75rem;"><?= htmlspecialchars($b->badgeText) ?></span>
                        <h1 class="display-5 fw-bold mb-3 text-white"><?= $b->title ?></h1>
                        <p class="lead text-light opacity-75 mb-4" style="font-size: 1.05rem;"><?= htmlspecialchars($b->subtitle ?? '') ?></p>
                        <div class="d-flex gap-3">
                            <a href="<?= (str_starts_with($b->link, 'http') || str_starts_with($b->link, 'index.php') || str_starts_with($b->link, '/')) ? ($baseUrl . $b->link) : ($baseUrl . '/' . $b->link) ?>" class="btn btn-silver px-4 py-2 text-decoration-none">
                                <i class="bi bi-arrow-right-circle me-2"></i>Khám phá ngay
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 text-center d-none d-lg-block">
                        <?php if (!empty($b->image) && file_exists(__DIR__ . "/../../uploads/banners/" . $b->image)): ?>
                            <img src="<?= $baseUrl ?>/uploads/banners/<?= htmlspecialchars($b->image) ?>" alt="Banner Slide" class="img-fluid rounded-4 shadow" style="max-height: 280px; object-fit: contain;">
                        <?php else: ?>
                            <i class="bi <?= $idx === 0 ? 'bi-laptop' : ($idx === 1 ? 'bi-ticket-perforated' : 'bi-shield-check') ?> display-1 text-white opacity-25" style="font-size: 9rem;"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($banners) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Trước</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sau</span>
    </button>
    <?php endif; ?>
</div>

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

    <!-- Sản phẩm giảm giá / Flash Sale với Bộ đếm ngược -->
    <?php if (!empty($discountedProducts)): ?>
    <section class="mb-5 p-4 rounded-4 position-relative" style="background: linear-gradient(135deg, #090d16 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.08);">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <span class="badge bg-danger text-uppercase px-3 py-2 fw-bold mb-2" style="letter-spacing: 1px;">
                    <i class="bi bi-fire me-1"></i>FLASH SALE GIỚI HẠN
                </span>
                <h2 class="h3 fw-bold text-white mb-0">Ưu Đãi Giảm Giá Cực Sốc</h2>
            </div>
            
            <!-- Đồng hồ đếm ngược -->
            <div class="d-flex align-items-center gap-2 bg-dark p-2 px-3 rounded-3 border border-secondary">
                <span class="small text-light me-1"><i class="bi bi-stopwatch text-danger me-1"></i>Kết thúc sau:</span>
                <div class="text-center bg-danger text-white rounded px-2 py-1 fw-bold" style="min-width: 38px;">
                    <span id="fs-days" class="fs-6">02</span>
                    <div style="font-size: 9px; text-transform: uppercase;">Ngày</div>
                </div>
                <span class="text-white fw-bold">:</span>
                <div class="text-center bg-danger text-white rounded px-2 py-1 fw-bold" style="min-width: 38px;">
                    <span id="fs-hours" class="fs-6">14</span>
                    <div style="font-size: 9px; text-transform: uppercase;">Giờ</div>
                </div>
                <span class="text-white fw-bold">:</span>
                <div class="text-center bg-danger text-white rounded px-2 py-1 fw-bold" style="min-width: 38px;">
                    <span id="fs-minutes" class="fs-6">35</span>
                    <div style="font-size: 9px; text-transform: uppercase;">Phút</div>
                </div>
                <span class="text-white fw-bold">:</span>
                <div class="text-center bg-danger text-white rounded px-2 py-1 fw-bold" style="min-width: 38px;">
                    <span id="fs-seconds" class="fs-6">42</span>
                    <div style="font-size: 9px; text-transform: uppercase;">Giây</div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($discountedProducts as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sản phẩm bán chạy nhất (Top Sellers) -->
    <?php if (!empty($bestSellingProducts)): ?>
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
            <div>
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1 mb-1" style="font-size: 0.7rem;">
                    <i class="bi bi-trophy-fill me-1 text-danger"></i>TOP YÊU THÍCH & BÁN CHẠY
                </span>
                <h2 class="section-title text-start mb-0">Sản phẩm bán chạy nhất</h2>
            </div>
            <a href="<?= $baseUrl ?>/products" class="text-decoration-none fw-semibold small text-primary">
                Xem thêm <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($bestSellingProducts as $p): ?>
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
