<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="section-title"><?= $heading ?? 'TẤT CẢ SẢN PHẨM' ?></h2>
        <p class="text-secondary small mt-2"><?= $totalRecords ?? 0 ?> sản phẩm có sẵn</p>
    </div>

    <!-- Bộ lọc nâng cao kết hợp (Advanced Filter) -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-light">
        <form method="GET" action="<?= $baseUrl ?>/index.php" class="row g-2 align-items-center">
            <input type="hidden" name="area" value="client">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="search">

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Từ khóa tìm kiếm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                        <option value="<?= $cat->id ?>" <?= (isset($_GET['category_id']) && (int)$_GET['category_id'] === $cat->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->name) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="brand_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả thương hiệu --</option>
                    <?php if (!empty($brands)): foreach ($brands as $b): ?>
                        <option value="<?= $b->id ?>" <?= (isset($_GET['brand_id']) && (int)$_GET['brand_id'] === $b->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b->name) ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="price_range" class="form-select form-select-sm">
                    <option value="">-- Mức giá --</option>
                    <option value="under_1m" <?= ($_GET['price_range'] ?? '') === 'under_1m' ? 'selected' : '' ?>>Dưới 1 triệu</option>
                    <option value="1m_5m" <?= ($_GET['price_range'] ?? '') === '1m_5m' ? 'selected' : '' ?>>Từ 1 - 5 triệu</option>
                    <option value="5m_15m" <?= ($_GET['price_range'] ?? '') === '5m_15m' ? 'selected' : '' ?>>Từ 5 - 15 triệu</option>
                    <option value="over_15m" <?= ($_GET['price_range'] ?? '') === 'over_15m' ? 'selected' : '' ?>>Trên 15 triệu</option>
                </select>
            </div>

            <div class="col-md-2">
                <div class="form-check form-switch pt-1">
                    <input class="form-check-input" type="checkbox" name="on_sale" value="1" id="saleCheck" <?= !empty($_GET['on_sale']) ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-semibold text-danger" for="saleCheck">
                        <i class="bi bi-fire"></i> Đang giảm giá
                    </label>
                </div>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-dark btn-sm w-100 fw-semibold" title="Lọc dữ liệu">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                <?php if (!empty($_GET['keyword']) || !empty($_GET['category_id']) || !empty($_GET['brand_id']) || !empty($_GET['price_range']) || !empty($_GET['on_sale'])): ?>
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=product&action=index" class="btn btn-outline-secondary btn-sm" title="Xóa bộ lọc">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($list)): ?>
        <div class="text-center py-5">
            <div class="card p-5 border-0 shadow-sm rounded-3">
                <i class="bi bi-box-seam display-4 text-muted mb-3"></i>
                <h5 class="text-secondary">Không tìm thấy sản phẩm nào</h5>
                <p class="text-muted small">Hãy thử tìm kiếm với từ khóa khác hoặc quay lại trang chủ.</p>
                <div>
                    <a href="<?= $baseUrl ?>" class="btn btn-dark btn-sm px-3 py-2 mt-2">Về trang chủ</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($list as $p): ?>
                <?php include __DIR__ . "/../layouts/product-card.php"; ?>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <?php
                    $params = $_GET;
                    for ($i = 1; $i <= $totalPages; $i++):
                        $params['page'] = $i;
                        $url = $baseUrl . '/index.php?' . http_build_query($params);
                ?>
                <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link <?= $i === ($page ?? 1) ? 'bg-dark text-white border-dark' : 'text-dark border' ?>" href="<?= htmlspecialchars($url) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
