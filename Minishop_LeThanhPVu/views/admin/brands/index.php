<?php
$pageTitle = "Danh sách thương hiệu";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
$dao = new BrandDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

$keyword = trim($_GET['keyword'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');
$limit = max(1, (int)($_GET['limit'] ?? 10));
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalRecords = $dao->count("brands", "brandname", $keyword);
$totalPages = (int)ceil($totalRecords / $limit);

$list = $dao->getPage($limit, $offset, $keyword, $sort);

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH THƯƠNG HIỆU</h4>
    <a href="create.php" class="btn btn-info text-white btn-sm">+ Thêm thương hiệu</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Hình ảnh</th><th>Tên thương hiệu</th><th>Slug</th><th>Trạng thái</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = $offset + 1; foreach ($list as $b): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td>
                <?php if (!empty($b->image)) { ?>
                    <img src="../../../uploads/brands/<?= $b->image ?>" alt="<?= htmlspecialchars($b->name) ?>" class="img-thumbnail" width="60">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
            <td class="fw-bold"><?= htmlspecialchars($b->name) ?></td>
            <td><code><?= htmlspecialchars($b->slug) ?></code></td>
            <td><span class="badge bg-<?= $b->status ? 'success' : 'secondary' ?>"><?= $b->status ? 'Hiển thị' : 'Ẩn' ?></span></td>
            <td>
                <a href="detail.php?id=<?= $b->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="edit.php?id=<?= $b->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $b->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?>
        <tr><td colspan="6" class="text-center text-muted">Không tìm thấy thương hiệu nào</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>


<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
