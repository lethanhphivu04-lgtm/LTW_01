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
    // xem tren dt//
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'MiniShop') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/client/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/client/chatbot.css?v=<?= time() ?>">
    <script>
        window.BASE_URL = '<?= $baseUrl ?>';
    </script>
</head>
<body>
<!-- Top Bar -->
<div class="top-bar py-1" style="background-color: #090d16 !important; color: #94a3b8 !important; border-bottom: 1px solid rgba(255,255,255,0.08);">
    <div class="container d-flex justify-content-between align-items-center small">
        <span><i class="bi bi-telephone me-1"></i> Hotline: 0123-456-789</span>
        <span><i class="bi bi-shield-check me-1"></i> Sản phẩm chính hãng 100% | Bảo hành 12 tháng</span>
    </div>
</div>

<!-- Header / Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #0f172a !important; border-bottom: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
        <a class="navbar-brand fs-4 text-white fw-bold" href="<?= $baseUrl ?>">
            <i class="bi bi-cpu me-2"></i>MINI<span style="color: #94a3b8;">SHOP</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>"><i class="bi bi-house me-1"></i>Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>/products"><i class="bi bi-grid me-1"></i>Sản phẩm</a>
                </li>
                <!-- Dropdown Danh mục -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-folder me-1"></i>Danh mục</a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow">
                        <?php foreach ($headerCategories as $cat): ?>
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>/category/<?= urlencode($cat->slug) ?>"><?= htmlspecialchars($cat->name) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <!-- Dropdown Thương hiệu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-tag me-1"></i>Thương hiệu</a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow">
                        <?php foreach ($headerBrands as $brand): ?>
                        <li><a class="dropdown-item" href="<?= $baseUrl ?>/brand/<?= urlencode($brand->slug) ?>"><?= htmlspecialchars($brand->name) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <!-- Tra cứu đơn hàng -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>/index.php?area=client&controller=cart&action=tracking"><i class="bi bi-search me-1"></i>Tra cứu đơn hàng</a>
                </li>
            </ul>
            <!-- Search -->
            <form class="d-flex me-3 header-search" action="<?= $baseUrl ?>/products/search" method="GET">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <!-- Icons -->
            <div class="d-flex align-items-center gap-3">
                <?php
                $headerWishlistCount = count($_SESSION['wishlist'] ?? []);
                $headerCartCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
                ?>
                <a href="<?= $baseUrl ?>/admin/login" class="btn btn-outline-secondary btn-sm text-white border-0" title="Đăng nhập Quản trị">
                    <i class="bi bi-person-circle fs-5"></i>
                </a>
                <a href="<?= $baseUrl ?>/index.php?area=client&controller=wishlist&action=index" class="btn btn-outline-secondary btn-sm position-relative text-white border-0" title="Sản phẩm yêu thích">
                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                    <span id="wishlistCount" class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:10px;"><?= $headerWishlistCount ?></span>
                </a>
                <a href="<?= $baseUrl ?>/cart" class="btn-cart-nav position-relative text-decoration-none" title="Giỏ hàng">
                    <i class="bi bi-cart3 me-1"></i>Giỏ hàng
                    <span id="cartCount" class="badge rounded-pill bg-danger ms-1"><?= $headerCartCount ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>
