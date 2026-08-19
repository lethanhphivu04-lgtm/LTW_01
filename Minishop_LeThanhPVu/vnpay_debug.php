<?php
/**
 * Script debug VNPay - kiểm tra URL thanh toán được tạo ra
 */
require_once __DIR__ . '/autoload.php';

// Force timezone Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

use Config\VNPayConfig;

$vnp_TmnCode = VNPayConfig::$vnp_TmnCode;
$vnp_HashSecret = VNPayConfig::$vnp_HashSecret;
$vnp_Url = VNPayConfig::$vnp_Url;

echo "<h3>VNPay Debug Info</h3>";
echo "<p><strong>TmnCode:</strong> " . htmlspecialchars($vnp_TmnCode) . " (length: " . strlen($vnp_TmnCode) . ")</p>";
echo "<p><strong>HashSecret:</strong> " . htmlspecialchars($vnp_HashSecret) . " (length: " . strlen($vnp_HashSecret) . ")</p>";
echo "<p><strong>PHP Timezone:</strong> " . date_default_timezone_get() . "</p>";
echo "<p><strong>Current DateTime:</strong> " . date('YmdHis') . " (" . date('Y-m-d H:i:s') . ")</p>";
echo "<p><strong>ExpireDate (+30m):</strong> " . date('YmdHis', strtotime('+30 minutes')) . "</p>";
echo "<hr>";

// Tạo URL test
$orderCode = "TEST" . date("YmdHis");
$amount = 100000; // 100,000 VND

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = $scheme . '://' . $host . $scriptDir;
$vnp_ReturnUrl = rtrim($baseUrl, '/') . '/cart/vnpay_return';

$inputData = [
    "vnp_Version"    => "2.1.0",
    "vnp_TmnCode"    => $vnp_TmnCode,
    "vnp_Amount"     => (int)($amount * 100),
    "vnp_Command"    => "pay",
    "vnp_CreateDate"  => date('YmdHis'),
    "vnp_CurrCode"   => "VND",
    "vnp_ExpireDate"  => date('YmdHis', strtotime('+30 minutes')),
    "vnp_IpAddr"     => ($_SERVER['REMOTE_ADDR'] === '::1') ? '127.0.0.1' : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
    "vnp_Locale"     => "vn",
    "vnp_OrderInfo"  => "Test thanh toan " . $orderCode,
    "vnp_OrderType"  => "other",
    "vnp_ReturnUrl"  => $vnp_ReturnUrl,
    "vnp_TxnRef"     => $orderCode,
];

ksort($inputData);

echo "<h4>Parameters (sorted):</h4><table border='1' cellpadding='5'>";
foreach ($inputData as $k => $v) {
    echo "<tr><td><strong>$k</strong></td><td>" . htmlspecialchars($v) . "</td></tr>";
}
echo "</table>";

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

$vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
$paymentUrl = $vnp_Url . "?" . $query . "vnp_SecureHash=" . $vnpSecureHash;

echo "<hr>";
echo "<p><strong>HashData:</strong><br><code style='word-break:break-all'>" . htmlspecialchars($hashData) . "</code></p>";
echo "<p><strong>SecureHash:</strong><br><code>" . $vnpSecureHash . "</code></p>";
echo "<p><strong>ReturnUrl:</strong> " . htmlspecialchars($vnp_ReturnUrl) . "</p>";
echo "<hr>";
echo "<p><a href='" . htmlspecialchars($paymentUrl) . "' target='_blank' style='font-size:18px; padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>🔗 Click để test thanh toán VNPay</a></p>";
echo "<hr>";
echo "<p><strong>Full URL:</strong><br><textarea rows='5' cols='100'>" . htmlspecialchars($paymentUrl) . "</textarea></p>";
