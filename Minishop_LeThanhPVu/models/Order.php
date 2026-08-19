<?php
namespace Models;

class Order
{
    public int $id;
    public int $customerId;
    public ?int $userId;
    public string $orderCode;
    public float $totalAmount;
    public ?string $note;
    public int $status; // 0: Chờ xử lý, 1: Hoàn thành, 2: Hủy
    public string $paymentMethod; // 'cod' hoặc 'vnpay'
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $customerId = 0,
        ?int $userId = null,
        string $orderCode = "",
        float $totalAmount = 0,
        ?string $note = null,
        int $status = 0,
        string $paymentMethod = 'cod'
    ) {
        $this->customerId = $customerId;
        $this->userId = $userId;
        $this->orderCode = $orderCode;
        $this->totalAmount = $totalAmount;
        $this->note = $note;
        $this->status = $status;
        $this->paymentMethod = $paymentMethod;
    }
}
