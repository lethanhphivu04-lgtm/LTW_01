<?php
namespace DAO;

use Models\Brand;

class BrandDAO extends BaseDAO
{
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM brands WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function count(string $table = "brands", string $column = "brandname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("brands");
        }
        $sql = "SELECT COUNT(*) AS total FROM brands WHERE brandname LIKE ? OR slug LIKE ?";
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
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands";
            if ($keyword !== '') {
                $sql .= " WHERE brandname LIKE ? OR slug LIKE ?";
            }
            switch ($sort) {
                case 'name_asc': $sql .= " ORDER BY brandname ASC"; break;
                case 'name_desc': $sql .= " ORDER BY brandname DESC"; break;
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

    public function countAll(): int
    {
        return $this->count("brands");
    }

    public function getByLimit(int $limit = 5): array
    {
        $list = [];
        $stmt = $this->prepare("SELECT * FROM brands WHERE status = 1 ORDER BY id DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $this->mapRow($row);
        }
        return $list;
    }

    public function findBySlug(string $slug): ?Brand
    {
        $stmt = $this->prepare("SELECT * FROM brands WHERE slug = ? AND status = 1 LIMIT 1");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $this->mapRow($row) : null;
    }
}
