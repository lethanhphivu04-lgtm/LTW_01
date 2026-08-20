<?php
namespace Controllers\Admin;

use DAO\UserDAO;
use Middleware\CsrfMiddleware;

/**
 * Controller xử lý Đăng nhập & Đăng xuất hệ thống Quản trị (Admin Auth)
 */
class AuthController
{
    /**
     * Xử lý Đăng nhập Admin
     */
    public function login()
    {
        $errors = [];
        $username = "";

        // 1. Nếu là yêu cầu GET -> Hiển thị form đăng nhập
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // 2. Kiểm tra mã bảo mật CSRF chống giả mạo request
        CsrfMiddleware::verify();

        // 3. Nhận dữ liệu từ Form
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        // 4. Kiểm tra hợp lệ dữ liệu nhập
        if ($username === "") {
            $errors['username'] = "Vui lòng nhập tên đăng nhập.";
        }
        if ($password === "") {
            $errors['password'] = "Vui lòng nhập mật khẩu.";
        }

        if (!empty($errors)) {
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // 5. Tìm tài khoản trong Cơ sở dữ liệu
        $userDAO = new UserDAO();
        $user = $userDAO->findByUsername($username);

        if (!$user) {
            $errors['username'] = "Tên đăng nhập không đúng.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // 6. Kiểm tra mật khẩu (hỗ trợ cả hash bcrypt và text thuần cho môi trường lab)
        if (!password_verify($password, $user->password) && $password !== $user->password) {
            $errors['password'] = "Mật khẩu không đúng.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // 7. Kiểm tra trạng thái tài khoản
        if ($user->status === 0) {
            $errors['username'] = "Tài khoản của bạn đã bị khóa.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // 8. Đăng nhập thành công -> Lưu thông tin vào Session
        $_SESSION["user"] = $user;

        // 9. Xử lý ghi nhớ đăng nhập (Remember Me) bằng Cookie mã hóa 30 ngày
        if (isset($_POST["remember"])) {
            $hash = md5($username . "MINISHOP_SECRET_KEY");
            $cookieVal = base64_encode($username . ":" . $hash);
            setcookie("remember_user", $cookieVal, time() + (86400 * 30), "/");
        } else {
            if (isset($_COOKIE["remember_user"])) {
                setcookie("remember_user", "", time() - 3600, "/");
            }
        }

        // Chuyển hướng vào trang Dashboard quản trị
        header("Location: index.php?area=admin&controller=dashboard&action=index");
        exit;
    }

    /**
     * Xử lý Đăng xuất Admin & Xóa Session + Cookie
     */
    public function logout()
    {
        unset($_SESSION["user"]);
        if (isset($_COOKIE["remember_user"])) {
            setcookie("remember_user", "", time() - 3600, "/");
        }
        header("Location: index.php?area=admin&controller=auth&action=login");
        exit;
    }
}
