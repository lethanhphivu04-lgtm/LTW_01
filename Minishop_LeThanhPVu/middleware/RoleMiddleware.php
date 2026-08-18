<?php
namespace Middleware;

use Models\User;

class RoleMiddleware
{
    public static function requireAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION["user"] ?? null;
        if ($user instanceof \__PHP_Incomplete_Class || !is_object($user)) {
            unset($_SESSION["user"]);
            $user = null;
        }

        if (!$user || !isset($user->role) || (int)$user->role !== 1) {
            http_response_code(403);
            die("
            <div style='font-family: system-ui, sans-serif; max-width: 500px; margin: 80px auto; text-align: center; padding: 30px; border-radius: 12px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid #fee2e2;'>
                <div style='font-size: 48px; color: #dc3545; margin-bottom: 16px;'>🚫</div>
                <h3 style='color: #991b1b; margin-bottom: 12px; font-weight: 700;'>403 - TỪ CHỐI TRUY CẬP</h3>
                <p style='color: #4b5563; line-height: 1.6; margin-bottom: 24px;'>Tài khoản của bạn không có quyền Admin để thực hiện hoặc xem chức năng này.</p>
                <a href='index.php?area=admin&controller=product&action=index' style='display: inline-block; padding: 10px 24px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600;'>Quay lại Quản trị</a>
            </div>
            ");
        }
    }
}
