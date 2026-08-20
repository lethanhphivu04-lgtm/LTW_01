<?php
namespace Middleware;

/**
 * Middleware phòng chống tấn công giả mạo yêu cầu qua trang chéo (Cross-Site Request Forgery - CSRF)
 */
class CsrfMiddleware
{
    /**
     * Tự động sinh mã bảo mật CSRF Token 64 ký tự ngẫu nhiên vào Session nếu chưa có
     */
    public static function generateToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Kiểm tra tính hợp lệ của Token gửi lên từ Form người dùng bằng hash_equals (chống tấn công định thời gian Timing Attack)
     */
    public static function verify()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST["csrf_token"] ?? "";
        $sessionToken = $_SESSION["csrf_token"] ?? "";

        if (empty($sessionToken) || !is_string($sessionToken) || !hash_equals($sessionToken, (string)$token)) {
            http_response_code(403);
            die("Lỗi bảo mật: CSRF Token không hợp lệ hoặc đã hết hạn.");
        }
    }
}
