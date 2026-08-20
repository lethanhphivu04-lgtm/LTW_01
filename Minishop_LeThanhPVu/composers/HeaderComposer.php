<?php
namespace Composers;

use DAO\CategoryDAO;
use DAO\BrandDAO;

/**
 * View Composer cung cấp dữ liệu dùng chung cho Header toàn trang Client
 * (Tự động nạp Danh mục & Thương hiệu cho menu Dropdown mà không cần Controller truyền vào).
 */
class HeaderComposer
{
    /**
     * Lấy dữ liệu danh mục & thương hiệu hiển thị trên thanh Menu điều hướng
     */
    public static function compose(): array
    {
        $categoryDAO = new CategoryDAO();
        $brandDAO = new BrandDAO();
        return [
            'headerCategories' => $categoryDAO->getByLimit(8),
            'headerBrands'     => $brandDAO->getByLimit(8),
        ];
    }
}
