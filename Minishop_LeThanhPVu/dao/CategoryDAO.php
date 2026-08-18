<?php
namespace DAO;

use Models\Category;

class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    private function mapRow(array $row): Category
    {
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
                $list[] = $this->mapRow($row);
            }
        } catch (\Exception $e) {
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
                return $this->mapRow($row);
            }
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Xóa danh mục
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM categories WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function count(string $table = "categories", string $column = "catename", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("categories");
        }
        $sql = "SELECT COUNT(*) AS total FROM categories WHERE catename LIKE ? OR slug LIKE ?";
        $stmt = $this->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("ss", $kw, $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories";
            if ($keyword !== '') {
                $sql .= " WHERE catename LIKE ? OR slug LIKE ?";
            }
            switch ($sort) {
                case 'name_asc': $sql .= " ORDER BY catename ASC"; break;
                case 'name_desc': $sql .= " ORDER BY catename DESC"; break;
                case 'id_asc': $sql .= " ORDER BY id ASC"; break;
                default: $sql .= " ORDER BY id DESC"; break;
            }
            $sql .= " LIMIT ? OFFSET ?";

            $stmt = $this->prepare($sql);
            if ($keyword !== '') {
                $kw = "%$keyword%";
                $stmt->bind_param("ssii", $kw, $kw, $limit, $offset);
            } else {
                $stmt->bind_param("ii", $limit, $offset);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Đếm tổng số danh mục cho Dashboard
    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM categories");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
