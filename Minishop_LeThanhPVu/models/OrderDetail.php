<?php
namespace Models;

/**
 * Model đại diện cho Chi tiết đơn hàng (OrderDetail - từng sản phẩm trong đơn)
 */
class OrderDetail
{
    public int $id;                   // Mã định danh dòng chi tiết (Khóa chính)
    public int $orderId;              // ID đơn hàng cha (Khóa ngoại)
    public int $productId;            // ID sản phẩm được mua (Khóa ngoại)
    public int $quantity;             // Số lượng đặt mua
    public float $price;              // Đơn giá bán tại thời điểm đặt hàng
    public float $subtotal;           // Thành tiền của dòng (quantity * price)
    public string $createdAt;         // Thời gian tạo

    public function __construct(
        int $orderId = 0,
        int $productId = 0,
        int $quantity = 1,
        float $price = 0,
        float $subtotal = 0
    ) {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->subtotal = $subtotal;
    }
}
