<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Customer.php";

class CustomerDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
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
                    $list[] = $c;
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
        try {
            $sql = "DELETE FROM customers WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM customers");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
