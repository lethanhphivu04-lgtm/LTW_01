<?php
namespace Config;

/**
 * Cấu hình kết nối Cổng thanh toán trực tuyến VNPay Sandbox (Môi trường thử nghiệm)
 */
class VNPayConfig
{
    // Thông tin Merchant do VNPay cung cấp
    public static string $vnp_TmnCode = '0WBBOYAE';                                    // Mã Website (Terminal Code)
    public static string $vnp_HashSecret = 'MPZWHQGORAMQYNQSVXOJNFKTWHCTZUJF';         // Khóa bí mật tạo chữ ký số (Checksum HashSecret)
    public static string $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'; // Đường dẫn cổng thanh toán VNPay
    public static string $vnp_ReturnUrl = '';                                          // URL nhận kết quả trả về (được tính động trong VNPayService)

    // THÔNG TIN THẺ TEST DEMO VNPAY:
    // - Ngân hàng: NCB
    // - Số thẻ: 9704198526191432198
    // - Tên chủ thẻ: NGUYEN VAN A
    // - Ngày phát hành: 07/15
    // - Mã OTP: 123456
}
