<?php
namespace Controllers\Admin;

use DAO\UserDAO;
use Middleware\CsrfMiddleware;

class AuthController
{
    public function login()
    {
        $errors = [];
        $username = "";

        // Hiển thị form
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // Kiểm tra CSRF
        CsrfMiddleware::verify();

        // Nhận dữ liệu
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        // Validate
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

        // Tìm User
        $userDAO = new UserDAO();
        $user = $userDAO->findByUsername($username);

        // Kiểm tra tài khoản và mật khẩu
        if (!$user) {
            $errors['username'] = "Tên đăng nhập không đúng.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        if (!password_verify($password, $user->password) && $password !== $user->password) {
            $errors['password'] = "Mật khẩu không đúng.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        if ($user->status === 0) {
            $errors['username'] = "Tài khoản của bạn đã bị khóa.";
            require __DIR__ . '/../../views/admin/login.php';
            return;
        }

        // Đăng nhập thành công
        $_SESSION["user"] = $user;

        if (!empty($_POST["remember"])) {
            $rememberToken = base64_encode($user->username . ":" . md5($user->username . "MINISHOP_SECRET_KEY"));
            setcookie("remember_user", $rememberToken, time() + 7 * 24 * 3600, "/");
        } else {
            setcookie("remember_user", "", time() - 3600, "/");
        }

        header("Location: index.php?area=admin&controller=product&action=index");
        exit;
    }

    // Đăng xuất
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        setcookie("remember_user", "", time() - 3600, "/");

        header("Location: index.php?area=admin&controller=auth&action=login");
        exit;
    }
}
