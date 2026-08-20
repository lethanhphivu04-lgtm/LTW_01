<?php
namespace Controllers\Client;

use Services\ChatbotService;

/**
 * Controller tiếp nhận yêu cầu trò chuyện từ Chatbot Widget phía Client (API JSON)
 */
class ChatbotController
{
    /**
     * Tiếp nhận câu hỏi và trả về câu trả lời tự động kèm sản phẩm gợi ý
     */
    public function send()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Đọc dữ liệu JSON gửi lên từ Fetch AJAX
        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? $_POST['message'] ?? '');

        if ($message === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Nội dung tin nhắn không được để trống.'
            ]);
            exit;
        }

        try {
            // Gọi ChatbotService xử lý phân tích từ khóa và tra cứu CSDL
            $result = ChatbotService::reply($message);
            echo json_encode([
                'success'  => true,
                'reply'    => $result['reply'],
                'products' => $result['products'] ?? []
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'reply'   => 'Dạ hệ thống đang bận một chút, anh/chị thử lại sau giây lát nhé!',
                'error'   => $e->getMessage()
            ]);
        }
        exit;
    }
}
