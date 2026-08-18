<?php
namespace Controllers\Admin;

use DAO\OrderDAO;

class OrderController
{
    private OrderDAO $orderDAO;

    public function __construct()
    {
        $this->orderDAO = new OrderDAO();
    }

    public function index()
    {
        $pageTitle = "Quản lý Đơn hàng";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->orderDAO->count("orders", "order_code", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->orderDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'code_asc' => 'Mã ĐH A-Z',
            'code_desc' => 'Mã ĐH Z-A',
            'amount_asc' => 'Tổng tiền tăng dần',
            'amount_desc' => 'Tổng tiền giảm dần'
        ];

        require __DIR__ . "/../../views/admin/orders/index.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $status = (int)($_POST['status'] ?? 0);
            $this->orderDAO->updateStatus($id, $status);
            header("Location: index.php?area=admin&controller=order&action=detail&id=" . $id);
            exit;
        }

        $order = $this->orderDAO->findByIdWithJoin($id);
        if (!$order) {
            header("Location: index.php?area=admin&controller=order&action=index");
            exit;
        }

        $pageTitle = "Chi tiết đơn hàng #" . htmlspecialchars($order['order_code']);
        $details = $this->orderDAO->getOrderDetails($id);

        require __DIR__ . "/../../views/admin/orders/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->orderDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=order&action=index");
        exit;
    }
}
