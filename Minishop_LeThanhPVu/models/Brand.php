<?php
namespace Models;

/**
 * Model đại diện cho Thương hiệu (Brand) sản phẩm
 */
class Brand
{
    public int $id;                   // Mã định danh thương hiệu (Khóa chính)
    public string $name;              // Tên thương hiệu (VD: Logitech, Razer, Sony, Dell, Asus...)
    public string $slug;              // Chuỗi định danh URL chuẩn SEO (VD: logitech, razer...)
    public ?string $image;            // Tên file ảnh logo thương hiệu
    public ?string $description;      // Mô tả chi tiết về thương hiệu
    public int $status;               // Trạng thái: 1 = Hiển thị, 0 = Ẩn
    public string $createdAt;         // Thời gian tạo
    public string $updatedAt;         // Thời gian cập nhật gần nhất

    public function __construct(
        string $name = "",
        string $slug = "",
        ?string $image = null,
        ?string $description = null,
        int $status = 1
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
    }
}
