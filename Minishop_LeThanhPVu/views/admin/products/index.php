<?php
$pageTitle = "Danh sách sản phẩm";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
$dao = new ProductDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php"); exit;
}

$keyword = trim($_GET['keyword'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');
$limit = max(1, (int)($_GET['limit'] ?? 10));
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalRecords = $dao->count("products", "proname", $keyword);
$totalPages = (int)ceil($totalRecords / $limit);

$list = $dao->getPage($limit, $offset, $keyword, $sort);

$sortOptions = [
    'default' => 'Mới nhất',
    'name_asc' => 'Tên A-Z',
    'name_desc' => 'Tên Z-A',
    'price_asc' => 'Giá tăng dần',
    'price_desc' => 'Giá giảm dần'
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH SẢN PHẨM</h4>
    <a href="create.php" class="btn btn-success btn-sm">+ Thêm sản phẩm</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>STT</th>
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Thương hiệu</th>
            <th>Giá gốc</th>
            <th>Giá bán</th>
            <th>SL</th>
            <th>Trạng thái</th>
            <th width="180">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php $stt = $offset + 1; foreach ($list as $p): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td>
                <?php if (!empty($p['image'])) { ?>
                    <img src="../../../uploads/products/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['proname']) ?>" class="img-thumbnail" width="80">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
            <td class="fw-bold"><?= htmlspecialchars($p['proname']) ?></td>
            <td><span class="badge bg-primary"><?= htmlspecialchars($p['catename']) ?></span></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['brandname']) ?></span></td>
            <td class="text-muted text-decoration-line-through"><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
            <td class="text-danger fw-bold"><?= number_format($p['discount_price'], 0, ',', '.') ?> đ</td>
            <td><span class="badge bg-info text-dark"><?= $p['quantity'] ?></span></td>
            <td><span class="badge bg-<?= $p['status'] ? 'success' : 'secondary' ?>"><?= $p['status'] ? 'Còn bán' : 'Ngừng bán' ?></span></td>
            <td>
                <a href="detail.php?id=<?= $p['id'] ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?><tr><td colspan="10" class="text-center text-muted">Không tìm thấy sản phẩm nào</td></tr><?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>


