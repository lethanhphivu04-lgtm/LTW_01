<?php
/**
 * ============================================================================
 * MINISHOP - CƠ CHẾ AUTOLOADING & CÁC HÀM TIỆN ÍCH TOÀN CỤC
 * Tác giả: Lê Thanh Phi Vũ
 * ============================================================================
 */

// 1. ĐĂNG KÝ HÀM TỰ ĐỘNG NẠP CLASS (PSR-4 SIMPLIFIED AUTOLOADER)
spl_autoload_register(function ($className) {
    // Tương thích ngược cho các đối tượng Model được lưu trong Session cũ
    if (in_array($className, ['User', 'Product', 'Category', 'Brand', 'Customer', 'Order', 'OrderDetail', 'Coupon', 'Review', 'Banner', 'StockReceipt'])) {
        $modelFile = __DIR__ . '/models/' . $className . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (!class_exists($className, false)) {
                class_alias('Models\\' . $className, $className);
            }
            return;
        }
    }

    // Bản đồ ánh xạ Namespace sang Thư mục vật lý tương ứng
    $prefixes = [
        'Controllers\\'          => __DIR__ . '/controllers/',
        'Composers\\'            => __DIR__ . '/composers/',
        'DAO\\'                  => __DIR__ . '/dao/',
        'Models\\'               => __DIR__ . '/models/',
        'Middleware\\'           => __DIR__ . '/middleware/',
        'Config\\'               => __DIR__ . '/config/',
        'Services\\'             => __DIR__ . '/services/',
        'PHPMailer\\PHPMailer\\' => __DIR__ . '/services/PHPMailer/',
    ];

    // Duyệt qua các tiền tố và nạp file PHP phù hợp
    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($className, $prefix)) {
            $relativeClass = substr($className, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// 2. HÀM TẠO ĐƯỜNG DẪN THÂN THIỆN CHUẨN SEO (SLUG GENERATOR)
if (!function_exists('create_slug')) {
    /**
     * Chuyển đổi chuỗi tiếng Việt có dấu thành chuỗi không dấu chuẩn SEO
     * Ví dụ: "Bàn phím cơ Asus ROG" -> "ban-phim-co-asus-rog"
     */
    function create_slug(string $str): string
    {
        // Chuyển nguyên âm thường có dấu thành không dấu
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", "y", $str);
        $str = preg_replace("/(đ)/u", "d", $str);

        // Chuyển nguyên âm hoa có dấu thành không dấu
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/u", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u", "Y", $str);
        $str = preg_replace("/(Đ)/u", "D", $str);

        // Thay thế toàn bộ ký tự đặc biệt / khoảng trắng thành dấu '-'
        $str = preg_replace("/[^A-Za-z0-9-]+/", "-", $str);

        // Thu gọn nhiều dấu gạch ngang liên tiếp thành 1 dấu duy nhất
        $str = preg_replace("/-+/", "-", $str);

        // Chuyển về chữ thường và cắt bỏ dấu '-' ở đầu và cuối chuỗi
        return strtolower(trim($str, '-'));
    }
}
