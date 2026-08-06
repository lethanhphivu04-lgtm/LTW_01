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
$list = $keyword !== '' ? $dao->search($keyword) : $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH THƯƠNG HIỆU</h4>
    <a href="create.php" class="btn btn-info text-white btn-sm">+ Thêm thương hiệu</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input name="keyword" class="form-control" placeholder="Tìm theo tên hoặc slug..." value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-primary">Tìm kiếm</button>
        <?php if ($keyword !== ''): ?>
            <a href="index.php" class="btn btn-outline-secondary">Xóa lọc</a>
        <?php endif; ?>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Tên thương hiệu</th><th>Slug</th><th>Trạng thái</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = 1; foreach ($list as $b): ?>
        <tr>
            <td><?= $stt++ ?></td>
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
        <tr><td colspan="5" class="text-center text-muted">Không tìm thấy kết quả</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
