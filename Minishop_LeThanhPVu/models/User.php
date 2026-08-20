<?php
namespace Models;

/**
 * Model đại diện cho Tài khoản Quản trị & Nhân viên (User)
 */
class User
{
    public int $id;                   // Mã định danh tài khoản (Khóa chính)
    public string $fullname;          // Họ và tên người dùng
    public string $username;          // Tên đăng nhập (Username)
    public string $password;          // Mật khẩu (đã mã hóa hash)
    public string $email;             // Địa chỉ Email
    public ?string $phone;            // Số điện thoại
    public ?string $address;          // Địa chỉ liên hệ
    public int $role;                 // Phân quyền: 1 = Quản trị viên (Admin), 0 = Nhân viên (Staff)
    public int $status;               // Trạng thái tài khoản: 1 = Hoạt động, 0 = Khóa
    public string $createdAt;         // Thời gian tạo
    public string $updatedAt;         // Thời gian cập nhật gần nhất

    public function __construct(
        string $fullname = "",
        string $username = "",
        string $password = "",
        string $email = "",
        ?string $phone = null,
        ?string $address = null,
        int $role = 0,
        int $status = 1
    ) {
        $this->fullname = $fullname;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->role = $role;
        $this->status = $status;
    }
}
