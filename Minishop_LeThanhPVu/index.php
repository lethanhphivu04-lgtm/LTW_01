<?php
require_once __DIR__ . '/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý Route từ URL (hỗ trợ cả Pretty URL từ .htaccess và Query Params)
$area = $_GET["area"] ?? null;
$controller = $_GET["controller"] ?? null;
$action = $_GET["action"] ?? null;

// Nếu không truyền qua query string, phân tích qua route hoặc REQUEST_URI
if ($controller === null) {
    $route = trim($_GET['route'] ?? '', '/');
    
    // Nếu không có ?route=, thử lấy từ REQUEST_URI
    if ($route === '') {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $path = substr($uri, strlen($scriptDir));
        } else {
            $path = $uri;
        }
        $route = trim($path, '/');
    }

    if (str_starts_with($route, 'index.php/')) {
        $route = substr($route, 10);
    }

    $segments = $route !== '' ? explode('/', $route) : [];

    if (empty($segments) || $segments[0] === 'index.php') {
        $area = "client";
        $controller = "home";
        $action = "index";
    } elseif ($segments[0] === 'admin') {
        $area = "admin";
        $sec = $segments[1] ?? 'dashboard';

        if ($sec === 'login') {
            $controller = "auth";
            $action = "login";
        } elseif ($sec === 'logout') {
            $controller = "auth";
            $action = "logout";
        } elseif ($sec === 'dashboard') {
            $controller = "dashboard";
            $action = $segments[2] ?? "index";
        } else {
            // Chuẩn hóa tên controller (bỏ đuôi s nếu có, ví dụ products -> product)
            $controller = rtrim($sec, 's');
            if ($controller === 'categorie') $controller = 'category';
            
            $action = $segments[2] ?? 'index';
            if (isset($segments[3])) {
                $_GET['id'] = $segments[3];
            }
        }
    } elseif ($segments[0] === 'cart') {
        $area = "client";
        $controller = "cart";
        $action = $segments[1] ?? "index";
        if (isset($segments[2])) {
            $_GET['id'] = $segments[2];
        }
    } elseif ($segments[0] === 'category' || $segments[0] === 'danh-muc') {
        $area = "client";
        $controller = "product";
        $action = "category";
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
    } elseif ($segments[0] === 'brand' || $segments[0] === 'thuong-hieu') {
        $area = "client";
        $controller = "product";
        $action = "brand";
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
    } elseif ($segments[0] === 'product' || $segments[0] === 'products' || $segments[0] === 'san-pham') {
        $area = "client";
        $controller = "product";
        if (empty($segments[1])) {
            $action = "index";
        } elseif (in_array($segments[1], ['index', 'category', 'brand', 'detail', 'search'])) {
            $action = $segments[1];
            if (isset($segments[2])) {
                $_GET['slug'] = $segments[2];
                $_GET['id'] = $segments[2];
            }
        } else {
            // Hỗ trợ đường dẫn /product/{slug}
            $action = "detail";
            $_GET['slug'] = $segments[1];
        }
    } else {
        $area = "client";
        $controller = rtrim($segments[0], 's');
        $action = $segments[1] ?? 'index';
        if (isset($segments[2])) {
            $_GET['id'] = $segments[2];
            $_GET['slug'] = $segments[2];
        }
    }
}

// Giá trị mặc định
$area = $area ?? "client";
$controller = $controller ?? "home";
$action = $action ?? "index";

// *** Kiểm tra Authentication cho Admin
if ($area === "admin" && $controller !== "auth") {
    \Middleware\AuthMiddleware::handle();
}

// *** Kiểm tra Guest (nếu đã đăng nhập thì không vào login nữa)
if ($area === "admin" && $controller === "auth" && $action === "login") {
    \Middleware\GuestMiddleware::handle();
}

// *** Tạo CSRF Token nếu là form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    \Middleware\CsrfMiddleware::generateToken();
}

// Xác định class Controller theo Area
if ($area === "admin") {
    $controllerClass = "Controllers\\Admin\\" . ucfirst($controller) . "Controller";
} else {
    $controllerClass = "Controllers\\Client\\" . ucfirst($controller) . "Controller";
}

// Kiểm tra Controller tồn tại
if (!class_exists($controllerClass)) {
    die("Controller không tồn tại: " . htmlspecialchars($controllerClass));
}

// Tạo đối tượng Controller
$controllerObject = new $controllerClass();

// Kiểm tra Action tồn tại
if (!method_exists($controllerObject, $action)) {
    die("Action không tồn tại: " . htmlspecialchars($action));
}

// Thực thi Action
$controllerObject->$action();
