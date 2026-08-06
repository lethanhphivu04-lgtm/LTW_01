<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Order.php";

class OrderDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $o = new Order(
                        (int)$row["customer_id"],
                        $row["user_id"] ? (int)$row["user_id"] : null,
                        $row["order_code"],
                        (float)$row["total_amount"],
                        $row["note"],
                        (int)$row["status"]
                    );
                    $o->id = (int)$row["id"];
                    $o->createdAt = $row["created_at"] ?? '';
                    $o->updatedAt = $row["updated_at"] ?? '';
                    $list[] = $o;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $o = new Order(
                    (int)$row["customer_id"],
                    $row["user_id"] ? (int)$row["user_id"] : null,
                    $row["order_code"],
                    (float)$row["total_amount"],
                    $row["note"],
                    (int)$row["status"]
                );
                $o->id = (int)$row["id"];
                $o->createdAt = $row["created_at"] ?? '';
                $o->updatedAt = $row["updated_at"] ?? '';
                return $o;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Order $o): bool
    {
        try {
            $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisdsi",
                $o->customerId,
                $o->userId,
                $o->orderCode,
                $o->totalAmount,
                $o->note,
                $o->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Order $o): bool
    {
        try {
            $sql = "UPDATE orders SET customer_id=?, user_id=?, order_code=?, total_amount=?, note=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisdsii",
                $o->customerId,
                $o->userId,
                $o->orderCode,
                $o->totalAmount,
                $o->note,
                $o->status,
                $o->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM orders WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM orders");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }

    // Lấy 5 đơn hàng mới nhất kèm tên khách hàng cho Dashboard
    public function getNewest(int $limit = 5): array
    {
        $list = [];
        try {
            $sql = "SELECT o.*, c.fullname AS customer_name FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    ORDER BY o.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
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
}
