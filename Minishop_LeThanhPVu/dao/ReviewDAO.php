<?php
namespace DAO;

use Models\Review;

/**
 * Data Access Object phụ trách thao tác CSDL cho Đánh giá & Bình luận (Reviews)
 */
class ReviewDAO extends BaseDAO
{
    /**
     * Ánh xạ một dòng dữ liệu CSDL thành đối tượng Model Review
     */
    private function mapRow(array $row): Review
    {
        $r = new Review(
            (int)$row['product_id'],
            $row['fullname'],
            (int)$row['rating'],
            $row['comment'],
            (int)$row['status']
        );
        $r->id = (int)$row['id'];
        $r->createdAt = $row['created_at'] ?? '';
        return $r;
    }

    /**
     * Lấy toàn bộ bình luận đã duyệt (status = 1) của một sản phẩm
     */
    public function getByProductId(int $productId): array
    {
        $list = [];
        $stmt = $this->prepare("SELECT * FROM reviews WHERE product_id = ? AND status = 1 ORDER BY id DESC");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    /**
     * Lưu một đánh giá mới từ khách hàng vào CSDL
     */
    public function insert(Review $r): bool
    {
        $stmt = $this->prepare("INSERT INTO reviews (product_id, fullname, rating, comment, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isisi", $r->productId, $r->fullname, $r->rating, $r->comment, $r->status);
        return $stmt->execute();
    }

    /**
     * Tính tổng số lượt đánh giá và điểm số trung bình (sao ⭐) của sản phẩm
     */
    public function getRatingSummary(int $productId): array
    {
        $stmt = $this->prepare("SELECT COUNT(*) AS total_reviews, AVG(rating) AS avg_rating FROM reviews WHERE product_id = ? AND status = 1");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return [
            'total' => (int)($row['total_reviews'] ?? 0),
            'avg'   => round((float)($row['avg_rating'] ?? 5), 1)
        ];
    }
}
