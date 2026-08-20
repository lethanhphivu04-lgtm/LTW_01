<?php
namespace Services;

use Config\EmailConfig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Service gửi Email thông báo tự động qua giao thức Gmail SMTP
 */
class EmailService
{
    /**
     * Gửi email xác nhận đặt hàng thành công kèm chi tiết hóa đơn đến khách hàng
     */
    public static function sendOrderConfirmation(
        string $toEmail,
        string $customerName,
        string $orderCode,
        array $items,
        float $totalAmount,
        string $address,
        string $phone,
        string $paymentMethod = 'cod'
    ): bool {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            $mail = new PHPMailer(true);

            // 1. Cấu hình thông số máy chủ SMTP
            $mail->isSMTP();
            $mail->Host       = EmailConfig::$host;
            $mail->SMTPAuth   = true;
            $mail->Username   = EmailConfig::$username;
            $mail->Password   = EmailConfig::$password;
            $mail->SMTPSecure = (EmailConfig::$smtpSecure === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = EmailConfig::$port;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 10; // Giới hạn 10s tránh làm chậm trải nghiệm

            // 2. Thiết lập Người gửi & Người nhận
            $mail->setFrom(EmailConfig::$username, EmailConfig::$fromName);
            $mail->addAddress($toEmail, $customerName);

            // 3. Tiêu đề & Định dạng nội dung HTML
            $mail->isHTML(true);
            $mail->Subject = "📦 [MiniShop] Xác nhận đơn hàng #" . $orderCode;

            $paymentText = (strtolower($paymentMethod) === 'vnpay') 
                ? '<span style="color:#0d6efd;font-weight:bold;">Thanh toán Online qua VNPay (Đã thanh toán)</span>' 
                : '<span style="color:#198754;font-weight:bold;">Thanh toán khi nhận hàng (COD)</span>';

            // 4. Tạo bảng chi tiết danh sách sản phẩm
            $itemsHtml = '';
            $stt = 1;
            foreach ($items as $item) {
                $name = htmlspecialchars($item['proname'] ?? $item['productname'] ?? 'Sản phẩm');
                $qty = (int)($item['quantity'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                $subtotal = $qty * $price;
                $itemsHtml .= "
                    <tr>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$stt}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>{$name}</strong></td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$qty}</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>" . number_format($price, 0, ',', '.') . " đ</td>
                        <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold; color: #dc3545;'>" . number_format($subtotal, 0, ',', '.') . " đ</td>
                    </tr>
                ";
                $stt++;
            }

            // 5. Khung giao diện Email phong cách hiện đại
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 650px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                <div style='background: #0f172a; color: #ffffff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0; font-size: 24px; letter-spacing: 1px;'>MINISHOP</h2>
                    <p style='margin: 5px 0 0; color: #94a3b8; font-size: 13px;'>Cảm ơn quý khách đã đặt hàng!</p>
                </div>
                
                <div style='padding: 24px;'>
                    <p style='font-size: 15px; color: #333;'>Xin chào <strong>" . htmlspecialchars($customerName) . "</strong>,</p>
                    <p style='font-size: 14px; color: #555; line-height: 1.5;'>Đơn hàng <strong>#{$orderCode}</strong> của quý khách đã được tiếp nhận thành công vào hệ thống. Chúng tôi đang xử lý và chuẩn bị đóng gói giao đến quý khách.</p>
                    
                    <div style='background: #f8fafc; border-left: 4px solid #0f172a; padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #475569;'>
                        <p style='margin: 4px 0;'><strong>Số điện thoại:</strong> " . htmlspecialchars($phone) . "</p>
                        <p style='margin: 4px 0;'><strong>Địa chỉ giao:</strong> " . htmlspecialchars($address) . "</p>
                        <p style='margin: 4px 0;'><strong>Hình thức thanh toán:</strong> {$paymentText}</p>
                    </div>

                    <h4 style='color: #0f172a; margin-bottom: 10px; border-bottom: 2px solid #0f172a; padding-bottom: 5px;'>CHI TIẾT ĐƠN HÀNG</h4>
                    <table style='width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px;'>
                        <thead>
                            <tr style='background: #f1f5f9; color: #334155;'>
                                <th style='padding: 8px; text-align: center;'>STT</th>
                                <th style='padding: 8px; text-align: left;'>Sản phẩm</th>
                                <th style='padding: 8px; text-align: center;'>SL</th>
                                <th style='padding: 8px; text-align: right;'>Đơn giá</th>
                                <th style='padding: 8px; text-align: right;'>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='4' style='padding: 12px 10px; text-align: right; font-size: 15px; font-weight: bold; border-top: 2px solid #0f172a;'>TỔNG THANH TOÁN:</td>
                                <td style='padding: 12px 10px; text-align: right; font-size: 16px; font-weight: bold; color: #dc3545; border-top: 2px solid #0f172a;'>" . number_format($totalAmount, 0, ',', '.') . " đ</td>
                            </tr>
                        </tfoot>
                    </table>

                    <p style='font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 20px;'>
                        Nếu có bất kỳ thắc mắc nào, quý khách vui lòng liên hệ Hotline <strong>0123-456-789</strong> hoặc tra cứu tiến độ đơn hàng trên website.
                    </p>
                </div>

                <div style='background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; padding: 15px; border-top: 1px solid #e2e8f0;'>
                    &copy; 2026 MiniShop. Mọi quyền được bảo lưu.
                </div>
            </div>
            ";

            $mail->Body = $body;
            return $mail->send();
        } catch (\Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
}
