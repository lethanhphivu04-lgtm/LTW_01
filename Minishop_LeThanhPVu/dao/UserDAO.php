<?php
namespace DAO;

use Models\User;

class UserDAO extends BaseDAO
{
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

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users ORDER BY id DESC";
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

    public function findById(int $id): ?User
    {
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users WHERE id = ?";
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

    public function findByUsername(string $username): ?User
    {
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users WHERE username = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("s", $username);
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

    public function insert(User $u): bool
    {
        try {
            $sql = "INSERT INTO users(fullname, username, password, email, phone, address, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function update(User $u): bool
    {
        try {
            $sql = "UPDATE users SET fullname=?, username=?, password=?, email=?, phone=?, address=?, role=?, status=? WHERE id=?";
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function count(string $table = "users", string $column = "fullname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("users");
        }
        $sql = "SELECT COUNT(*) AS total FROM users WHERE fullname LIKE ? OR username LIKE ? OR email LIKE ?";
        $stmt = $this->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users";
            if ($keyword !== '') {
                $sql .= " WHERE fullname LIKE ? OR username LIKE ? OR email LIKE ?";
            }
            switch ($sort) {
                case 'name_asc': $sql .= " ORDER BY fullname ASC"; break;
                case 'name_desc': $sql .= " ORDER BY fullname DESC"; break;
                case 'id_asc': $sql .= " ORDER BY id ASC"; break;
                default: $sql .= " ORDER BY id DESC"; break;
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
        } catch (\Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countAll(): int
    {
        return $this->count("users");
    }
}
