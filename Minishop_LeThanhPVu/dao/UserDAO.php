<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/User.php";

class UserDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Tìm kiếm người dùng
    public function search(string $keyword): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users WHERE fullname LIKE ? OR username LIKE ? OR email LIKE ? ORDER BY id DESC";
            $stmt = $this->prepare($sql);
            $kw = "%$keyword%";
            $stmt->bind_param("sss", $kw, $kw, $kw);
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
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM users");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
