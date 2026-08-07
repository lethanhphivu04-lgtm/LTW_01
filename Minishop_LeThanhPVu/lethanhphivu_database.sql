-- ========================================================

CREATE DATABASE IF NOT EXISTS `lethanhphivu_database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lethanhphivu_database`;

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Xóa các bảng theo thứ tự bảng con trước để tránh lỗi khóa ngoại (Foreign Key #1451)
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;

-- 1. Bảng categories (10 danh mục)
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `catename` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `catename`, `slug`, `image`, `description`, `status`) VALUES
(1, 'Chuột máy tính', 'chuot-may-tinh', 'chuot.png', 'Các loại chuột không dây và có dây', 1),
(2, 'Bàn phím cơ', 'ban-phim-co', 'banphim.png', 'Bàn phím cơ gaming và văn phòng', 1),
(3, 'Tai nghe Gaming', 'tai-nghe-gaming', 'tainghe.png', 'Tai nghe âm thanh vòm chất lượng cao', 1),
(4, 'Màn hình hiển thị', 'man-hinh-hien-thi', 'manhinh.png', 'Màn hình 4K, 144Hz cho đồ họa và chơi game', 1),
(5, 'Laptop văn phòng', 'laptop-van-phong', 'laptop.png', 'Laptop mỏng nhẹ, pin trâu cho dân công sở', 1),
(6, 'Loa Bluetooth & Soundbar', 'loa-bluetooth-soundbar', 'loa.png', 'Loa nghe nhạc không dây bass trầm', 1),
(7, 'Webcam & Microphone', 'webcam-microphone', 'webcam.png', 'Webcam Full HD 4K và micro thu âm stream', 1),
(8, 'Ổ cứng SSD & HDD', 'o-cung-ssd-hdd', 'ssd.png', 'Ổ cứng di động và ổ cứng gắn trong tốc độ cao', 1),
(9, 'Bàn ghế Ergonomic', 'ban-ghe-ergonomic', 'ghee.png', 'Bàn nâng hạ và ghế công hòa học bảo vệ cột sống', 1),
(10, 'Phụ kiện & Cáp chuyển', 'phu-kien-cap-chuyen', 'phukien.png', 'Hub USB-C, cáp HDMI, sạc nhanh GaN', 1);

-- 2. Bảng brands (15 thương hiệu)
CREATE TABLE `brands` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `brandname` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `brands` (`id`, `brandname`, `slug`, `image`, `description`, `status`) VALUES
(1, 'Logitech', 'logitech', 'logitech.png', 'Thương hiệu phụ kiện số 1 thế giới', 1),
(2, 'Razer', 'razer', 'razer.png', 'Thương hiệu gaming gear hàng đầu', 1),
(3, 'Dell', 'dell', 'dell.png', 'Thương hiệu máy tính và màn hình bền bỉ', 1),
(4, 'Asus', 'asus', 'asus.png', 'Thương hiệu công nghệ hàng đầu Đài Loan', 1),
(5, 'Apple', 'apple', 'apple.png', 'Hệ sinh thái sản phẩm cao cấp Apple', 1),
(6, 'Corsair', 'corsair', 'corsair.png', 'Thương hiệu phụ kiện máy tính hàng đầu Mỹ', 1),
(7, 'SteelSeries', 'steelseries', 'steelseries.png', 'Thiết bị gaming gear chuyên nghiệp Đan Mạch', 1),
(8, 'Kingston', 'kingston', 'kingston.png', 'Thương hiệu bộ nhớ & SSD hàng đầu thế giới', 1),
(9, 'Samsung', 'samsung', 'samsung.png', 'Tập đoàn công nghệ điện tử hàng đầu Hàn Quốc', 1),
(10, 'LG', 'lg', 'lg.png', 'Màn hình OLED và thiết bị điện tử gia dụng', 1),
(11, 'Sony', 'sony', 'sony.png', 'Âm thanh tai nghe chống ồn hàng đầu Nhật Bản', 1),
(12, 'JBL', 'jbl', 'jbl.png', 'Thương hiệu loa nghe nhạc nổi tiếng Mỹ', 1),
(13, 'Keychron', 'keychron', 'keychron.png', 'Bàn phím cơ không dây bán chạy nhất cho Mac/Windows', 1),
(14, 'Akko', 'akko', 'akko.png', 'Bàn phím cơ thời trang và phím custom nổi tiếng', 1),
(15, 'Anker', 'anker', 'anker.png', 'Phụ kiện sạc cáp và tai nghe không dây chất lượng cao', 1);

-- 3. Bảng products (20 sản phẩm)
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `brand_id` INT NOT NULL,
  `proname` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `price` DECIMAL(10,0) NOT NULL,
  `discount_price` DECIMAL(10,0) NOT NULL,
  `quantity` INT DEFAULT 0,
  `image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `proname`, `slug`, `price`, `discount_price`, `quantity`, `image`, `description`, `status`) VALUES
(1, 1, 1, 'Chuột Logitech MX Master 3S', 'chuot-logitech-mx-master-3s', 2490000, 2190000, 15, 'mx_master_3s.png', 'Chuột không dây siêu nhạy cho lập trình viên', 1),
(2, 2, 2, 'Bàn phím Razer BlackWidow V4', 'ban-phim-razer-blackwidow-v4', 3890000, 3490000, 10, 'razer_v4.png', 'Bàn phím cơ RGB cao cấp', 1),
(3, 4, 3, 'Màn hình Dell UltraSharp U2723QE 27 inch 4K', 'man-hinh-dell-ultrasharp-u2723qe', 14500000, 13800000, 8, 'dell_u2723qe.png', 'Màn hình chuẩn màu đồ họa chuyên nghiệp', 1),
(4, 5, 4, 'Laptop Asus ROG Zephyrus G14', 'laptop-asus-rog-zephyrus-g14', 32000000, 29900000, 5, 'asus_g14.png', 'Laptop gaming siêu mỏng gọn', 1),
(5, 3, 1, 'Tai nghe Logitech G Pro X Wireless', 'tai-nghe-logitech-g-pro-x-wireless', 4500000, 3990000, 12, 'logitech_gpro.png', 'Tai nghe không dây chuẩn eSports', 1),
(6, 2, 13, 'Bàn phím cơ Keychron K2 Pro Wireless', 'ban-phim-co-keychron-k2-pro-wireless', 2290000, 1990000, 20, 'keychron_k2_pro.png', 'Bàn phím cơ không dây Hotswap mạch ngược', 1),
(7, 1, 1, 'Chuột Logitech G Pro X Superlight 2', 'chuot-logitech-g-pro-x-superlight-2', 3690000, 3290000, 18, 'gpro_superlight2.png', 'Chuột gaming siêu nhẹ 60g cho game thủ', 1),
(8, 3, 11, 'Tai nghe chống ồn Sony WH-1000XM5', 'tai-nghe-chong-on-sony-wh-1000xm5', 8490000, 7690000, 10, 'sony_wh1000xm5.png', 'Tai nghe chống ồn chủ động cao cấp nhất', 1),
(9, 6, 12, 'Loa Bluetooth JBL Charge 5', 'loa-bluetooth-jbl-charge-5', 3990000, 3490000, 25, 'jbl_charge5.png', 'Loa Bluetooth kháng nước IP67 pin 20 giờ', 1),
(10, 8, 9, 'Ổ cứng SSD Samsung 990 Pro 1TB NVMe', 'o-cung-ssd-samsung-990-pro-1tb-nvme', 3190000, 2890000, 30, 'samsung_990pro.png', 'SSD NVMe PCIe 4.0 tốc độ đọc 7450MB/s', 1),
(11, 4, 10, 'Màn hình LG UltraGear 27GP850-B 27 inch 2K 180Hz', 'man-hinh-lg-ultragear-27gp850-b', 9990000, 8990000, 12, 'lg_27gp850.png', 'Màn hình Nano IPS 1ms chuyên game', 1),
(12, 2, 14, 'Bàn phím cơ Akko 3068B Plus Black Gold', 'ban-phim-co-akko-3068b-plus-black-gold', 1890000, 1590000, 15, 'akko_3068b.png', 'Bàn phím 3 chế độ kết nối keycap PBT', 1),
(13, 7, 1, 'Webcam Logitech Brio 4K Ultra HD', 'webcam-logitech-brio-4k-ultra-hd', 4990000, 4390000, 8, 'logitech_brio.png', 'Webcam 4K HDR hỗ trợ Windows Hello', 1),
(14, 10, 15, 'Sạc nhanh Anker 737 GaNPrime 120W 3 cổng', 'sac-nhanh-anker-737-ganprime-120w', 1990000, 1690000, 40, 'anker_737.png', 'Củ sạc công nghệ GaN công suất lớn cho Laptop/Macbook', 1),
(15, 5, 5, 'Laptop Apple MacBook Air M2 8GB 256GB', 'laptop-apple-macbook-air-m2-8gb-256gb', 28990000, 26490000, 7, 'macbook_air_m2.png', 'MacBook thiết kế mỏng nhẹ chip M2 hiệu năng cao', 1),
(16, 3, 7, 'Tai nghe SteelSeries Arctis Nova Pro Wireless', 'tai-nghe-steelseries-arctis-nova-pro-wireless', 8990000, 7990000, 6, 'steelseries_nova_pro.png', 'Tai nghe Gaming không dây đổi pin nóng cao cấp', 1),
(17, 1, 2, 'Chuột Razer DeathAdder V3 Pro Wireless', 'chuot-razer-deathadder-v3-pro-wireless', 3790000, 3290000, 14, 'razer_deathadder_v3.png', 'Chuột Ergonomic siêu nhẹ 63g cho FPS', 1),
(18, 8, 8, 'Ổ cứng SSD Kingston NV2 1TB PCIe 4.0 NVMe', 'o-cung-ssd-kingston-nv2-1tb', 1790000, 1490000, 50, 'kingston_nv2.png', 'SSD M.2 NVMe giá rẻ tốc độ cao', 1),
(19, 9, 4, 'Ghế Gaming Asus ROG Destrier Ergo', 'ghe-gaming-asus-rog-destrier-ergo', 19990000, 17990000, 4, 'rog_destrier.png', 'Ghế chơi game công hòa học cao cấp bằng lưới', 1),
(20, 6, 11, 'Loa Bluetooth Sony SRS-XG300', 'loa-bluetooth-sony-srs-xg300', 6990000, 5990000, 9, 'sony_xg300.png', 'Loa di động âm thanh bùng nổ chống nước IP67', 1);

-- 4. Bảng product_images (hình ảnh sản phẩm)
CREATE TABLE `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_images` (`id`, `product_id`, `image`, `sort_order`) VALUES
(1, 1, 'mx_master_3s_detail1.png', 1),
(2, 1, 'mx_master_3s_detail2.png', 2),
(3, 2, 'razer_v4_detail1.png', 1),
(4, 3, 'dell_u2723qe_detail1.png', 1),
(5, 4, 'asus_g14_detail1.png', 1);

-- 5. Bảng users (người dùng/nhân viên)
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `role` TINYINT DEFAULT 0 COMMENT '0: Nhân viên, 1: Quản trị',
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `email`, `phone`, `address`, `role`, `status`) VALUES
(1, 'Lê Thanh Phi Vũ', 'admin', '123456', 'phivu@hitu.edu.vn', '0901234567', 'TP. Hồ Chí Minh', 1, 1),
(2, 'Nguyễn Văn An', 'nv_an', '123456', 'an.nguyen@minishop.com', '0912345678', 'Hà Nội', 0, 1),
(3, 'Trần Thị Bích', 'nv_bich', '123456', 'bich.tran@minishop.com', '0923456789', 'Đà Nẵng', 0, 1),
(4, 'Phạm Minh Cường', 'nv_cuong', '123456', 'cuong.pham@minishop.com', '0934567890', 'Cần Thơ', 0, 1),
(5, 'Hoàng Anh Dũng', 'nv_dung', '123456', 'dung.hoang@minishop.com', '0945678901', 'Hải Phòng', 0, 1);

-- 6. Bảng customers (17 khách hàng)
CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` (`id`, `fullname`, `phone`, `email`, `address`, `note`, `status`) VALUES
(1, 'Nguyễn Quốc Bảo', '0988776655', 'baonq@gmail.com', '123 Nguyễn Trãi, Q5, TP.HCM', 'Khách VIP', 1),
(2, 'Vũ Thị Hồng', '0977665544', 'hongvu@gmail.com', '456 Lê Duẩn, Q1, TP.HCM', 'Giao giờ hành chính', 1),
(3, 'Đặng Hoàng Nam', '0966554433', 'namdh@gmail.com', '789 Trần Hưng Đạo, Q5, TP.HCM', '', 1),
(4, 'Bùi Thị Mai', '0955443322', 'maibt@gmail.com', '321 Điện Biên Phủ, Q3, TP.HCM', '', 1),
(5, 'Đỗ Văn Hùng', '0944332211', 'hungdv@gmail.com', '654 Võ Văn Kiệt, Q1, TP.HCM', '', 1),
(6, 'Lê Văn Hoàng', '0933221100', 'hoanglv@gmail.com', '12 Lý Thường Kiệt, Q10, TP.HCM', 'Khách hay mua linh kiện', 1),
(7, 'Phan Thị Thanh', '0922110099', 'thanhpt@gmail.com', '56 Nguyễn Văn Cừ, Q5, TP.HCM', '', 1),
(8, 'Trịnh Quốc Tuấn', '0911009988', 'tuantq@gmail.com', '88 Lê Văn Sỹ, Q3, TP.HCM', 'Giao chiều T7', 1),
(9, 'Ngô Kim Ngân', '0900998877', 'ngannk@gmail.com', '101 Cách Mạng Tháng 8, Q10, TP.HCM', '', 1),
(10, 'Võ Thành Nam', '0899887766', 'namvt@gmail.com', '234 Xô Viết Nghệ Tĩnh, Bình Thạnh, TP.HCM', '', 1),
(11, 'Hồ Mỹ Linh', '0888776655', 'linhhm@gmail.com', '78 Nguyễn Thị Minh Khai, Q1, TP.HCM', '', 1),
(12, 'Đương Văn Khánh', '0877665544', 'khanhdv@gmail.com', '90 Phạm Văn Đồng, Thủ Đức, TP.HCM', 'Gọi trước khi giao 30p', 1),
(13, 'Nguyễn Thị Thu Thảo', '0866554433', 'thaontt@gmail.com', '45 Hoàng Diệu, Q4, TP.HCM', '', 1),
(14, 'Phạm Đức Anh', '0855443322', 'anhpd@gmail.com', '123 Ba Tháng Hai, Q10, TP.HCM', '', 1),
(15, 'Trần Bảo Ngọc', '0844332211', 'ngoctb@gmail.com', '67 Nguyễn Trãi, Q5, TP.HCM', '', 1),
(16, 'Lương Quang Huy', '0833221100', 'huylq@gmail.com', '89 Võ Thị Sáu, Q3, TP.HCM', '', 1),
(17, 'Đào Minh Trí', '0822110099', 'tridm@gmail.com', '213 Phan Xích Long, Phú Nhuận, TP.HCM', '', 1);

-- 7. Bảng orders (đơn hàng)
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `user_id` INT DEFAULT NULL,
  `order_code` VARCHAR(30) NOT NULL UNIQUE,
  `total_amount` DECIMAL(12,2) DEFAULT 0,
  `note` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 0 COMMENT '0: Chờ xử lý, 1: Hoàn thành, 2: Hủy',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `customer_id`, `user_id`, `order_code`, `total_amount`, `note`, `status`) VALUES
(1, 1, 1, 'DH001', 2190000.00, 'Đã cọc 500k', 1),
(2, 2, 2, 'DH002', 3490000.00, 'Thanh toán COD', 0),
(3, 3, 1, 'DH003', 13800000.00, 'Xuất hóa đơn GTGT', 1),
(4, 4, 3, 'DH004', 29900000.00, 'Chuyển khoản ngân hàng', 0),
(5, 5, 2, 'DH005', 3990000.00, 'Khách hẹn lấy tại cửa hàng', 2);

-- 8. Bảng order_details (chi tiết đơn hàng)
CREATE TABLE `order_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 1, 2190000.00, 2190000.00),
(2, 2, 2, 1, 3490000.00, 3490000.00),
(3, 3, 3, 1, 13800000.00, 13800000.00),
(4, 4, 4, 1, 29900000.00, 29900000.00),
(5, 5, 5, 1, 3990000.00, 3990000.00);

SET FOREIGN_KEY_CHECKS = 1;
