<?php
if (!class_exists('Composers\HeaderComposer')) {
    require_once __DIR__ . "/../../../autoload.php";
}
if (session_status() === PHP_SESSION_NONE) session_start();

$headerData = \Composers\HeaderComposer::compose();
$headerCategories = $headerData['headerCategories'];
$headerBrands = $headerData['headerBrands'];

$baseUrl = '/LTW_01/Minishop_LeThanhPVu';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'MiniShop') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/client/style.css">
    <script>
        window.BASE_URL = '<?= $baseUrl ?>';
    </script>
</head>
<body>
<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-1">
    <div class="container d-flex justify-content-between align-items-center small">
        <span><i class="bi bi-telephone me-1"></i> Hotline: 0123-456-789</span>
        <span><i class="bi bi-truck me-1"></i> Miễn phí vận chuyển đơn từ 500.000đ</span>
    </div>
</div>

<!-- Header / Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="<?= $baseUrl ?>/index.php">
            <i class="bi bi-cart-fill me-1"></i>MINI SHOP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="<?= $baseUrl ?>"><i class="bi bi-house me-1"></i>Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="<?= $baseUrl ?>/products"><i class="bi bi-grid me-1"></i>Sản phẩm</a>
                </li>
                <!-- Dropdown Danh mục -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown"><i class="bi bi-folder me-1"></i>Danh mục</a>
                    <ul class="dropdown-menu">
                        <?php foreach ($headerCategories as $cat): ?>
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>/category/<?= urlencode($cat->slug) ?>"><?= htmlspecialchars($cat->name) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <!-- Dropdown Thương hiệu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown"><i class="bi bi-tag me-1"></i>Thương hiệu</a>
                    <ul class="dropdown-menu">
                        <?php foreach ($headerBrands as $brand): ?>
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>/brand/<?= urlencode($brand->slug) ?>"><?= htmlspecialchars($brand->name) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
            <!-- Search -->
            <form class="d-flex me-3" action="<?= $baseUrl ?>/products/search" method="GET">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <!-- Icons -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $baseUrl ?>/admin/login" class="text-dark fs-5" title="Đăng nhập Admin"><i class="bi bi-person-circle"></i></a>
                <?php
                $headerCartCount = 0;
                if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $cItem) {
                        $headerCartCount += (int)($cItem['quantity'] ?? 0);
                    }
                }
                ?>
                <a href="<?= $baseUrl ?>/cart" class="btn btn-outline-primary position-relative" title="Giỏ hàng">
                    <i class="bi bi-cart3"></i>
                    <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $headerCartCount ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>

