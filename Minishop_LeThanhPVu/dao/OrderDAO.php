<?php
namespace DAO;

use Models\Order;
use Models\OrderDetail;

class OrderDAO extends BaseDAO
{
    // Chuyển đổi dữ liệu từ MySQL row sang Object Order
    private function mapRow(array $row): Order
    {
        $o = new Order(
            (int)$row["customer_id"],
            $row["user_id"] ? (int)$row["user_id"] : null,
            $row["order_code"],
            (float)$row["total_amount"],
            $row["note"],
            (int)$row["status"],
            $row["payment_method"] ?? 'cod',
            $row["coupon_code"] ?? null,
            (float)($row["discount_amount"] ?? 0)
        );
        $o->id = (int)$row["id"];
        $o->createdAt = $row["created_at"] ?? '';
        $o->updatedAt = $row["updated_at"] ?? '';
        return $o;
    }

    // Lấy toàn bộ đơn hàng
    public function getAll(): array
    {
        $list = [];
        $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, payment_method, created_at, updated_at 
                FROM orders 
                ORDER BY id DESC";
        $result = $this->executeQuery($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    // Tìm đơn hàng theo ID
    public function findById(int $id): ?Order
    {
        $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, payment_method, created_at, updated_at 
                FROM orders 
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

    // Thêm mới đơn hàng cơ bản
    public function insert(Order $o): bool
    {
        $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "iisdsis",
            $o->customerId,
            $o->userId,
            $o->orderCode,
            $o->totalAmount,
            $o->note,
            $o->status,
            $o->paymentMethod
        );
        return $stmt->execute();
    }

    // Cập nhật thông tin đơn hàng
    public function update(Order $o): bool
    {
        $sql = "UPDATE orders 
                SET customer_id=?, user_id=?, order_code=?, total_amount=?, note=?, status=?, payment_method=? 
                WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "iisdsisi",
            $o->customerId,
            $o->userId,
            $o->orderCode,
            $o->totalAmount,
            $o->note,
            $o->status,
            $o->paymentMethod,
            $o->id
        );
        return $stmt->execute();
    }

    // Xóa đơn hàng và hoàn trả tồn kho nếu chưa bị hủy
    public function delete(int $id): bool
    {
        $this->conn->begin_transaction();
        try {
            $currentOrder = $this->findById($id);
            // Nếu đơn hàng chưa bị hủy (status != 4), hoàn trả tồn kho trước khi xóa
            if ($currentOrder && $currentOrder->status !== 4) {
                $details = $this->getOrderDetails($id);
                $sqlRestore = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
                $stmtRestore = $this->prepare($sqlRestore);
                foreach ($details as $item) {
                    $pId = (int)$item['product_id'];
                    $qty = (int)$item['quantity'];
                    $stmtRestore->bind_param("ii", $qty, $pId);
                    $stmtRestore->execute();
                }
            }

            // Xóa chi tiết đơn hàng trước (Foreign Key cascade)
            $sqlDeleteDetails = "DELETE FROM order_details WHERE order_id = ?";
            $stmtDeleteDetails = $this->prepare($sqlDeleteDetails);
            $stmtDeleteDetails->bind_param("i", $id);
            $stmtDeleteDetails->execute();

            // Xóa bản ghi đơn hàng
            $sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
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
        $sql = "SELECT o.*, c.fullname AS customer_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                ORDER BY o.id DESC 
                LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Đếm tổng số đơn hàng (hỗ trợ tìm kiếm theo mã đơn hoặc tên khách)
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

    // Lấy danh sách đơn hàng có phân trang và sắp xếp cho Admin
    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        $sql = "SELECT o.*, c.fullname AS customer_name, u.fullname AS user_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                LEFT JOIN users u ON o.user_id = u.id";
        
        if ($keyword !== '') {
            $sql .= " WHERE o.order_code LIKE ? OR c.fullname LIKE ?";
        }

        switch ($sort) {
            case 'code_asc':
                $sql .= " ORDER BY o.order_code ASC";
                break;
            case 'code_desc':
                $sql .= " ORDER BY o.order_code DESC";
                break;
            case 'amount_asc':
                $sql .= " ORDER BY o.total_amount ASC";
                break;
            case 'amount_desc':
                $sql .= " ORDER BY o.total_amount DESC";
                break;
            default:
                $sql .= " ORDER BY o.id DESC";
                break;
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
        return $list;
    }

    // Lấy 1 đơn hàng kèm thông tin khách hàng và nhân viên
    public function findByIdWithJoin(int $id): ?array
    {
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
        return null;
    }

    // Tra cứu đơn hàng cho khách vãng lai bằng Mã Đơn Hàng + SĐT
    public function findByOrderCodeAndPhone(string $orderCode, string $phone): ?array
    {
        $sql = "SELECT o.*, c.fullname AS customer_name, c.phone AS customer_phone, c.address AS customer_address, c.email AS customer_email 
                FROM orders o 
                INNER JOIN customers c ON o.customer_id = c.id 
                WHERE o.order_code = ? AND c.phone = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ss", $orderCode, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // Lấy chi tiết các sản phẩm trong đơn hàng (Master-Detail)
    public function getOrderDetails(int $orderId): array
    {
        $list = [];
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
        return $list;
    }

    // Cập nhật trạng thái đơn hàng (Xử lý hoàn trả tồn kho nếu hủy đơn)
    public function updateStatus(int $id, int $status): bool
    {
        if ($status === 4) {
            $currentOrder = $this->findById($id);
            if ($currentOrder && $currentOrder->status !== 4) {
                $details = $this->getOrderDetails($id);
                $this->conn->begin_transaction();
                try {
                    $sqlRestore = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
                    $stmtRestore = $this->prepare($sqlRestore);
                    foreach ($details as $item) {
                        $pId = (int)$item['product_id'];
                        $qty = (int)$item['quantity'];
                        $stmtRestore->bind_param("ii", $qty, $pId);
                        $stmtRestore->execute();
                    }
                    $sql = "UPDATE orders SET status=? WHERE id=?";
                    $stmt = $this->prepare($sql);
                    $stmt->bind_param("ii", $status, $id);
                    $stmt->execute();
                    $this->conn->commit();
                    return true;
                } catch (\Exception $e) {
                    $this->conn->rollback();
                    throw $e;
                }
            }
        }

        $sql = "UPDATE orders SET status=? WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ii", $status, $id);
        return $stmt->execute();
    }

    // Tạo đơn hàng kèm chi tiết (Transaction Atomic & Trừ tồn kho)
    public function createOrderWithDetails(Order $order, array $items): int
    {
        $this->conn->begin_transaction();
        try {
            $sqlOrder = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status, payment_method, coupon_code, discount_amount) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtOrder = $this->prepare($sqlOrder);
            $stmtOrder->bind_param(
                "iisdsissd",
                $order->customerId,
                $order->userId,
                $order->orderCode,
                $order->totalAmount,
                $order->note,
                $order->status,
                $order->paymentMethod,
                $order->couponCode,
                $order->discountAmount
            );
            if (!$stmtOrder->execute()) {
                throw new \Exception("Không thể tạo đơn hàng: " . $stmtOrder->error);
            }
            $orderId = (int)$this->conn->insert_id;

            $sqlDetail = "INSERT INTO order_details(order_id, product_id, quantity, price, subtotal) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->prepare($sqlDetail);

            $sqlDeduct = "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
            $stmtDeduct = $this->prepare($sqlDeduct);

            foreach ($items as $item) {
                $productId = (int)($item['productid'] ?? $item['id']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                $subtotal = $price * $quantity;

                // 1. Trừ tồn kho trong CSDL (Atomic check & update)
                $stmtDeduct->bind_param("iii", $quantity, $productId, $quantity);
                if (!$stmtDeduct->execute() || $stmtDeduct->affected_rows === 0) {
                    throw new \Exception("Sản phẩm ID {$productId} không đủ tồn kho hoặc đã được mua bởi người khác.");
                }

                // 2. Thêm vào bảng order_details
                $stmtDetail->bind_param(
                    "iiidd",
                    $orderId,
                    $productId,
                    $quantity,
                    $price,
                    $subtotal
                );
                if (!$stmtDetail->execute()) {
                    throw new \Exception("Không thể lưu chi tiết đơn hàng: " . $stmtDetail->error);
                }
            }

            $this->conn->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Tìm đơn hàng theo mã đơn hàng (dùng cho VNPay callback và Email)
    public function findByOrderCode(string $orderCode): ?array
    {
        $sql = "SELECT o.*, c.fullname AS customer_name, c.phone AS customer_phone, c.address AS customer_address, c.email AS customer_email
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                WHERE o.order_code = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $orderCode);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // Cập nhật trạng thái đơn hàng sau khi thanh toán VNPay
    public function updatePaymentStatus(string $orderCode, int $status): bool
    {
        $sql = "UPDATE orders SET status=? WHERE order_code=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("is", $status, $orderCode);
        return $stmt->execute();
    }

    // --- Analytics & Dashboard Queries ---

    // Tổng doanh thu từ các đơn hàng hợp lệ (không tính đơn hủy)
    public function getTotalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) AS revenue FROM orders WHERE status != 4";
        $res = $this->executeQuery($sql);
        return $res ? (float)$res->fetch_assoc()['revenue'] : 0;
    }

    // Thống kê doanh thu theo 7 ngày gần nhất
    public function getRevenueLast7Days(): array
    {
        $list = [];
        $sql = "SELECT DATE(created_at) AS order_date, COALESCE(SUM(total_amount), 0) AS daily_revenue, COUNT(*) AS order_count
                FROM orders
                WHERE status != 4 AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(created_at)
                ORDER BY order_date ASC";
        $res = $this->executeQuery($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $row;
            }
        }
        return $list;
    }

    // Thống kê đơn hàng theo phương thức thanh toán
    public function getPaymentMethodStats(): array
    {
        $list = [];
        $sql = "SELECT payment_method, COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_amount
                FROM orders
                WHERE status != 4
                GROUP BY payment_method";
        $res = $this->executeQuery($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[] = $row;
            }
        }
        return $list;
    }

    // Thống kê số lượng đơn theo trạng thái
    public function getOrderStatusStats(): array
    {
        $list = [];
        $sql = "SELECT status, COUNT(*) AS count FROM orders GROUP BY status";
        $res = $this->executeQuery($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $list[(int)$row['status']] = (int)$row['count'];
            }
        }
        return $list;
    }
}
