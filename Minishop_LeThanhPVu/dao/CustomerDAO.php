<?php
namespace DAO;

use Models\Customer;

class CustomerDAO extends BaseDAO
{
    // Chuyển đổi dữ liệu từ MySQL row sang Object Customer
    private function mapRow(array $row): Customer
    {
        $c = new Customer(
            $row["fullname"],
            $row["phone"],
            $row["email"],
            $row["address"],
            $row["note"],
            (int)$row["status"]
        );
        $c->id = (int)$row["id"];
        $c->createdAt = $row["created_at"] ?? '';
        $c->updatedAt = $row["updated_at"] ?? '';
        return $c;
    }

    // Lấy tất cả khách hàng
    public function getAll(): array
    {
        $list = [];
        $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at 
                FROM customers 
                ORDER BY id DESC";
        $result = $this->executeQuery($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    // Tìm khách hàng theo ID
    public function findById(int $id): ?Customer
    {
        $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at 
                FROM customers 
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

    // Thêm mới khách hàng
    public function insert(Customer $c): bool
    {
        $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $c->fullname,
            $c->phone,
            $c->email,
            $c->address,
            $c->note,
            $c->status
        );
        return $stmt->execute();
    }

    // Cập nhật thông tin khách hàng
    public function update(Customer $c): bool
    {
        $sql = "UPDATE customers 
                SET fullname=?, phone=?, email=?, address=?, note=?, status=? 
                WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "sssssii",
            $c->fullname,
            $c->phone,
            $c->email,
            $c->address,
            $c->note,
            $c->status,
            $c->id
        );
        return $stmt->execute();
    }

    // Xóa khách hàng
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM customers WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Đếm tổng số khách hàng (hỗ trợ tìm kiếm)
    public function count(string $table = "customers", string $column = "fullname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("customers");
        }
        $sql = "SELECT COUNT(*) AS total 
                FROM customers 
                WHERE fullname LIKE ? OR phone LIKE ? OR email LIKE ?";
        $stmt = $this->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Lấy danh sách khách hàng có phân trang và sắp xếp cho Admin
    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at 
                FROM customers";
        
        if ($keyword !== '') {
            $sql .= " WHERE fullname LIKE ? OR phone LIKE ? OR email LIKE ?";
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
        return $this->count("customers");
    }

    // Tìm khách hàng theo số điện thoại
    public function findByPhone(string $phone): ?Customer
    {
        $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at 
                FROM customers 
                WHERE phone = ? 
                LIMIT 1";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    // Thêm khách hàng và trả về ID vừa tạo
    public function insertGetId(Customer $c): int
    {
        $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $c->fullname,
            $c->phone,
            $c->email,
            $c->address,
            $c->note,
            $c->status
        );
        if ($stmt->execute()) {
            return (int)$this->conn->insert_id;
        }
        return 0;
    }
}
