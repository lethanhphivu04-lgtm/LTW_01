<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($scriptName, '/views/admin');
$adminUrl = ($pos !== false) ? substr($scriptName, 0, $pos + 12) : '/LeThanhPhiVu_LTW_001/Minishop_LeThanhPVu/views/admin';

$currentUser = $_SESSION["user"] ?? null;
?>
<div class="col-md-3 col-lg-2 sidebar p-3">
    <div class="nav flex-column nav-pills">
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/categories/index.php"><i class="bi bi-folder me-2"></i> Danh mục</a>
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/brands/index.php"><i class="bi bi-tag me-2"></i> Thương hiệu</a>
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/products/index.php"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/customers/index.php"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <?php if ($currentUser && (int)$currentUser->role === 1): ?>
            <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/users/index.php"><i class="bi bi-person-badge me-2"></i> Người dùng</a>
        <?php endif; ?>
        <a class="nav-link text-white mb-2" href="<?= $adminUrl ?>/orders/index.php"><i class="bi bi-receipt me-2"></i> Đơn hàng</a>
    </div>
</div>

