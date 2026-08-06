-- ========================================================
-- Cơ sở dữ liệu: lethanhphivu_database
-- Sinh viên: Lê Thanh Phi Vũ
-- Lab 6 - MiniShop Mini Project
-- ========================================================

CREATE DATABASE IF NOT EXISTS `lethanhphivu_database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lethanhphivu_database`;

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- 1. Bảng categories (danh mục)
DROP TABLE IF EXISTS `categories`;
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
(5, 'Laptop văn phòng', 'laptop-van-phong', 'laptop.png', 'Laptop mỏng nhẹ, pin trâu cho dân công sở', 1);

-- 2. Bảng brands (thương hiệu)
DROP TABLE IF EXISTS `brands`;
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
(5, 'Apple', 'apple', 'apple.png', 'Hệ sinh thái sản phẩm cao cấp Apple', 1);

-- 3. Bảng products (sản phẩm)
DROP TABLE IF EXISTS `products`;
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
(5, 3, 1, 'Tai nghe Logitech G Pro X Wireless', 'tai-nghe-logitech-g-pro-x-wireless', 4500000, 3990000, 12, 'logitech_gpro.png', 'Tai nghe không dây chuẩn eSports', 1);

-- 4. Bảng product_images (hình ảnh sản phẩm)
DROP TABLE IF EXISTS `product_images`;
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
DROP TABLE IF EXISTS `users`;
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

-- 6. Bảng customers (khách hàng)
DROP TABLE IF EXISTS `customers`;
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
(5, 'Đỗ Văn Hùng', '0944332211', 'hungdv@gmail.com', '654 Võ Văn Kiệt, Q1, TP.HCM', '', 1);

-- 7. Bảng orders (đơn hàng)
DROP TABLE IF EXISTS `orders`;
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
DROP TABLE IF EXISTS `order_details`;
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
