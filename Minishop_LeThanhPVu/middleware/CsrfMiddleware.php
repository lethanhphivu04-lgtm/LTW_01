<?php
namespace Middleware;

class CsrfMiddleware
{
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
    }

    public static function verify()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST["csrf_token"] ?? "";
        $sessionToken = $_SESSION["csrf_token"] ?? "";

        if (empty($sessionToken) || !is_string($sessionToken) || !hash_equals($sessionToken, (string)$token)) {
            http_response_code(403);
            die("CSRF Token không hợp lệ.");
        }
    }
}
