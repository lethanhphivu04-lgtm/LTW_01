<?php
$pageTitle = "Danh sách người dùng";
require_once __DIR__ . "/../../../middleware/RoleMiddleware.php";
RoleMiddleware::requireAdmin();
require_once __DIR__ . "/../../../dao/UserDAO.php";

$dao = new UserDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php"); exit;
}

$keyword = trim($_GET['keyword'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');
$limit = max(1, (int)($_GET['limit'] ?? 10));
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalRecords = $dao->count("users", "fullname", $keyword);
$totalPages = (int)ceil($totalRecords / $limit);

$list = $dao->getPage($limit, $offset, $keyword, $sort);

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH NGƯỜI DÙNG</h4>
    <a href="create.php" class="btn btn-primary btn-sm">+ Thêm tài khoản</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Họ tên</th><th>Username</th><th>Email</th><th>Vai trò</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = $offset + 1; foreach ($list as $u): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold"><?= htmlspecialchars($u->fullname) ?></td>
            <td><code><?= htmlspecialchars($u->username) ?></code></td>
            <td><?= htmlspecialchars($u->email) ?></td>
            <td><span class="badge bg-<?= $u->role ? 'danger' : 'info text-dark' ?>"><?= $u->role ? 'Quản trị' : 'Nhân viên' ?></span></td>
            <td>
                <a href="detail.php?id=<?= $u->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="edit.php?id=<?= $u->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $u->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted">Không tìm thấy người dùng nào</td></tr><?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>


