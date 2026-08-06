<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($scriptName, '/views/admin');
$adminUrl = ($pos !== false) ? substr($scriptName, 0, $pos + 12) : '/LeThanhPhiVu_LTW_001/Minishop_LeThanhPVu/views/admin';
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
    <span class="navbar-text ms-auto me-3 text-white-50"><i class="bi bi-person-circle"></i> Lê Thanh Phi Vũ (Admin)</span>
    <a href="#" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
</nav>
