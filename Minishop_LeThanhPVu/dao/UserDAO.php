<?php
namespace DAO;

use Models\User;

class UserDAO extends BaseDAO
{
    // Chuyển đổi dữ liệu từ MySQL row sang Object User
    private function mapRow(array $row): User
    {
        $u = new User(
            $row["fullname"],
            $row["username"],
            $row["password"],
            $row["email"],
            $row["phone"],
            $row["address"],
            (int)$row["role"],
            (int)$row["status"]
        );
        $u->id = (int)$row["id"];
        $u->createdAt = $row["created_at"] ?? '';
        $u->updatedAt = $row["updated_at"] ?? '';
        return $u;
    }

    // Lấy toàn bộ người dùng
    public function getAll(): array
    {
        $list = [];
        $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at 
                FROM users 
                ORDER BY id DESC";
        $result = $this->executeQuery($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    // Tìm người dùng theo ID
    public function findById(int $id): ?User
    {
        $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at 
                FROM users 
                WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    // Tìm người dùng theo tên đăng nhập (Username)
    public function findByUsername(string $username): ?User
    {
        $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at 
                FROM users 
                WHERE username = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    // Thêm người dùng mới
    public function insert(User $u): bool
    {
        $sql = "INSERT INTO users(fullname, username, password, email, phone, address, role, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "ssssssii",
            $u->fullname,
            $u->username,
            $u->password,
            $u->email,
            $u->phone,
            $u->address,
            $u->role,
            $u->status
        );
        return $stmt->execute();
    }

    // Cập nhật người dùng
    public function update(User $u): bool
    {
        $sql = "UPDATE users 
                SET fullname=?, username=?, password=?, email=?, phone=?, address=?, role=?, status=? 
                WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "ssssssiii",
            $u->fullname,
            $u->username,
            $u->password,
            $u->email,
            $u->phone,
            $u->address,
            $u->role,
            $u->status,
            $u->id
        );
        return $stmt->execute();
    }

    // Xóa người dùng
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Đếm tổng số người dùng (hỗ trợ tìm kiếm)
    public function count(string $table = "users", string $column = "fullname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("users");
        }
        $sql = "SELECT COUNT(*) AS total 
                FROM users 
                WHERE fullname LIKE ? OR username LIKE ? OR email LIKE ?";
        $stmt = $this->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Lấy danh sách người dùng có phân trang và sắp xếp cho Admin
    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at 
                FROM users";
        
        if ($keyword !== '') {
            $sql .= " WHERE fullname LIKE ? OR username LIKE ? OR email LIKE ?";
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY fullname ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY fullname DESC";
                break;
            case 'id_asc':
                $sql .= " ORDER BY id ASC";
                break;
            default:
                $sql .= " ORDER BY id DESC";
                break;
        }
        $sql .= " LIMIT ? OFFSET ?";

        $stmt = $this->prepare($sql);
        if ($keyword !== '') {
            $kw = "%$keyword%";
            $stmt->bind_param("sssii", $kw, $kw, $kw, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $this->mapRow($row);
        }
        return $list;
    }

    public function countAll(): int
    {
        return $this->count("users");
    }
}
