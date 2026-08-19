<?php
namespace Config;

class VNPayConfig
{
    // ===== CẤU HÌNH VNPAY SANDBOX =====
    // Đăng ký tại: https://sandbox.vnpayment.vn/devreg/
    // Sau khi đăng ký, VNPay gửi vnp_TmnCode và vnp_HashSecret qua email

    public static string $vnp_TmnCode = '0WBBOYAE';
    public static string $vnp_HashSecret = 'MPZWHQGORAMQYNQSVXOJNFKTWHCTZUJF';
    public static string $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    public static string $vnp_ReturnUrl = '';  // Sẽ được tính tự động trong VNPayService

    // Thẻ test: Ngân hàng NCB, Số thẻ: 9704198526191432198
    // Tên: NGUYEN VAN A, Ngày phát hành: 07/15, OTP: 123456
}
