<?php
namespace Middleware;

/**
 * Middleware dành cho Khách chưa đăng nhập
 * (Nếu người dùng đã đăng nhập rồi thì không cho vào lại trang Login mà chuyển thẳng vào trang Quản trị).
 */
class GuestMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["user"])) {
            header("Location: index.php?area=admin&controller=product&action=index");
            exit;
        }
    }
}
