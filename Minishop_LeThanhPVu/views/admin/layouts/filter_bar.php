<?php
/**
 * Top Filter Bar (Search, Sort, Limit)
 */
$sortOptions = $sortOptions ?? [
    'default' => 'Mới nhất',
    'name_asc' => 'Tên A-Z',
    'name_desc' => 'Tên Z-A',
    'id_asc' => 'Cũ nhất'
];
?>
<form method="GET" class="row g-2 mb-3 align-items-center">
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa tìm kiếm..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-primary" type="submit">Tìm kiếm</button>
            <?php if ($keyword !== ''): ?>
                <a href="index.php" class="btn btn-outline-secondary">Xóa lọc</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="me-2 text-nowrap">Sắp xếp:</label>
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($sortOptions as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $sort === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="me-2 text-nowrap">Hiển thị:</label>
            <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ([10, 20, 30, 50] as $l): ?>
                    <option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?> / trang</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>
