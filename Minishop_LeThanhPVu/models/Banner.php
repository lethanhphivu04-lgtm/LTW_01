<?php
namespace Models;

/**
 * Model đại diện cho Banner Slider hiển thị ngoài trang chủ
 */
class Banner
{
    public ?int $id = null;           // Mã định danh banner (Khóa chính)
    public string $title;             // Tiêu đề chính của banner (Hỗ trợ HTML)
    public ?string $subtitle;         // Phụ đề mô tả ngắn
    public ?string $image;            // Tên file ảnh banner (lưu trong uploads/banners/)
    public string $link;              // Đường dẫn khi người dùng bấm vào (URL link)
    public string $badgeText;         // Chữ hiển thị trên huy hiệu nổi (Badge)
    public int $sortOrder;            // Thứ tự ưu tiên hiển thị (số nhỏ đứng trước)
    public int $status;               // Trạng thái: 1 = Hiển thị, 0 = Ẩn
    public string $createdAt;         // Thời gian tạo

    public function __construct(
        string $title,
        ?string $subtitle = null,
        ?string $image = null,
        string $link = '/products',
        string $badgeText = 'Premium Tech Store',
        int $sortOrder = 0,
        int $status = 1
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->image = $image;
        $this->link = $link;
        $this->badgeText = $badgeText;
        $this->sortOrder = $sortOrder;
        $this->status = $status;
    }
}
