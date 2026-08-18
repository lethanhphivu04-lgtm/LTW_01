<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO;
use DAO\BrandDAO;

class ProductController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;
    private BrandDAO $brandDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
    }

    public function index()
    {
        $pageTitle = "Tất cả sản phẩm";
        $limit = 12;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productDAO->countAllClient();
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->getAllClient($limit, $offset);
        $heading = "TẤT CẢ SẢN PHẨM";

        require __DIR__ . "/../../views/client/products/index.php";
    }

    public function category()
    {
        $slug = trim($_GET['slug'] ?? '');
        $cat = $this->categoryDAO->findBySlug($slug);
        if (!$cat) { header("Location: index.php"); exit; }

        $pageTitle = "Danh mục: " . $cat->name;
        $heading = "DANH MỤC: " . strtoupper($cat->name);
        $limit = 12;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productDAO->countByCategorySlug($slug);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->getByCategorySlug($slug, $limit, $offset);

        require __DIR__ . "/../../views/client/products/index.php";
    }

    public function brand()
    {
        $slug = trim($_GET['slug'] ?? '');
        $brand = $this->brandDAO->findBySlug($slug);
        if (!$brand) { header("Location: index.php"); exit; }

        $pageTitle = "Thương hiệu: " . $brand->name;
        $heading = "THƯƠNG HIỆU: " . strtoupper($brand->name);
        $limit = 12;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productDAO->countByBrandSlug($slug);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->getByBrandSlug($slug, $limit, $offset);

        require __DIR__ . "/../../views/client/products/index.php";
    }

    public function detail()
    {
        $slug = trim($_GET['slug'] ?? '');
        $product = $this->productDAO->findBySlug($slug);
        if (!$product) { header("Location: index.php"); exit; }

        $pageTitle = $product['proname'] . " - MiniShop";
        $relatedProducts = $this->productDAO->getByCategorySlug($product['category_slug'], 4);

        require __DIR__ . "/../../views/client/products/detail.php";
    }

    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $pageTitle = "Tìm kiếm: " . $keyword;
        $heading = "KẾT QUẢ TÌM KIẾM: \"" . htmlspecialchars($keyword) . "\"";
        $limit = 12;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productDAO->countSearch($keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->search($keyword, $limit, $offset);

        require __DIR__ . "/../../views/client/products/index.php";
    }
}
