<?php
namespace Models;

/**
 * Model đại diện cho Đánh giá & Bình luận (Review / Rating) sản phẩm
 */
class Review
{
    public ?int $id = null;           // Mã định danh bình luận (Khóa chính)
    public int $productId;            // ID sản phẩm được đánh giá (Khóa ngoại)
    public string $fullname;          // Tên người đánh giá
    public int $rating;               // Số sao đánh giá (từ 1 đến 5 sao ⭐)
    public string $comment;           // Nội dung cảm nhận / nhận xét
    public int $status;               // Trạng thái: 1 = Hiển thị công khai, 0 = Ẩn
    public string $createdAt;         // Thời gian gửi đánh giá

    public function __construct(
        int $productId,
        string $fullname,
        int $rating = 5,
        string $comment = '',
        int $status = 1
    ) {
        $this->productId = $productId;
        $this->fullname = trim($fullname);
        $this->rating = max(1, min(5, $rating));
        $this->comment = trim($comment);
        $this->status = $status;
    }
}
