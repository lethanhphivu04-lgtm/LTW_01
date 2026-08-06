<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $p = new Product(
                        (int)$row["category_id"],
                        (int)$row["brand_id"],
                        $row["proname"],
                        $row["slug"],
                        (float)$row["price"],
                        (float)$row["discount_price"],
                        (int)$row["quantity"],
                        $row["image"],
                        $row["description"],
                        (int)$row["status"]
                    );
                    $p->id = (int)$row["id"];
                    $p->createdAt = $row["created_at"] ?? '';
                    $p->updatedAt = $row["updated_at"] ?? '';
                    $list[] = $p;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $p = new Product(
                    (int)$row["category_id"],
                    (int)$row["brand_id"],
                    $row["proname"],
                    $row["slug"],
                    (float)$row["price"],
                    (float)$row["discount_price"],
                    (int)$row["quantity"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $p->id = (int)$row["id"];
                $p->createdAt = $row["created_at"] ?? '';
                $p->updatedAt = $row["updated_at"] ?? '';
                return $p;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $p): bool
    {
        try {
            $sql = "INSERT INTO products(category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissi",
                $p->categoryId,
                $p->brandId,
                $p->name,
                $p->slug,
                $p->price,
                $p->discountPrice,
                $p->quantity,
                $p->image,
                $p->description,
                $p->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Product $p): bool
    {
        try {
            $sql = "UPDATE products SET category_id=?, brand_id=?, proname=?, slug=?, price=?, discount_price=?, quantity=?, image=?, description=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissii",
                $p->categoryId,
                $p->brandId,
                $p->name,
                $p->slug,
                $p->price,
                $p->discountPrice,
                $p->quantity,
                $p->image,
                $p->description,
                $p->status,
                $p->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM products WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM products");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    // Lấy 5 sản phẩm mới nhất cho Dashboard
    public function getNewest(int $limit = 5): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    ORDER BY p.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $row['category_name'] = $row['catename'] ?? '';
                $row['brand_name'] = $row['brandname'] ?? '';
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}
