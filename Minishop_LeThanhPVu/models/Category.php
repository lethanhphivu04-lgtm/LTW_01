<?php
namespace Models;

/**
 * Model đại diện cho Danh mục (Category) ngành hàng
 */
class Category
{
    public int $id;                   // Mã định danh danh mục (Khóa chính)
    public string $name;              // Tên danh mục (VD: Chuột Gaming, Bàn phím cơ, Màn hình, Laptop...)
    public string $slug;              // Chuỗi định danh URL thân thiện SEO (VD: chuot-gaming, ban-phim-co...)
    public ?string $image;            // Tên file ảnh đại diện danh mục
    public ?string $description;      // Mô tả chi tiết danh mục
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
