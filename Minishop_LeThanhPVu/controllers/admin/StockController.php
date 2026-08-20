<?php
namespace Controllers\Admin;

use DAO\StockReceiptDAO;
use DAO\ProductDAO;
use Models\StockReceipt;

/**
 * Controller Quản lý Nhập kho và Lập Phiếu nhập hàng (Stock Management)
 */
class StockController
{
    private StockReceiptDAO $stockReceiptDAO;
    private ProductDAO $productDAO;

    public function __construct()
    {
        $this->stockReceiptDAO = new StockReceiptDAO();
        $this->productDAO = new ProductDAO();
    }

    /**
     * Hiển thị danh sách Lịch sử Phiếu nhập kho & Thống kê tổng vốn nhập
     */
    public function index()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $receipts = $this->stockReceiptDAO->getAllWithProduct($keyword);
        $totalCost = $this->stockReceiptDAO->getTotalImportCost();
        $pageTitle = "Quản lý Nhập kho (Phiếu nhập)";

        require __DIR__ . "/../../views/admin/stock/index.php";
    }

    /**
     * Lập Phiếu Nhập Kho Mới (Tự động cộng dồn tồn kho sản phẩm bằng Transaction)
     */
    public function create()
    {
        $products = $this->productDAO->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $importPrice = (float)($_POST['import_price'] ?? 0);
            $supplierName = trim($_POST['supplier_name'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $currentUser = $_SESSION['user']->username ?? 'admin';

            // Kiểm tra dữ liệu đầu vào
            if ($productId <= 0 || $quantity <= 0 || $importPrice <= 0) {
                $_SESSION['error'] = "Vui lòng chọn sản phẩm, nhập số lượng và giá vốn nhập hợp lệ (> 0).";
                header("Location: index.php?area=admin&controller=stock&action=create");
                exit;
            }

            // Sinh mã phiếu nhập tự động: PNK-YYYYMMDD-HHIISS
            $receiptCode = "PNK-" . date("Ymd-His");

            $receipt = new StockReceipt(
                $receiptCode,
                $productId,
                $quantity,
                $importPrice,
                $supplierName,
                $note,
                $currentUser
            );

            try {
                // Thêm phiếu nhập và cập nhật tồn kho an toàn trong CSDL
                if ($this->stockReceiptDAO->insertReceipt($receipt)) {
                    $_SESSION['success'] = "Lập phiếu nhập kho thành công! Đã tự động cộng thêm +{$quantity} sản phẩm vào tồn kho.";
                    header("Location: index.php?area=admin&controller=stock&action=index");
                    exit;
                } else {
                    $_SESSION['error'] = "Không thể tạo phiếu nhập kho.";
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = "Lỗi khi nhập kho: " . $e->getMessage();
            }
        }

        $pageTitle = "Lập Phiếu Nhập Kho Mới";
        require __DIR__ . "/../../views/admin/stock/create.php";
    }

    /**
     * Xuất và In Phiếu Nhập Kho khổ A4 chuẩn Mẫu số 01 - VT
     */
    public function receipt()
    {
        $id = (int)($_GET['id'] ?? 0);
        $receipt = $this->stockReceiptDAO->findByIdWithProduct($id);
        if (!$receipt) {
            header("Location: index.php?area=admin&controller=stock&action=index");
            exit;
        }

        $pageTitle = "Phiếu nhập kho #" . htmlspecialchars($receipt['receipt_code']);
        require __DIR__ . "/../../views/admin/stock/receipt.php";
    }
}
