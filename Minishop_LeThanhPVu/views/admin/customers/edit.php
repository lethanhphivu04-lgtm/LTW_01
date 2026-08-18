<?php
$pageTitle = $pageTitle ?? "Cập nhật khách hàng";
$errors = $errors ?? [];
$id = $customer->id ?? 0;
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT KHÁCH HÀNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" action="index.php?area=admin&controller=customer&action=edit&id=<?= $id ?>" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($_POST['fullname'] ?? $customer->fullname) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại <span class="text-danger">*</span></label><input name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? $customer->phone) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $customer->email ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Địa chỉ</label><input name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? $customer->address ?? '') ?>"></div>
        <div class="col-12"><button class="btn btn-success" type="submit">Cập nhật</button> <a href="index.php?area=admin&controller=customer&action=index" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
