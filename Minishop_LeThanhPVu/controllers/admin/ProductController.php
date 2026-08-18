<?php
namespace Controllers\Admin;

use DAO\ProductDAO;
use DAO\CategoryDAO;
use DAO\BrandDAO;
use Models\Product;

class ProductController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;
    private BrandDAO $brandDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
    }

    public function index()
    {
        $pageTitle = "Danh sách sản phẩm";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->productDAO->count("products", "proname", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->productDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'price_asc' => 'Giá tăng dần',
            'price_desc' => 'Giá giảm dần'
        ];

        require __DIR__ . "/../../views/admin/products/index.php";
    }

    public function create()
    {
        $pageTitle = "Thêm sản phẩm";
        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['proname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $brandId = (int)($_POST['brand_id'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            $discountPrice = (float)($_POST['discount_price'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên sản phẩm không được để trống.';
            if ($categoryId <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm.';
            if ($brandId <= 0) $errors[] = 'Vui lòng chọn thương hiệu.';
            if ($price <= 0) $errors[] = 'Giá gốc phải lớn hơn 0.';
            if ($discountPrice < 0) $errors[] = 'Giá bán không được nhỏ hơn 0.';
            if ($quantity < 0) $errors[] = 'Số lượng không được nhỏ hơn 0.';

            if ($slug === '') {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            }

            $image = "";
            $fileName = $_FILES["image"]["name"] ?? "";
            $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
            $fileSize = $_FILES["image"]["size"] ?? 0;
            $error    = $_FILES["image"]["error"] ?? 0;

            if ($fileName != "") {
                if ($error != UPLOAD_ERR_OK) {
                    $errors[] = "Upload hình ảnh không thành công.";
                }
                $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowExtensions)) {
                    $errors[] = "Chỉ cho phép file JPG, JPEG, PNG, GIF hoặc WEBP.";
                }
                $maxSize = 2 * 1024 * 1024;
                if ($fileSize > $maxSize) {
                    $errors[] = "Kích thước hình ảnh <= 2 MB.";
                }
                if (empty($errors)) {
                    $image = time() . "_" . uniqid() . "." . $extension;
                    $uploadDir = __DIR__ . "/../../uploads/products/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    if (!move_uploaded_file($tmpName, $uploadDir . $image)) {
                        $errors[] = "Lỗi khi lưu file ảnh.";
                        $image = "";
                    }
                }
            }

            if (empty($errors)) {
                $p = new Product(
                    $categoryId,
                    $brandId,
                    $name,
                    $slug,
                    $price,
                    $discountPrice,
                    $quantity,
                    $image !== '' ? $image : null,
                    $description !== '' ? $description : null,
                    $status
                );
                $this->productDAO->insert($p);
                header("Location: index.php?area=admin&controller=product&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/products/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productDAO->findById($id);
        if (!$product) {
            header("Location: index.php?area=admin&controller=product&action=index");
            exit;
        }

        $pageTitle = "Chỉnh sửa sản phẩm";
        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['proname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $brandId = (int)($_POST['brand_id'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            $discountPrice = (float)($_POST['discount_price'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên sản phẩm không được để trống.';
            if ($categoryId <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm.';
            if ($brandId <= 0) $errors[] = 'Vui lòng chọn thương hiệu.';
            if ($price <= 0) $errors[] = 'Giá gốc phải lớn hơn 0.';
            if ($discountPrice < 0) $errors[] = 'Giá bán không được nhỏ hơn 0.';
            if ($quantity < 0) $errors[] = 'Số lượng không được nhỏ hơn 0.';

            if ($slug === '') {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            }

            $image = $product->image;
            $fileName = $_FILES["image"]["name"] ?? "";
            $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
            $fileSize = $_FILES["image"]["size"] ?? 0;
            $error    = $_FILES["image"]["error"] ?? 0;

            if ($fileName != "") {
                if ($error != UPLOAD_ERR_OK) {
                    $errors[] = "Upload hình ảnh không thành công.";
                }
                $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowExtensions)) {
                    $errors[] = "Chỉ cho phép file JPG, JPEG, PNG, GIF hoặc WEBP.";
                }
                $maxSize = 2 * 1024 * 1024;
                if ($fileSize > $maxSize) {
                    $errors[] = "Kích thước hình ảnh <= 2 MB.";
                }
                if (empty($errors)) {
                    $newImage = time() . "_" . uniqid() . "." . $extension;
                    $uploadDir = __DIR__ . "/../../uploads/products/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    if (move_uploaded_file($tmpName, $uploadDir . $newImage)) {
                        if (!empty($product->image) && file_exists($uploadDir . $product->image)) {
                            unlink($uploadDir . $product->image);
                        }
                        $image = $newImage;
                    } else {
                        $errors[] = "Lỗi khi lưu file ảnh.";
                    }
                }
            }

            if (empty($errors)) {
                $product->categoryId = $categoryId;
                $product->brandId = $brandId;
                $product->name = $name;
                $product->slug = $slug;
                $product->price = $price;
                $product->discountPrice = $discountPrice;
                $product->quantity = $quantity;
                $product->image = $image;
                $product->description = $description;
                $product->status = $status;

                $this->productDAO->update($product);
                header("Location: index.php?area=admin&controller=product&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/products/edit.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productDAO->findByIdWithJoin($id);
        if (!$product) {
            header("Location: index.php?area=admin&controller=product&action=index");
            exit;
        }

        $pageTitle = "Chi tiết sản phẩm - " . htmlspecialchars($product['proname']);
        $galleryImages = $this->productDAO->getImagesByProductId($id);

        require __DIR__ . "/../../views/admin/products/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->productDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=product&action=index");
        exit;
    }
}
