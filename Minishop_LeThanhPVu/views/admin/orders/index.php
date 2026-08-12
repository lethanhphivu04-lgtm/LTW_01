<?php
$pageTitle = "Danh sách đơn hàng";
require_once __DIR__ . "/../../../dao/OrderDAO.php";

$dao = new OrderDAO();

$keyword = trim($_GET['keyword'] ?? '');
$sort = trim($_GET['sort'] ?? 'default');
$limit = max(1, (int)($_GET['limit'] ?? 10));
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalRecords = $dao->count("orders", "order_code", $keyword);
$totalPages = (int)ceil($totalRecords / $limit);

$list = $dao->getPage($limit, $offset, $keyword, $sort);

$sortOptions = [
    'default' => 'Mới nhất',
    'code_asc' => 'Mã đơn A-Z',
    'code_desc' => 'Mã đơn Z-A',
    'amount_asc' => 'Tổng tiền tăng dần',
    'amount_desc' => 'Tổng tiền giảm dần'
];

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH ĐƠN HÀNG</h4>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>STT</th>
            <th>Mã đơn hàng</th>
            <th>Khách hàng</th>
            <th>Nhân viên xử lý</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
            <th>Trạng thái</th>
            <th width="150">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php $stt = $offset + 1; foreach ($list as $o): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold text-primary"><?= htmlspecialchars($o['order_code']) ?></td>
            <td class="fw-bold"><?= htmlspecialchars($o['customer_name'] ?? 'Khách lẻ') ?></td>
            <td><?= htmlspecialchars($o['user_name'] ?? 'Chưa phân công') ?></td>
            <td class="fw-bold text-success"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</td>
            <td><?= $o['created_at'] ?></td>
            <td>
                <?php if ($o['status'] == 1): ?>
                    <span class="badge bg-success">Đã xác nhận</span>
                <?php elseif ($o['status'] == 2): ?>
                    <span class="badge bg-info text-dark">Đang giao</span>
                <?php elseif ($o['status'] == 3): ?>
                    <span class="badge bg-primary">Hoàn thành</span>
                <?php elseif ($o['status'] == 4): ?>
                    <span class="badge bg-danger">Đã hủy</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="detail.php?id=<?= $o['id'] ?>" class="btn btn-info btn-sm text-white">Chi tiết / Xử lý</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" class="text-center text-muted">Không tìm thấy kết quả đơn hàng nào</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>


<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
