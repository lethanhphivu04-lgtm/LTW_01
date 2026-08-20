<?php
namespace Models;

/**
 * Model đại diện cho Sản phẩm (Product) công nghệ & phụ kiện
 */
class Product
{
    public int $id;                   // Mã định danh sản phẩm (Khóa chính)
    public int $categoryId;           // ID danh mục ngành hàng (Khóa ngoại)
    public int $brandId;              // ID thương hiệu sản xuất (Khóa ngoại)
    public string $name;              // Tên sản phẩm
    public string $slug;              // Chuỗi URL định danh chuẩn SEO (VD: chuot-logitech-mx-master-3s)
    public float $price;              // Giá gốc niêm yết (VNĐ)
    public float $discountPrice;      // Giá bán thực tế / Giá khuyến mãi (VNĐ)
    public int $quantity;             // Số lượng tồn kho hiện có
    public ?string $image;            // Tên file ảnh đại diện chính (uploads/products/)
    public ?string $description;      // Mô tả chi tiết & Thông số kỹ thuật sản phẩm
    public int $status;               // Trạng thái: 1 = Còn kinh doanh, 0 = Ngừng kinh doanh
    public string $createdAt;         // Thời gian tạo
    public string $updatedAt;         // Thời gian cập nhật gần nhất

    public function __construct(
        int $categoryId = 0,
        int $brandId = 0,
        string $name = "",
        string $slug = "",
        float $price = 0,
        float $discountPrice = 0,
        int $quantity = 0,
        ?string $image = null,
        ?string $description = null,
        int $status = 1
    ) {
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->name = $name;
        $this->slug = $slug;
        $this->price = $price;
        $this->discountPrice = $discountPrice;
        $this->quantity = $quantity;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
    }
}
