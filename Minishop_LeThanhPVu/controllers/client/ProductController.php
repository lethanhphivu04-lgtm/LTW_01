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

        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();

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

        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();

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

        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();

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
        $galleryImages = $this->productDAO->getImagesByProductId((int)$product['id']);
        $relatedProducts = $this->productDAO->getByCategorySlug($product['category_slug'], 4);

        $reviewDAO = new \DAO\ReviewDAO();
        $reviews = $reviewDAO->getByProductId((int)$product['id']);
        $ratingSummary = $reviewDAO->getRatingSummary((int)$product['id']);

        require __DIR__ . "/../../views/client/products/detail.php";
    }

    public function review()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $slug = trim($_POST['slug'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $rating = (int)($_POST['rating'] ?? 5);
            $comment = trim($_POST['comment'] ?? '');

            if ($productId > 0 && !empty($fullname) && !empty($comment)) {
                $reviewDAO = new \DAO\ReviewDAO();
                $r = new \Models\Review($productId, $fullname, $rating, $comment, 1);
                $reviewDAO->insert($r);
                $_SESSION['review_success'] = "Cảm ơn bạn đã gửi đánh giá cho sản phẩm!";
            }

            header("Location: index.php?area=client&controller=product&action=detail&slug=" . urlencode($slug) . "#reviews-section");
            exit;
        }
    }

    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $brandId = (int)($_GET['brand_id'] ?? 0);
        $priceRange = trim($_GET['price_range'] ?? '');
        $onSale = (int)($_GET['on_sale'] ?? 0);

        $minPrice = null;
        $maxPrice = null;
        if ($priceRange === 'under_1m') {
            $maxPrice = 1000000;
        } elseif ($priceRange === '1m_5m') {
            $minPrice = 1000000;
            $maxPrice = 5000000;
        } elseif ($priceRange === '5m_15m') {
            $minPrice = 5000000;
            $maxPrice = 15000000;
        } elseif ($priceRange === 'over_15m') {
            $minPrice = 15000000;
        }

        $filters = [
            'keyword' => $keyword,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'on_sale' => $onSale
        ];

        $pageTitle = !empty($keyword) ? "Tìm kiếm: " . $keyword : "Tìm kiếm nâng cao";
        $heading = !empty($keyword) ? "KẾT QUẢ TÌM KIẾM: \"" . htmlspecialchars($keyword) . "\"" : "KẾT QUẢ TÌM KIẾM & BỘ LỌC";
        $limit = 12;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();

        $totalRecords = $this->productDAO->countSearchAdvanced($filters);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->searchAdvanced($filters, $limit, $offset);

        require __DIR__ . "/../../views/client/products/index.php";
    }
}
