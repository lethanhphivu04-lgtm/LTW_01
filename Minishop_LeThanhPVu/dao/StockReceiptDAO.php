<?php
namespace DAO;

use Models\StockReceipt;

class StockReceiptDAO extends BaseDAO
{
    // Lấy danh sách phiếu nhập kèm thông tin sản phẩm
    public function getAllWithProduct(string $keyword = ''): array
    {
        $list = [];
        $sql = "SELECT sr.*, p.proname AS product_name, p.image AS product_image, p.price AS product_price, p.quantity AS current_stock 
                FROM stock_receipts sr 
                INNER JOIN products p ON sr.product_id = p.id";
        
        if ($keyword !== '') {
            $sql .= " WHERE sr.receipt_code LIKE ? OR p.proname LIKE ? OR sr.supplier_name LIKE ?";
        }
        $sql .= " ORDER BY sr.id DESC";

        $stmt = $this->prepare($sql);
        if ($keyword !== '') {
            $kw = "%$keyword%";
            $stmt->bind_param("sss", $kw, $kw, $kw);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Lập phiếu nhập kho (Transaction: Lưu phiếu và Tự động tăng tồn kho sản phẩm)
    public function insertReceipt(StockReceipt $sr): bool
    {
        $this->conn->begin_transaction();
        try {
            // 1. Thêm bản ghi phiếu nhập kho
            $sqlReceipt = "INSERT INTO stock_receipts (receipt_code, product_id, quantity, import_price, supplier_name, note, created_by) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtReceipt = $this->prepare($sqlReceipt);
            $stmtReceipt->bind_param(
                "siidsss",
                $sr->receiptCode,
                $sr->productId,
                $sr->quantity,
                $sr->importPrice,
                $sr->supplierName,
                $sr->note,
                $sr->createdBy
            );
            if (!$stmtReceipt->execute()) {
                throw new \Exception("Không thể tạo phiếu nhập kho: " . $stmtReceipt->error);
            }

            // 2. Tự động cộng dồn số lượng tồn kho sản phẩm
            $sqlUpdateStock = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
            $stmtUpdate = $this->prepare($sqlUpdateStock);
            $stmtUpdate->bind_param("ii", $sr->quantity, $sr->productId);
            if (!$stmtUpdate->execute()) {
                throw new \Exception("Không thể cập nhật tồn kho sản phẩm: " . $stmtUpdate->error);
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Tìm chi tiết 1 phiếu nhập theo ID
    public function findByIdWithProduct(int $id): ?array
    {
        $sql = "SELECT sr.*, p.proname AS product_name, p.slug AS product_slug, p.image AS product_image, p.price AS product_price, p.quantity AS current_stock, c.catename AS category_name, b.brandname AS brand_name
                FROM stock_receipts sr 
                INNER JOIN products p ON sr.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE sr.id = ? 
                LIMIT 1";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    // Đếm tổng số phiếu nhập
    public function countAll(): int
    {
        return $this->count("stock_receipts");
    }

    // Tổng giá trị vốn đã nhập kho
    public function getTotalImportCost(): float
    {
        $sql = "SELECT COALESCE(SUM(quantity * import_price), 0) AS total_cost FROM stock_receipts";
        $res = $this->executeQuery($sql);
        return $res ? (float)$res->fetch_assoc()['total_cost'] : 0;
    }
}
