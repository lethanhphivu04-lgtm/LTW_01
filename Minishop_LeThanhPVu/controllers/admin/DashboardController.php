<?php
namespace Controllers\Admin;

use DAO\CategoryDAO;
use DAO\BrandDAO;
use DAO\ProductDAO;
use DAO\CustomerDAO;
use DAO\OrderDAO;

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

    public function index()
    {
        $pageTitle = "Dashboard - Quản trị hệ thống";

        $totalCategory = $this->categoryDAO->countAll();
        $totalBrand = $this->brandDAO->countAll();
        $totalProduct = $this->productDAO->countAll();
        $totalCustomer = $this->customerDAO->countAll();
        $totalOrder = $this->orderDAO->countAll();

        $newestProducts = $this->productDAO->getNewest(5);
        $newestOrders = $this->orderDAO->getNewest(5);

        require __DIR__ . "/../../views/admin/dashboard.php";
    }
}
