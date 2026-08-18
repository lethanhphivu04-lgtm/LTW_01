<?php
$pageTitle = $pageTitle ?? "Danh sách danh mục";
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH LOẠI SẢN PHẨM</h4>
    <a href="index.php?area=admin&controller=category&action=create" class="btn btn-primary btn-sm">+ Thêm loại</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Hình ảnh</th><th>Tên loại</th><th>Slug</th><th>Trạng thái</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = ($offset ?? 0) + 1; foreach ($list as $c): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td>
                <?php if (!empty($c->image)) { ?>
                    <img src="uploads/categories/<?= $c->image ?>" alt="<?= htmlspecialchars($c->name) ?>" class="img-thumbnail" width="60">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
            <td class="fw-bold"><?= htmlspecialchars($c->name) ?></td>
            <td><code><?= htmlspecialchars($c->slug) ?></code></td>
            <td><span class="badge bg-<?= $c->status ? 'success' : 'secondary' ?>"><?= $c->status ? 'Hiển thị' : 'Ẩn' ?></span></td>
            <td>
                <a href="index.php?area=admin&controller=category&action=detail&id=<?= $c->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="index.php?area=admin&controller=category&action=edit&id=<?= $c->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?area=admin&controller=category&action=delete&id=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?>
        <tr><td colspan="6" class="text-center text-muted">Không tìm thấy danh mục nào</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
