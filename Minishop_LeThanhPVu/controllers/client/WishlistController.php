<?php
namespace Controllers\Client;

use DAO\ProductDAO;

/**
 * Controller xử lý Danh sách sản phẩm yêu thích (Wishlist)
 * Hỗ trợ lưu trữ bền vững qua cả Session và Cookie 30 ngày cho khách vãng lai.
 */
class WishlistController
{
    private ProductDAO $productDAO;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->productDAO = new ProductDAO();
    }

    /**
     * Lấy mảng ID các sản phẩm yêu thích từ Session hoặc Cookie
     */
    private function getWishlist(): array
    {
        if (isset($_SESSION['wishlist'])) {
            return $_SESSION['wishlist'];
        }
        if (!empty($_COOKIE['minishop_wishlist'])) {
            $data = json_decode($_COOKIE['minishop_wishlist'], true);
            if (is_array($data)) {
                $_SESSION['wishlist'] = $data;
                return $data;
            }
        }
        return [];
    }

    /**
     * Hiển thị trang Danh sách sản phẩm yêu thích
     */
    public function index()
    {
        $wishlistIds = $this->getWishlist();
        $products = [];
        foreach ($wishlistIds as $id) {
            $p = $this->productDAO->findById((int)$id);
            if ($p && $p->status === 1) {
                $products[] = $p;
            }
        }

        $pageTitle = "Sản phẩm yêu thích";
        require __DIR__ . "/../../views/client/wishlist/index.php";
    }

    /**
     * Thêm / Bỏ yêu thích sản phẩm qua AJAX
     */
    public function toggle()
    {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true);
        $productId = (int)($input['product_id'] ?? $_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            exit;
        }

        $wishlist = $_SESSION['wishlist'] ?? [];
        $key = array_search($productId, $wishlist);

        if ($key !== false) {
            // 1. Đã có trong danh sách -> Xóa bỏ thích
            unset($wishlist[$key]);
            $isWishlisted = false;
            $msg = 'Đã bỏ sản phẩm khỏi danh sách yêu thích.';
        } else {
            // 2. Chưa có trong danh sách -> Thêm vào yêu thích
            $wishlist[] = $productId;
            $isWishlisted = true;
            $msg = 'Đã thêm sản phẩm vào danh sách yêu thích ❤️';
        }

        $wishlist = array_values($wishlist);
        $_SESSION['wishlist'] = $wishlist;

        // Lưu vào Cookie 30 ngày để khách tắt trình duyệt mở lại vẫn còn nguyên
        setcookie('minishop_wishlist', json_encode($wishlist), time() + 30 * 86400, '/');

        echo json_encode([
            'success' => true,
            'is_wishlisted' => $isWishlisted,
            'count' => count($wishlist),
            'message' => $msg
        ]);
        exit;
    }

    /**
     * Trả về số lượng sản phẩm yêu thích (API JSON)
     */
    public function count()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'count' => count($this->getWishlist())
        ]);
        exit;
    }
}
