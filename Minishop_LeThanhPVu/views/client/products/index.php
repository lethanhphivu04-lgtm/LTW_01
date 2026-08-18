<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <div class="text-center mb-4">
        <h2 class="section-title"><?= $heading ?? 'TẤT CẢ SẢN PHẨM' ?></h2>
        <p class="text-secondary small mt-2"><?= $totalRecords ?? 0 ?> sản phẩm có sẵn</p>
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
