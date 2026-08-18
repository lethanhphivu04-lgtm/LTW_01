<?php
namespace DAO;

use Models\Order;
use Models\OrderDetail;

class OrderDAO extends BaseDAO
{
    private function mapRow(array $row): Order
    {
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

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders ORDER BY id DESC";
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

    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders WHERE id = ?";
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        return $this->count("orders");
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
        } catch (\Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function count(string $table = "orders", string $column = "order_code", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("orders");
        }
        $sql = "SELECT COUNT(*) AS total 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.order_code LIKE ? OR c.fullname LIKE ?";
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
            $sql = "SELECT o.*, c.fullname AS customer_name, u.fullname AS user_name 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    LEFT JOIN users u ON o.user_id = u.id";
            if ($keyword !== '') {
                $sql .= " WHERE o.order_code LIKE ? OR c.fullname LIKE ?";
            }
            switch ($sort) {
                case 'code_asc': $sql .= " ORDER BY o.order_code ASC"; break;
                case 'code_desc': $sql .= " ORDER BY o.order_code DESC"; break;
                case 'amount_asc': $sql .= " ORDER BY o.total_amount ASC"; break;
                case 'amount_desc': $sql .= " ORDER BY o.total_amount DESC"; break;
                default: $sql .= " ORDER BY o.id DESC"; break;
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
                $list[] = $row;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Lấy 1 đơn hàng kèm tên khách hàng + nhân viên
    public function findByIdWithJoin(int $id): ?array
    {
        try {
            $sql = "SELECT o.*, c.fullname AS customer_name, c.phone AS customer_phone, c.address AS customer_address, u.fullname AS user_name 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE o.id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return null;
    }

    // Lấy chi tiết sản phẩm trong đơn hàng (Master-Detail)
    public function getOrderDetails(int $orderId): array
    {
        $list = [];
        try {
            $sql = "SELECT od.*, p.proname, p.image 
                    FROM order_details od 
                    INNER JOIN products p ON od.product_id = p.id 
                    WHERE od.order_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $row;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function updateStatus(int $id, int $status): bool
    {
        $sql = "UPDATE orders SET status=? WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ii", $status, $id);
        return $stmt->execute();
    }
}
