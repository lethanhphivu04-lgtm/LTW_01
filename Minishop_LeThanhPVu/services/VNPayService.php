<?php
namespace Services;

use Config\VNPayConfig;

class VNPayService
{
    /**
     * Tạo URL thanh toán VNPay
     */
    public static function createPaymentUrl(string $orderCode, float $totalAmount, string $orderInfo, string $ipAddr): string
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TmnCode = VNPayConfig::$vnp_TmnCode;
        $vnp_HashSecret = VNPayConfig::$vnp_HashSecret;
        $vnp_Url = VNPayConfig::$vnp_Url;

        // Fix IPv6 localhost → IPv4
        if ($ipAddr === '::1' || empty($ipAddr)) {
            $ipAddr = '127.0.0.1';
        }

        // Tính ReturnUrl tự động dựa trên domain hiện tại
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = $scheme . '://' . $host . $scriptDir;
        $vnp_ReturnUrl = rtrim($baseUrl, '/') . '/cart/vnpay_return';

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => (int)($totalAmount * 100), // VNPay yêu cầu đơn vị là đồng * 100
            "vnp_Command"    => "pay",
            "vnp_CreateDate"  => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_ExpireDate"  => date('YmdHis', strtotime('+30 minutes')),
            "vnp_IpAddr"     => $ipAddr,
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => $orderInfo,
            "vnp_OrderType"  => "other",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $orderCode,
        ];

        // Sắp xếp theo key alphabetically (bắt buộc)
        ksort($inputData);

        $hashData = "";
        $query = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo chữ ký bảo mật HMAC SHA512
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

        return $paymentUrl;
    }

    /**
     * Xác thực dữ liệu trả về từ VNPay
     * @return array ['isValid' => bool, 'isSuccess' => bool, 'message' => string]
     */
    public static function validateReturn(array $vnpData): array
    {
        $vnp_HashSecret = VNPayConfig::$vnp_HashSecret;

        // Lấy SecureHash từ response
        $vnp_SecureHash = $vnpData['vnp_SecureHash'] ?? '';
        // Xóa các trường hash trước khi tính toán lại
        unset($vnpData['vnp_SecureHash'], $vnpData['vnp_SecureHashType']);

        ksort($vnpData);

        $hashData = "";
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if (str_starts_with($key, "vnp_")) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            return ['isValid' => false, 'isSuccess' => false, 'message' => 'Chữ ký không hợp lệ.'];
        }

        $responseCode = $vnpData['vnp_ResponseCode'] ?? '99';
        if ($responseCode === '00') {
            return ['isValid' => true, 'isSuccess' => true, 'message' => 'Thanh toán thành công.'];
        }

        return ['isValid' => true, 'isSuccess' => false, 'message' => 'Thanh toán không thành công. Mã lỗi: ' . $responseCode];
    }
}
