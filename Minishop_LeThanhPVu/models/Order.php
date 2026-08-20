<?php
namespace Models;

/**
 * Model đại diện cho Đơn hàng (Order)
 */
class Order
{
    public int $id;                   // Mã định danh đơn hàng (Khóa chính)
    public int $customerId;           // ID của khách hàng đặt mua (Khóa ngoại)
    public ?int $userId;              // ID nhân viên xử lý đơn hàng
    public string $orderCode;         // Mã đơn hàng định dạng duy nhất (VD: DH20260820143015)
    public float $totalAmount;        // Tổng số tiền thanh toán cuối cùng (đã trừ giảm giá)
    public ?string $note;             // Ghi chú giao hàng từ khách
    public int $status;               // Trạng thái: 0: Chờ xử lý, 1: Đã xác nhận, 2: Đang giao, 3: Hoàn thành, 4: Đã hủy
    public string $paymentMethod;     // Phương thức thanh toán: 'cod' (Tiền mặt) hoặc 'vnpay' (Thanh toán Online)
    public ?string $couponCode;       // Mã giảm giá đã áp dụng (nếu có)
    public float $discountAmount;     // Số tiền được chiết khấu giảm giá
    public string $createdAt;         // Thời gian đặt hàng
    public string $updatedAt;         // Thời gian cập nhật trạng thái gần nhất

    public function __construct(
        int $customerId = 0,
        ?int $userId = null,
        string $orderCode = "",
        float $totalAmount = 0,
        ?string $note = null,
        int $status = 0,
        string $paymentMethod = 'cod',
        ?string $couponCode = null,
        float $discountAmount = 0
    ) {
        $this->customerId = $customerId;
        $this->userId = $userId;
        $this->orderCode = $orderCode;
        $this->totalAmount = $totalAmount;
        $this->note = $note;
        $this->status = $status;
        $this->paymentMethod = $paymentMethod;
        $this->couponCode = $couponCode;
        $this->discountAmount = $discountAmount;
    }
}
