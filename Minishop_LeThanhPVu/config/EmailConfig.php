<?php
namespace Config;

/**
 * Cấu hình thông số máy chủ gửi thư điện tử Gmail SMTP
 */
class EmailConfig
{
    // Máy chủ gửi mail SMTP của Google
    public static string $host = 'smtp.gmail.com';
    public static int $port = 587;                                         // Cổng mã hóa TLS
    public static string $smtpSecure = 'tls';                              // Giao thức bảo mật kết nối
    
    // Thông tin tài khoản gửi email tự động
    public static string $username = 'lethanhphivu04@gmail.com';           // Địa chỉ Gmail gửi thông báo
    public static string $password = 'hjoldwngrzexvohi';                  // Mật khẩu ứng dụng (App Password 16 ký tự của Google)
    public static string $fromName = 'MiniShop - Thông báo đơn hàng';      // Tên hiển thị người gửi
}
