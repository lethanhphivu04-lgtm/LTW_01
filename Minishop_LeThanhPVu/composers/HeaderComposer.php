<?php
namespace Composers;

use DAO\CategoryDAO;
use DAO\BrandDAO;

class HeaderComposer
{
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
