<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    private function mapRow(array $row): Product
    {
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

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products ORDER BY id DESC";
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

    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products WHERE id = ?";
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
        // $this->beginTransaction();
        // try {
        //     // 1. Xóa hình ảnh sản phẩm (product_images)
        //     $stmt1 = $this->prepare("DELETE FROM product_images WHERE product_id = ?");
        //     $stmt1->bind_param("i", $id);
        //     $stmt1->execute();
        //
        //     // 2. Xóa các chi tiết đơn hàng chứa sản phẩm này (order_details)
        //     $stmt2 = $this->prepare("DELETE FROM order_details WHERE product_id = ?");
        //     $stmt2->bind_param("i", $id);
        //     $stmt2->execute();
        //
        //     // 3. Xóa sản phẩm
        //     $stmt3 = $this->prepare("DELETE FROM products WHERE id = ?");
        //     $stmt3->bind_param("i", $id);
        //     $res = $stmt3->execute();
        //
        //     $this->commit();
        //     return $res;
        // } catch (Exception $e) {
        //     $this->rollback();
        //     throw $e;
        // }

        try {
            $sql = "DELETE FROM products WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Lấy danh sách sản phẩm kèm tên danh mục + thương hiệu (JOIN), có tìm kiếm
    public function getAllWithJoin(string $keyword = ''): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    INNER JOIN categories c ON p.category_id = c.id 
                    INNER JOIN brands b ON p.brand_id = b.id";
            if ($keyword !== '') {
                $sql .= " WHERE p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?";
            }
            $sql .= " ORDER BY p.id DESC";
            $stmt = $this->prepare($sql);
            if ($keyword !== '') {
                $kw = "%$keyword%";
                $stmt->bind_param("sss", $kw, $kw, $kw);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Lấy 1 sản phẩm kèm tên danh mục + thương hiệu
    public function findByIdWithJoin(int $id): ?array
    {
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    INNER JOIN categories c ON p.category_id = c.id 
                    INNER JOIN brands b ON p.brand_id = b.id 
                    WHERE p.id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
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

    // E1: Them anh phu gallery
    public function insertImage($productId, $image)
    {
        try {
            $sql = "INSERT INTO product_images(product_id, image) VALUES (?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("is", $productId, $image);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // E1: Lay danh sach anh phu gallery
    public function getImagesByProductId($productId): array
    {
        $list = [];
        try {
            $sql = "SELECT id, product_id, image, sort_order, created_at FROM product_images WHERE product_id = ? ORDER BY id DESC";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // E1: Xoa anh phu gallery (xoa file vat ly truoc khi xoa DB)
    public function deleteImage($id): bool
    {
        try {
            $sqlSelect = "SELECT image FROM product_images WHERE id = ?";
            $stmtSelect = $this->prepare($sqlSelect);
            $stmtSelect->bind_param("i", $id);
            $stmtSelect->execute();
            $res = $stmtSelect->get_result();
            if ($row = $res->fetch_assoc()) {
                $filePath = __DIR__ . "/../../../uploads/products/" . $row['image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $sqlDelete = "DELETE FROM product_images WHERE id = ?";
            $stmtDelete = $this->prepare($sqlDelete);
            $stmtDelete->bind_param("i", $id);
            return $stmtDelete->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
