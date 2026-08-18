<?php
namespace Models;

class User
{
    public int $id;
    public string $fullname;
    public string $username;
    public string $password;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public int $role; // 0: Nhân viên, 1: Quản trị
    public int $status;
    public string $createdAt;
    public string $updatedAt;

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
