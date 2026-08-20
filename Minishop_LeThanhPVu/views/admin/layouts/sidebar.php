<?php
$currentUser = $_SESSION["user"] ?? null;
if ($currentUser instanceof \__PHP_Incomplete_Class || !is_object($currentUser)) {
    unset($_SESSION["user"]);
    $currentUser = null;
}

$currentController = $_GET['controller'] ?? 'product';

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$pos = strpos($scriptDir, '/views');
$baseUrl = ($pos !== false) ? substr($scriptDir, 0, $pos) : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/LTW_01/Minishop_LeThanhPVu';
?>
<div class="col-md-3 col-lg-2 sidebar p-3">
    <div class="nav flex-column nav-pills">
        <a class="nav-link text-white mb-2 <?= $currentController === 'dashboard' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=dashboard&action=index"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'category' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=category&action=index"><i class="bi bi-folder me-2"></i> Danh mục</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'brand' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=brand&action=index"><i class="bi bi-tag me-2"></i> Thương hiệu</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'product' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=product&action=index"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'stock' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=index"><i class="bi bi-box-arrow-in-down me-2"></i> Nhập kho</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'customer' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=customer&action=index"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <?php if ($currentUser && isset($currentUser->role) && (int)$currentUser->role === 1): ?>
            <a class="nav-link text-white mb-2 <?= $currentController === 'user' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=user&action=index"><i class="bi bi-person-badge me-2"></i> Người dùng</a>
        <?php endif; ?>
        <a class="nav-link text-white mb-2 <?= $currentController === 'order' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=order&action=index"><i class="bi bi-receipt me-2"></i> Đơn hàng</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'coupon' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=coupon&action=index"><i class="bi bi-ticket-perforated me-2"></i> Mã giảm giá</a>
        <a class="nav-link text-white mb-2 <?= $currentController === 'banner' ? 'active' : '' ?>" href="<?= $baseUrl ?>/index.php?area=admin&controller=banner&action=index"><i class="bi bi-images me-2"></i> Banner Slider</a>
    </div>
</div>
