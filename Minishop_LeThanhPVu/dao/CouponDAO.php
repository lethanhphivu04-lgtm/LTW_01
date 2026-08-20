<?php
namespace DAO;

use Models\Coupon;

/**
 * Data Access Object phụ trách thao tác CSDL cho Mã giảm giá (Coupons)
 */
class CouponDAO extends BaseDAO
{
    /**
     * Ánh xạ một dòng dữ liệu CSDL thành đối tượng Model Coupon
     */
    private function mapRow(array $row): Coupon
    {
        $c = new Coupon(
            $row['code'],
            $row['discount_type'],
            (float)$row['discount_value'],
            (float)$row['min_order_value'],
            $row['max_discount'] !== null ? (float)$row['max_discount'] : null,
            $row['expiry_date'] ?? null,
            (int)$row['status']
        );
        $c->id = (int)$row['id'];
        $c->createdAt = $row['created_at'] ?? '';
        return $c;
    }

    /**
     * Tìm mã giảm giá theo Mã Code (kèm kiểm tra status = 1 đang hoạt động)
     */
    public function findByCode(string $code): ?Coupon
    {
        $code = strtoupper(trim($code));
        $stmt = $this->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1 LIMIT 1");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    /**
     * Lấy toàn bộ danh sách mã giảm giá cho trang Admin
     */
    public function getAll(): array
    {
        $list = [];
        $res = $this->executeQuery("SELECT * FROM coupons ORDER BY id DESC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    /**
     * Tìm mã giảm giá theo ID
     */
    public function findById(int $id): ?Coupon
    {
        $stmt = $this->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    /**
     * Thêm mới một mã giảm giá
     */
    public function insert(Coupon $c): bool
    {
        $stmt = $this->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_order_value, max_discount, expiry_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param(
            "ssdddsi",
            $c->code,
            $c->discountType,
            $c->discountValue,
            $c->minOrderValue,
            $c->maxDiscount,
            $c->expiryDate,
            $c->status
        );
        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin mã giảm giá
     */
    public function update(Coupon $c): bool
    {
        $stmt = $this->prepare("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, min_order_value = ?, max_discount = ?, expiry_date = ?, status = ? WHERE id = ?");
        $stmt->bind_param(
            "ssdddsii",
            $c->code,
            $c->discountType,
            $c->discountValue,
            $c->minOrderValue,
            $c->maxDiscount,
            $c->expiryDate,
            $c->status,
            $c->id
        );
        return $stmt->execute();
    }

    /**
     * Xóa một mã giảm giá theo ID
     */
    public function delete(int $id): bool
    {
        $stmt = $this->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
