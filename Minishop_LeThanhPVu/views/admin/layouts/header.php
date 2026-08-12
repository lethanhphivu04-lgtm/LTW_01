<?php
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../../../middleware/CsrfMiddleware.php";
AuthMiddleware::handle();
CsrfMiddleware::generateToken();



$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($scriptName, '/views/admin');
$adminUrl = ($pos !== false) ? substr($scriptName, 0, $pos + 12) : '/LeThanhPhiVu_LTW_001/Minishop_LeThanhPVu/views/admin';

$user = $_SESSION["user"] ?? null;
$displayName = $user ? htmlspecialchars($user->fullname . ($user->role ? ' (Admin)' : ' (Nhân viên)')) : 'Chưa đăng nhập';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'MiniShop Admin' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #1e293b; }
        .sidebar .nav-link { color: #94a3b8; padding: 0.75rem 1rem; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff; background-color: #334155; border-radius: 6px; }
        .stat-card { border-radius: 12px; border: none; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3 shadow-sm">
    <a class="navbar-brand fw-bold text-primary" href="<?= $adminUrl ?>/dashboard.php"><i class="bi bi-cart-fill"></i> MINI SHOP</a>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="navbar-text text-white mb-0"><i class="bi bi-person-circle fs-5 me-1"></i> <?= $displayName ?></span>
        <?php if ($user): ?>
            <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        <?php endif; ?>
    </div>
</nav>

<?php if ($user): ?>
<!-- Modal Xác nhận Đăng xuất -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white py-2">
        <h5 class="modal-title fs-6 fw-bold" id="logoutModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận đăng xuất</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-dark py-4">
        Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
        <a href="<?= $adminUrl ?>/logout.php" class="btn btn-danger btn-sm fw-bold">Đăng xuất</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


