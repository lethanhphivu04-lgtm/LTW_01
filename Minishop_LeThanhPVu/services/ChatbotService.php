<?php
namespace Services;

use DAO\ProductDAO;

/**
 * Service xử lý logic tư vấn tự động cho Chatbot
 * Phân tích từ khóa câu hỏi của người dùng và truy vấn CSDL trả về câu trả lời kèm sản phẩm.
 */
class ChatbotService
{
    /**
     * Xử lý trả lời tự động bằng PHP thuần kết hợp CSDL
     */
    public static function reply(string $userMessage): array
    {
        $msg = mb_strtolower(trim($userMessage), 'UTF-8');
        $productDAO = new ProductDAO();

        // 1. Nhận diện chào hỏi
        if (self::contains($msg, ['chào', 'hello', 'hi', 'alo', 'bạn là ai'])) {
            return [
                'reply' => "Dạ chào bạn! Mình là **Trợ lý tự động của MiniShop** 🤖. Bạn cần tìm sản phẩm gì (chuột, bàn phím, màn hình, tai nghe...) hay cần hỗ trợ thông tin nào cứ nhắn mình nhé!",
                'products' => []
            ];
        }

        // 2. Nhận diện hỏi về Sản phẩm giảm giá / Khuyến mãi
        if (self::contains($msg, ['giảm giá', 'khuyến mãi', 'sale', 'ưu đãi', 'rẻ nhất'])) {
            $discountProducts = $productDAO->getDiscounted(4);
            return [
                'reply' => "🔥 **Các sản phẩm đang có giá ưu đãi tốt nhất tại MiniShop:**",
                'products' => $discountProducts
            ];
        }

        // 3. Nhận diện hỏi về Phương thức thanh toán
        if (self::contains($msg, ['thanh toán', 'vnpay', 'cod', 'tiền mặt', 'chuyển khoản'])) {
            return [
                'reply' => "💳 **MiniShop hỗ trợ 2 hình thức thanh toán:**\n"
                         . "1. **COD:** Nhận hàng rồi thanh toán tiền mặt cho shipper.\n"
                         . "2. **Cổng VNPay:** Thanh toán online an toàn qua thẻ ATM hoặc quét mã QR.",
                'products' => []
            ];
        }

        // 4. Nhận diện hỏi về Chính sách bảo hành & giao hàng
        if (self::contains($msg, ['bảo hành', 'đổi trả', 'giao hàng', 'ship', 'địa chỉ'])) {
            return [
                'reply' => "📦 **Chính sách hỗ trợ khách hàng:**\n"
                         . "• **Bảo hành:** Chính hãng 12 - 24 tháng.\n"
                         . "• **Đổi trả:** 1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất.\n"
                         . "• **Giao hàng:** Toàn quốc từ 2 - 4 ngày làm việc.\n"
                         . "• **Hotline:** 0123-456-789.",
                'products' => []
            ];
        }

        // 5. Nhận diện hỏi về Tra cứu đơn hàng
        if (self::contains($msg, ['tra cứu', 'kiểm tra đơn', 'tình trạng đơn'])) {
            return [
                'reply' => "🔍 Bạn có thể vào mục **[Tra cứu đơn hàng](index.php?area=client&controller=cart&action=tracking)** trên menu, sau đó nhập **Mã đơn hàng** và **SĐT** để kiểm tra nhé!",
                'products' => []
            ];
        }

        // 6. Nhận diện tìm kiếm theo tên hoặc danh mục sản phẩm trong CSDL
        $searchTerms = ['chuột', 'bàn phím', 'màn hình', 'laptop', 'tai nghe', 'loa', 'macbook', 'asus', 'logitech', 'razer', 'sony', 'dell'];
        $foundKeyword = null;
        foreach ($searchTerms as $term) {
            if (str_contains($msg, $term)) {
                $foundKeyword = $term;
                break;
            }
        }

        if ($foundKeyword) {
            $matchedProducts = $productDAO->searchByNameOrKeyword($foundKeyword, 4);
            if (!empty($matchedProducts)) {
                return [
                    'reply' => "✨ Đây là một số mẫu **" . ucfirst($foundKeyword) . "** nổi bật tại shop dành cho bạn:",
                    'products' => $matchedProducts
                ];
            }
        }

        // 7. Tìm kiếm tự do theo câu người dùng nhập
        $cleanedKeyword = preg_replace('/(tìm|mua|xem|bán|có|không|giá|bao nhiêu)/u', '', $msg);
        $cleanedKeyword = trim($cleanedKeyword);
        if (mb_strlen($cleanedKeyword, 'UTF-8') >= 2) {
            $matchedProducts = $productDAO->searchByNameOrKeyword($cleanedKeyword, 4);
            if (!empty($matchedProducts)) {
                return [
                    'reply' => "🔎 Dưới đây là các sản phẩm phù hợp với tìm kiếm của bạn:",
                    'products' => $matchedProducts
                ];
            }
        }

        // 8. Trả lời mặc định nếu không khớp
        return [
            'reply' => "Dạ MiniShop có rất nhiều thiết bị công nghệ chính hãng như **Chuột, Bàn phím, Màn hình, Tai nghe, Laptop**... Bạn có thể gõ tên sản phẩm cần tìm hoặc gọi hotline **0123-456-789** để được tư vấn nhanh nhất nhé!",
            'products' => []
        ];
    }

    /**
     * Hàm phụ trợ kiểm tra chuỗi có chứa bất kỳ từ khóa nào trong danh sách
     */
    private static function contains(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }
}
