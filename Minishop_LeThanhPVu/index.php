<?php
/**
 * ============================================================================
 * MINISHOP - ĐIỂM ĐIỀU HƯỚNG TRUNG TÂM (CENTRAL FRONT CONTROLLER / ROUTER)
 * Tác giả: Lê Thanh Phi Vũ
 * Mô tả: Tiếp nhận toàn bộ Request, phân tích URL, gọi Middleware bảo mật,
 *        khởi tạo Controller tương ứng và thực thi Action.
 * ============================================================================
 */

// 1. Tự động nạp toàn bộ Class qua Autoloader
require_once __DIR__ . '/autoload.php';

// 2. Khởi tạo Session cho toàn bộ ứng dụng
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Tự động khôi phục danh sách sản phẩm yêu thích (Wishlist) từ Cookie lưu 30 ngày
if (!isset($_SESSION['wishlist']) && !empty($_COOKIE['minishop_wishlist'])) {
    $cookieWishlist = json_decode($_COOKIE['minishop_wishlist'], true);
    if (is_array($cookieWishlist)) {
        $_SESSION['wishlist'] = $cookieWishlist;
    }
}

// 4. Lấy các tham số cơ bản từ Query String (nếu có)
$area = $_GET["area"] ?? null;             // client hoặc admin
$controller = $_GET["controller"] ?? null; // Tên controller (product, order, cart...)
$action = $_GET["action"] ?? null;         // Tên hàm xử lý (index, create, detail...)

// 5. Phân tích Pretty URL (Hỗ trợ URL thân thiện không cần truyền ?area=...&controller=...)
if ($controller === null) {
    $route = trim($_GET['route'] ?? '', '/');
    
    // Nếu không có param ?route=, tự động bóc tách từ REQUEST_URI
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

    // Nhận diện phân hệ và hành động dựa theo các đoạn URL
    if (empty($segments) || $segments[0] === 'index.php') {
        // Trang chủ người dùng
        $area = "client";
        $controller = "home";
        $action = "index";
    } elseif ($segments[0] === 'admin') {
        // Phân hệ Quản trị (Admin)
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
        // Giỏ hàng
        $area = "client";
        $controller = "cart";
        $action = $segments[1] ?? "index";
        if (isset($segments[2])) {
            $_GET['id'] = $segments[2];
        }
    } elseif ($segments[0] === 'category' || $segments[0] === 'danh-muc') {
        // Xem theo danh mục
        $area = "client";
        $controller = "product";
        $action = "category";
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
    } elseif ($segments[0] === 'brand' || $segments[0] === 'thuong-hieu') {
        // Xem theo thương hiệu
        $area = "client";
        $controller = "product";
        $action = "brand";
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
        }
    } elseif ($segments[0] === 'product' || $segments[0] === 'products' || $segments[0] === 'san-pham') {
        // Xem danh sách hoặc chi tiết sản phẩm
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
            // Hỗ trợ đường dẫn ngắn /product/{slug}
            $action = "detail";
            $_GET['slug'] = $segments[1];
        }
    } else {
        // Các controller client khác (wishlist, chatbot...)
        $area = "client";
        $controller = rtrim($segments[0], 's');
        $action = $segments[1] ?? 'index';
        if (isset($segments[2])) {
            $_GET['id'] = $segments[2];
            $_GET['slug'] = $segments[2];
        }
    }
}

// 6. Gán giá trị mặc định an toàn nếu chưa xác định
$area = $area ?? "client";
$controller = $controller ?? "home";
$action = $action ?? "index";

// 7. KIỂM TRA BẢO MẬT BẰNG MIDDLEWARE
// Chặn người lạ truy cập khu vực Admin (bắt buộc đăng nhập)
if ($area === "admin" && $controller !== "auth") {
    \Middleware\AuthMiddleware::handle();
}

// Nếu đã đăng nhập Admin thì không cần vào lại trang Login
if ($area === "admin" && $controller === "auth" && $action === "login") {
    \Middleware\GuestMiddleware::handle();
}

// Tự động sinh mã CSRF Token bảo vệ các Form
\Middleware\CsrfMiddleware::generateToken();

// 8. ĐIỀU PHỐI ĐẾN CONTROLLER TƯƠNG ỨNG
if ($area === "admin") {
    $controllerClass = "Controllers\\Admin\\" . ucfirst($controller) . "Controller";
} else {
    $controllerClass = "Controllers\\Client\\" . ucfirst($controller) . "Controller";
}

// Kiểm tra sự tồn tại của Controller
if (!class_exists($controllerClass)) {
    die("Lỗi 404: Controller không tồn tại -> " . htmlspecialchars($controllerClass));
}

// Khởi tạo đối tượng Controller
$controllerObject = new $controllerClass();

// Kiểm tra sự tồn tại của Action (Phương thức)
if (!method_exists($controllerObject, $action)) {
    die("Lỗi 404: Action không tồn tại -> " . htmlspecialchars($action));
}

// Thực thi hành động và hiển thị kết quả cho người dùng
$controllerObject->$action();
