<?php
namespace Services;

use Config\VNPayConfig;

/**
 * Service tích hợp Cổng thanh toán trực tuyến VNPay
 * Xử lý tạo link thanh toán (Chữ ký số SHA512) và xác thực dữ liệu phản hồi từ VNPay.
 */
class VNPayService
{
    /**
     * Tạo đường dẫn URL chuyển tiếp khách sang cổng thanh toán VNPay
     * 
     * @param string $orderCode Mã đơn hàng duy nhất (vnp_TxnRef)
     * @param float $totalAmount Tổng số tiền thanh toán (VNĐ)
     * @param string $orderInfo Nội dung thanh toán
     * @param string $ipAddr Địa chỉ IP của khách hàng
     * @return string URL thanh toán có chữ ký bảo mật SHA512
     */
    public static function createPaymentUrl(string $orderCode, float $totalAmount, string $orderInfo, string $ipAddr): string
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TmnCode = VNPayConfig::$vnp_TmnCode;
        $vnp_HashSecret = VNPayConfig::$vnp_HashSecret;
        $vnp_Url = VNPayConfig::$vnp_Url;

        // Chuẩn hóa IPv6 localhost (::1) về IPv4 (127.0.0.1)
        if ($ipAddr === '::1' || empty($ipAddr)) {
            $ipAddr = '127.0.0.1';
        }

        // Tự động tính toán đường dẫn ReturnUrl trả về kết quả
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = $scheme . '://' . $host . $scriptDir;
        $vnp_ReturnUrl = rtrim($baseUrl, '/') . '/cart/vnpay_return';

        // 1. Tập hợp các tham số gửi sang VNPay
        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => (int)($totalAmount * 100), // VNPay quy định số tiền nhân với 100
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_ExpireDate" => date('YmdHis', strtotime('+30 minutes')),
            "vnp_IpAddr"     => $ipAddr,
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => $orderInfo,
            "vnp_OrderType"  => "other",
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $orderCode,
        ];

        // 2. Sắp xếp mảng tham số theo thứ tự bảng chữ cái A-Z (Bắt buộc theo chuẩn VNPay)
        ksort($inputData);

        // 3. Xây dựng chuỗi dữ liệu Hash và Query String
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

        // 4. Tạo chữ ký bảo mật HMAC-SHA512
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        return $paymentUrl;
    }

    /**
     * Xác thực tính toàn vẹn của dữ liệu trả về từ VNPay (Chống làm giả kết quả giao dịch)
     * 
     * @param array $vnpData Mảng dữ liệu $_GET trả về từ VNPay
     * @return array [success: bool, orderCode: string, amount: float, message: string]
     */
    public static function verifyReturn(array $vnpData): array
    {
        $vnp_HashSecret = VNPayConfig::$vnp_HashSecret;
        $vnp_SecureHash = $vnpData['vnp_SecureHash'] ?? '';

        // Xóa trường chữ ký trước khi băm kiểm tra
        unset($vnpData['vnp_SecureHash']);
        unset($vnpData['vnp_SecureHashType']);

        ksort($vnpData);
        $hashData = "";
        $i = 0;
        foreach ($vnpData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Tính lại mã băm bằng khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra chữ ký có khớp nhau không
        if ($secureHash !== $vnp_SecureHash) {
            return [
                'success'   => false,
                'orderCode' => $vnpData['vnp_TxnRef'] ?? '',
                'amount'    => 0,
                'message'   => 'Chữ ký VNPay không hợp lệ (Dữ liệu có dấu hiệu bị can thiệp).'
            ];
        }

        // Kiểm tra mã kết quả giao dịch: '00' là Thành công
        $responseCode = $vnpData['vnp_ResponseCode'] ?? '';
        $orderCode = $vnpData['vnp_TxnRef'] ?? '';
        $amount = (float)($vnpData['vnp_Amount'] ?? 0) / 100;

        if ($responseCode === '00') {
            return [
                'success'   => true,
                'orderCode' => $orderCode,
                'amount'    => $amount,
                'message'   => 'Thanh toán qua VNPay thành công!'
            ];
        }

        return [
            'success'   => false,
            'orderCode' => $orderCode,
            'amount'    => $amount,
            'message'   => 'Giao dịch VNPay thất bại hoặc bị hủy (Mã phản hồi: ' . $responseCode . ').'
        ];
    }
}
