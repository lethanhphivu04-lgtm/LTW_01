<?php
namespace Models;

/**
 * Model đại diện cho Khách hàng (Customer) mua sắm
 */
class Customer
{
    public int $id;                   // Mã định danh khách hàng (Khóa chính)
    public string $fullname;          // Họ và tên khách hàng
    public string $phone;             // Số điện thoại liên hệ / nhận hàng
    public ?string $email;            // Địa chỉ Email nhận hóa đơn điện tử
    public ?string $address;          // Địa chỉ giao hàng mặc định
    public ?string $note;             // Ghi chú thêm về khách hàng
    public int $status;               // Trạng thái tài khoản: 1 = Hoạt động, 0 = Khóa
    public string $createdAt;         // Thời gian tạo
    public string $updatedAt;         // Thời gian cập nhật gần nhất

    public function __construct(
        string $fullname = "",
        string $phone = "",
        ?string $email = null,
        ?string $address = null,
        ?string $note = null,
        int $status = 1
    ) {
        $this->fullname = $fullname;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->note = $note;
        $this->status = $status;
    }
}
