<?php
namespace DAO;

use Models\Banner;

/**
 * Data Access Object phụ trách thao tác CSDL cho Banner Slider
 */
class BannerDAO extends BaseDAO
{
    /**
     * Ánh xạ một dòng dữ liệu từ CSDL thành đối tượng Model Banner
     */
    private function mapRow(array $row): Banner
    {
        $b = new Banner(
            $row['title'],
            $row['subtitle'] ?? null,
            $row['image'] ?? null,
            $row['link'] ?? '/products',
            $row['badge_text'] ?? 'Premium Tech Store',
            (int)($row['sort_order'] ?? 0),
            (int)($row['status'] ?? 1)
        );
        $b->id = (int)$row['id'];
        $b->createdAt = $row['created_at'] ?? '';
        return $b;
    }

    /**
     * Lấy danh sách Banner đang kích hoạt (status = 1) để hiển thị ngoài trang chủ
     */
    public function getActive(): array
    {
        $list = [];
        $res = $this->executeQuery("SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, id ASC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    /**
     * Lấy toàn bộ Banner trong trang Quản trị (Admin)
     */
    public function getAll(): array
    {
        $list = [];
        $res = $this->executeQuery("SELECT * FROM banners ORDER BY sort_order ASC, id DESC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    /**
     * Tìm một Banner theo ID
     */
    public function findById(int $id): ?Banner
    {
        $stmt = $this->prepare("SELECT * FROM banners WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    /**
     * Thêm mới một Banner vào CSDL
     */
    public function insert(Banner $b): bool
    {
        $stmt = $this->prepare("INSERT INTO banners (title, subtitle, image, link, badge_text, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param(
            "sssssii",
            $b->title,
            $b->subtitle,
            $b->image,
            $b->link,
            $b->badgeText,
            $b->sortOrder,
            $b->status
        );
        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin Banner
     */
    public function update(Banner $b): bool
    {
        $stmt = $this->prepare("UPDATE banners SET title = ?, subtitle = ?, image = ?, link = ?, badge_text = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->bind_param(
            "sssssiii",
            $b->title,
            $b->subtitle,
            $b->image,
            $b->link,
            $b->badgeText,
            $b->sortOrder,
            $b->status,
            $b->id
        );
        return $stmt->execute();
    }

    /**
     * Xóa một Banner theo ID
     */
    public function delete(int $id): bool
    {
        $stmt = $this->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
