<?php
namespace Middleware;

use DAO\UserDAO;

class AuthMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION["user"]) && ($_SESSION["user"] instanceof \__PHP_Incomplete_Class || !is_object($_SESSION["user"]))) {
            unset($_SESSION["user"]);
        }

        // Tự động khôi phục Session từ Cookie "Remember Me"
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

        if (!isset($_SESSION["user"])) {
            header("Location: index.php?area=admin&controller=auth&action=login");
            exit;
        }
    }
}
