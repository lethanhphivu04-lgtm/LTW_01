<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Customer.php";

class CustomerDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

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

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers ORDER BY id DESC";
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

    public function findById(int $id): ?Customer
    {
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers WHERE id = ?";
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

    public function insert(Customer $c): bool
    {
        try {
            $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) VALUES (?, ?, ?, ?, ?, ?)";
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
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Customer $c): bool
    {
        try {
            $sql = "UPDATE customers SET fullname=?, phone=?, email=?, address=?, note=?, status=? WHERE id=?";
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
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        // $this->beginTransaction();
        // try {
        //     // 1. Xóa chi tiết các đơn hàng thuộc khách hàng này
        //     $stmt1 = $this->prepare("DELETE FROM order_details WHERE order_id IN (SELECT id FROM orders WHERE customer_id = ?)");
        //     $stmt1->bind_param("i", $id);
        //     $stmt1->execute();
        //
        //     // 2. Xóa các đơn hàng của khách hàng này
        //     $stmt2 = $this->prepare("DELETE FROM orders WHERE customer_id = ?");
        //     $stmt2->bind_param("i", $id);
        //     $stmt2->execute();
        //
        //     // 3. Xóa khách hàng
        //     $stmt3 = $this->prepare("DELETE FROM customers WHERE id = ?");
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
            $sql = "DELETE FROM customers WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function count(string $table = "customers", string $column = "fullname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("customers");
        }
        $sql = "SELECT COUNT(*) AS total FROM customers WHERE fullname LIKE ? OR phone LIKE ? OR email LIKE ?";
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
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers";
            if ($keyword !== '') {
                $sql .= " WHERE fullname LIKE ? OR phone LIKE ? OR email LIKE ?";
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
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM customers");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}

