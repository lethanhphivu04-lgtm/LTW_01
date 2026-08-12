<?php
/**
 * Bottom Pagination Nav Links
 */
if ($totalPages > 1): 
    $queryString = function($p) use ($limit, $keyword, $sort) {
        return '?' . http_build_query([
            'limit' => $limit,
            'keyword' => $keyword,
            'sort' => $sort,
            'page' => $p
        ]);
    };
?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <!-- Nút Đầu -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $queryString(1) ?>">Đầu</a>
        </li>
        <!-- Nút Trước -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $queryString(max(1, $page - 1)) ?>">Trước</a>
        </li>

        <!-- Đánh số trang -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= $queryString($i) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Nút Sau -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $queryString(min($totalPages, $page + 1)) ?>">Sau</a>
        </li>
        <!-- Nút Cuối -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $queryString($totalPages) ?>">Cuối</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
