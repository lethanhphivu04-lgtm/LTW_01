<?php
require_once __DIR__ . "/dao/ProductDAO.php";
$dao = new ProductDAO();
$currentCount = $dao->count("products");
echo "Current count: $currentCount\n";

if ($currentCount < 43) {
    for ($i = $currentCount + 1; $i <= 43; $i++) {
        $p = new Product(
            1, 
            1, 
            "Sản phẩm" . $i, 
            "san-pham-test-" . $i, 
            $i * 10000, 
            $i * 9000, 
            15, 
            "anker.png", 
            "Mô tả " . $i, 
            1
        );
        $dao->insert($p);
    }
    echo "New product count: " . $dao->count("products") . "\n";
}
