<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Brand.php";

class BrandDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    private function mapRow(array $row): Brand
    {
        $brand = new Brand(
            $row["brandname"],
            $row["slug"],
            $row["image"],
            $row["description"],
            (int)$row["status"]
        );
        $brand->id = (int)$row["id"];
        $brand->createdAt = $row["created_at"] ?? '';
        $brand->updatedAt = $row["updated_at"] ?? '';
        return $brand;
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $list[] = $this->mapRow($row);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Brand
    {
        try {
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $this->mapRow($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Brand $brand): bool
    {
        try {
            $sql = "INSERT INTO brands(brandname, slug, image, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $brand->name,
                $brand->slug,
                $brand->image,
                $brand->description,
                $brand->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Brand $brand): bool
    {
        try {
            $sql = "UPDATE brands SET brandname=?, slug=?, image=?, description=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssii",
                $brand->name,
                $brand->slug,
                $brand->image,
                $brand->description,
                $brand->status,
                $brand->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        // $this->beginTransaction();
        // try {
        //     // 1. Xóa hình ảnh & chi tiết đơn hàng của sản phẩm thuộc thương hiệu này
        //     $stmt1 = $this->prepare("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE brand_id = ?)");
        //     $stmt1->bind_param("i", $id);
        //     $stmt1->execute();
        //
        //     $stmt2 = $this->prepare("DELETE FROM order_details WHERE product_id IN (SELECT id FROM products WHERE brand_id = ?)");
        //     $stmt2->bind_param("i", $id);
        //     $stmt2->execute();
        //
        //     // 2. Xóa sản phẩm thuộc thương hiệu này
        //     $stmt3 = $this->prepare("DELETE FROM products WHERE brand_id = ?");
        //     $stmt3->bind_param("i", $id);
        //     $stmt3->execute();
        //
        //     // 3. Xóa thương hiệu cha
        //     $stmt4 = $this->prepare("DELETE FROM brands WHERE id = ?");
        //     $stmt4->bind_param("i", $id);
        //     $res = $stmt4->execute();
        //
        //     $this->commit();
        //     return $res;
        // } catch (Exception $e) {
        //     $this->rollback();
        //     throw $e;
        // }

        try {
            $sql = "DELETE FROM brands WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Tìm kiếm thương hiệu
    public function search(string $keyword): array
    {
        $list = [];
        try {
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands WHERE brandname LIKE ? OR slug LIKE ? ORDER BY id DESC";
            $stmt = $this->prepare($sql);
            $kw = "%$keyword%";
            $stmt->bind_param("ss", $kw, $kw);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM brands");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
