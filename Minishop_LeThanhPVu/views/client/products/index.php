<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <h2 class="section-title text-center mb-4"><?= $heading ?? 'SẢN PHẨM' ?></h2>
    <p class="text-center text-muted mb-4"><?= $totalRecords ?? 0 ?> sản phẩm</p>

    <?php if (empty($list)): ?>
        <div class="alert alert-info text-center"><i class="bi bi-info-circle me-2"></i>Không tìm thấy sản phẩm nào.</div>
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
                    // Build base URL preserving current params
                    $params = $_GET;
                    for ($i = 1; $i <= $totalPages; $i++):
                        $params['page'] = $i;
                        $url = $baseUrl . '/index.php?' . http_build_query($params);
                ?>
                <li class="page-item <?= $i === ($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($url) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
