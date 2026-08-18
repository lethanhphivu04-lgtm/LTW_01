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
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $path = substr($uri, strlen($scriptDir));
        $route = trim($path, '/');
    }

    $segments = $route !== '' ? explode('/', $route) : [];

    if (empty($segments) || $segments[0] === 'index.php') {
        $area = "admin";
        $controller = "product";
        $action = "index";
    } elseif ($segments[0] === 'admin') {
        $area = "admin";
        $sec = $segments[1] ?? 'product';

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
    } else {
        $area = "client";
        $controller = rtrim($segments[0], 's');
        $action = $segments[1] ?? 'index';
        if (isset($segments[2])) {
            $_GET['id'] = $segments[2];
        }
    }
}

// Giá trị mặc định
$area = $area ?? "admin";
$controller = $controller ?? "product";
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
