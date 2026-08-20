<?php
namespace Controllers\Admin;

use DAO\CategoryDAO;
use DAO\BrandDAO;
use DAO\ProductDAO;
use DAO\CustomerDAO;
use DAO\OrderDAO;

/**
 * Controller xử lý Bảng điều khiển tổng quan (Admin Dashboard)
 * Thống kê số lượng, tổng doanh thu và vẽ biểu đồ Chart.js trực quan.
 */
class DashboardController
{
    private CategoryDAO $categoryDAO;
    private BrandDAO $brandDAO;
    private ProductDAO $productDAO;
    private CustomerDAO $customerDAO;
    private OrderDAO $orderDAO;

    public function __construct()
    {
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
        $this->productDAO = new ProductDAO();
        $this->customerDAO = new CustomerDAO();
        $this->orderDAO = new OrderDAO();
    }

    /**
     * Hiển thị trang Dashboard tổng quan
     */
    public function index()
    {
        $pageTitle = "Dashboard - Quản trị hệ thống MiniShop";

        // 1. Lấy các chỉ số tổng quan (Thẻ Card Thống kê)
        $totalCategory = $this->categoryDAO->countAll();
        $totalBrand    = $this->brandDAO->countAll();
        $totalProduct  = $this->productDAO->countAll();
        $totalCustomer = $this->customerDAO->countAll();
        $totalOrder    = $this->orderDAO->countAll();
        $totalRevenue  = $this->orderDAO->getTotalRevenue();

        // 2. Dữ liệu vẽ Biểu đồ doanh thu 7 ngày gần nhất & Tỉ lệ thanh toán
        $revenue7Days = $this->orderDAO->getRevenueLast7Days();
        $paymentStats = $this->orderDAO->getPaymentMethodStats();
        $statusStats  = $this->orderDAO->getOrderStatusStats();

        // 3. Danh sách sản phẩm mới và đơn hàng mới nhất
        $newestProducts = $this->productDAO->getNewest(5);
        $newestOrders   = $this->orderDAO->getNewest(5);

        require __DIR__ . "/../../views/admin/dashboard.php";
    }
}
