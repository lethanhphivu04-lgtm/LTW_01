<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO;

class HomeController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
    }

    public function index()
    {
        $pageTitle = "MiniShop - Trang chủ";
        $featuredCategories = $this->categoryDAO->getByLimit(6);
        $discountedProducts = $this->productDAO->getDiscounted(8);
        $newestProducts = $this->productDAO->getNewestClient(8);

        require __DIR__ . "/../../views/client/home/index.php";
    }
}
