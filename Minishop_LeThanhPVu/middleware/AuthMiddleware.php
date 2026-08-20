<?php
namespace Middleware;

use DAO\UserDAO;

/**
 * Middleware kiểm tra trạng thái Đăng nhập vào trang Quản trị (Admin Authentication)
 */
class AuthMiddleware
{
    /**
     * Chặn các yêu cầu chưa xác thực và chuyển hướng về trang đăng nhập
     */
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Xóa Session rác nếu object bị lỗi do chưa load class
        if (isset($_SESSION["user"]) && ($_SESSION["user"] instanceof \__PHP_Incomplete_Class || !is_object($_SESSION["user"]))) {
            unset($_SESSION["user"]);
        }

        // 1. Tự động khôi phục phiên đăng nhập từ Cookie "Ghi nhớ mật khẩu" (Remember Me)
        if (!isset($_SESSION["user"]) && !empty($_COOKIE["remember_user"])) {
            $parts = explode(":", base64_decode($_COOKIE["remember_user"]) ?? '');
            if (count($parts) === 2) {
                $username = $parts[0];
                $expectedHash = md5($username . "MINISHOP_SECRET_KEY");
                if (hash_equals($expectedHash, $parts[1])) {
                    $userDAO = new UserDAO();
                    $user = $userDAO->findByUsername($username);
                    if ($user) {
                        $_SESSION["user"] = $user;
                    }
                }
            }
        }

        // 2. Nếu vẫn chưa đăng nhập -> Chuyển hướng ngay về form Login
        if (!isset($_SESSION["user"])) {
            header("Location: index.php?area=admin&controller=auth&action=login");
            exit;
        }
    }
}
