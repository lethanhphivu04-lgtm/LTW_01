<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO;
use DAO\BannerDAO;

/**
 * Controller xử lý Trang chủ và Các trang thông tin tĩnh phía Client
 */
class HomeController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;
    private BannerDAO $bannerDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->bannerDAO = new BannerDAO();
    }

    /**
     * Hiển thị Trang chủ MiniShop
     * Lấy dữ liệu: Banner Slider, Danh mục nổi bật, Sản phẩm giảm giá Flash Sale,
     *              Sản phẩm bán chạy nhất, Sản phẩm mới nhất.
     */
    public function index()
    {
        $pageTitle = "MiniShop - Cửa hàng công nghệ & Phụ kiện chính hãng";
        
        // 1. Lấy danh sách banner slider đang kích hoạt
        $banners = $this->bannerDAO->getActive();
        
        // 2. Lấy 6 danh mục hàng đầu
        $featuredCategories = $this->categoryDAO->getByLimit(6);
        
        // 3. Lấy 8 sản phẩm giảm giá cho khu vực Flash Sale
        $discountedProducts = $this->productDAO->getDiscounted(8);
        
        // 4. Lấy 4 sản phẩm bán chạy nhất (Top Sellers từ bảng order_details)
        $bestSellingProducts = $this->productDAO->getBestSelling(4);
        
        // 5. Lấy 8 sản phẩm mới nhập về gần đây nhất
        $newestProducts = $this->productDAO->getNewestClient(8);

        require __DIR__ . "/../../views/client/home/index.php";
    }

    /**
     * Hiển thị trang Chính sách cửa hàng (Bảo hành, Đổi trả 30 ngày, Giao hàng, Thanh toán)
     */
    public function policy()
    {
        $type = trim($_GET['type'] ?? 'warranty');
        $validTypes = ['warranty', 'return', 'shipping', 'payment'];
        if (!in_array($type, $validTypes)) {
            $type = 'warranty';
        }

        $titles = [
            'warranty' => 'Chính sách bảo hành chính hãng',
            'return'   => 'Chính sách đổi trả trong 30 ngày',
            'shipping' => 'Chính sách giao hàng toàn quốc',
            'payment'  => 'Hướng dẫn mua hàng và thanh toán'
        ];

        $pageTitle = $titles[$type] ?? "Chính sách & Hỗ trợ";
        require __DIR__ . "/../../views/client/home/policy.php";
    }
}
