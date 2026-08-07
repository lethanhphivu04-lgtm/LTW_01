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
        $this->beginTransaction();
        try {
            // 1. Xóa các chi tiết đơn hàng (order_details) thuộc đơn này
            $stmt1 = $this->prepare("DELETE FROM order_details WHERE order_id = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // 2. Xóa đơn hàng
            $stmt2 = $this->prepare("DELETE FROM orders WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $res = $stmt2->execute();

            $this->commit();
            return $res;
        } catch (Exception $e) {
            $this->rollback();
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

    // Lấy danh sách đơn hàng kèm tên khách hàng + nhân viên (JOIN), có tìm kiếm
    public function getAllWithJoin(string $keyword = ''): array
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
            $sql .= " ORDER BY o.id DESC";
            $stmt = $this->prepare($sql);
            if ($keyword !== '') {
                $kw = "%$keyword%";
                $stmt->bind_param("ss", $kw, $kw);
            }
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(int $id, int $status): bool
    {
        try {
            $sql = "UPDATE orders SET status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ii", $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
