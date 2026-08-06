<?php
$pageTitle = "Cập nhật khách hàng";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";
$dao = new CustomerDAO();
$errors = [];
$id = (int)($_GET['id'] ?? 0);
$cust = $dao->findById($id);
if (!$cust) { header("Location: index.php"); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($fullname === '') $errors[] = 'Họ tên không được để trống';
    if ($phone === '') $errors[] = 'Số điện thoại không được để trống';
    if (empty($errors)) {
        $cust->fullname = $fullname; $cust->phone = $phone; $cust->email = $email; $cust->address = $address;
        $dao->update($cust);
        header("Location: index.php"); exit;
    }
} else {
    $fullname = $cust->fullname; $phone = $cust->phone; $email = $cust->email; $address = $cust->address;
}
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT KHÁCH HÀNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($fullname) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại <span class="text-danger">*</span></label><input name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Địa chỉ</label><input name="address" class="form-control" value="<?= htmlspecialchars($address ?? '') ?>"></div>
        <div class="col-12"><button class="btn btn-success">Cập nhật</button> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
