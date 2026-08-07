<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Category.php";

class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lấy tất cả danh mục (có hỗ trợ tìm kiếm theo từ khóa)
    public function getAll(string $keyword = ''): array
    {
        $list = [];
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories";
            if ($keyword !== '') {
                $sql .= " WHERE catename LIKE ? OR slug LIKE ?";
            }
            $sql .= " ORDER BY id DESC";
            $stmt = $this->prepare($sql);
            if ($keyword !== '') {
                $kw = "%$keyword%";
                $stmt->bind_param("ss", $kw, $kw);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $category = new Category(
                    $row["catename"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $category->id = (int)$row["id"];
                $category->createdAt = $row["created_at"] ?? '';
                $category->updatedAt = $row["updated_at"] ?? '';
                $list[] = $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Tìm theo ID
    public function findById(int $id): ?Category
    {
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $category = new Category(
                    $row["catename"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    (int)$row["status"]
                );
                $category->id = (int)$row["id"];
                $category->createdAt = $row["created_at"] ?? '';
                $category->updatedAt = $row["updated_at"] ?? '';
                return $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    // Thêm danh mục
    public function insert(Category $category): bool
    {
        try {
            $sql = "INSERT INTO categories(catename, slug, image, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $category->name,
                $category->slug,
                $category->image,
                $category->description,
                $category->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Cập nhật danh mục
    public function update(Category $category): bool
    {
        try {
            $sql = "UPDATE categories SET catename=?, slug=?, image=?, description=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssii",
                $category->name,
                $category->slug,
                $category->image,
                $category->description,
                $category->status,
                $category->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Xóa danh mục
    public function delete(int $id): bool
    {
        // --- Cách 2: Xóa thủ công dữ liệu con trước (dùng khi CSDL KHÔNG CÓ ON DELETE CASCADE) ---
        // $this->beginTransaction();
        // try {
        //     // 1. Xóa hình ảnh & chi tiết đơn hàng của các sản phẩm thuộc danh mục
        //     $stmt1 = $this->prepare("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE category_id = ?)");
        //     $stmt1->bind_param("i", $id);
        //     $stmt1->execute();
        //
        //     $stmt2 = $this->prepare("DELETE FROM order_details WHERE product_id IN (SELECT id FROM products WHERE category_id = ?)");
        //     $stmt2->bind_param("i", $id);
        //     $stmt2->execute();
        //
        //     // 2. Xóa các sản phẩm thuộc danh mục
        //     $stmt3 = $this->prepare("DELETE FROM products WHERE category_id = ?");
        //     $stmt3->bind_param("i", $id);
        //     $stmt3->execute();
        //
        //     // 3. Xóa danh mục cha
        //     $stmt4 = $this->prepare("DELETE FROM categories WHERE id = ?");
        //     $stmt4->bind_param("i", $id);
        //     $res = $stmt4->execute();
        //
        //     $this->commit();
        //     return $res;
        // } catch (Exception $e) {
        //     $this->rollback();
        //     throw $e;
        // }

        // --- Cách 1: Xóa trực tiếp (CSDL ĐÃ CÓ ON DELETE CASCADE tự động xóa con) ---
        try {
            $sql = "DELETE FROM categories WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Đếm tổng số danh mục cho Dashboard
    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM categories");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
