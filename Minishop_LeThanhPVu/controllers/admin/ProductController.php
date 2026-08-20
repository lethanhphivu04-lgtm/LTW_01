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
            $data = $this->extractAndValidate($_POST, $errors);
            $image = $this->handleUpload($_FILES['image'] ?? [], null, $errors);
            $galleryFiles = $this->handleMultipleUpload($_FILES['gallery'] ?? [], $errors);

            if (empty($errors)) {
                $p = new Product(
                    $data['category_id'],
                    $data['brand_id'],
                    $data['name'],
                    $data['slug'],
                    $data['price'],
                    $data['discount_price'],
                    $data['quantity'],
                    $image,
                    $data['description'],
                    $data['status']
                );
                $newId = $this->productDAO->insert($p);
                if ($newId && is_numeric($newId)) {
                    foreach ($galleryFiles as $gFile) {
                        $this->productDAO->insertImage((int)$newId, $gFile);
                    }
                }
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
        $galleryImages = $this->productDAO->getImagesByProductId($id);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->extractAndValidate($_POST, $errors);
            $image = $this->handleUpload($_FILES['image'] ?? [], $product->image, $errors) ?? $product->image;
            $galleryFiles = $this->handleMultipleUpload($_FILES['gallery'] ?? [], $errors);

            if (empty($errors)) {
                $product->categoryId = $data['category_id'];
                $product->brandId = $data['brand_id'];
                $product->name = $data['name'];
                $product->slug = $data['slug'];
                $product->price = $data['price'];
                $product->discountPrice = $data['discount_price'];
                $product->quantity = $data['quantity'];
                $product->image = $image;
                $product->description = $data['description'];
                $product->status = $data['status'];

                $this->productDAO->update($product);

                // Lưu các ảnh phụ mới
                foreach ($galleryFiles as $gFile) {
                    $this->productDAO->insertImage($id, $gFile);
                }

                header("Location: index.php?area=admin&controller=product&action=edit&id=" . $id . "&success=1");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/products/edit.php";
    }

    public function deleteGalleryImage()
    {
        $imageId = (int)($_GET['image_id'] ?? 0);
        $productId = (int)($_GET['product_id'] ?? 0);
        if ($imageId > 0) {
            $this->productDAO->deleteImage($imageId);
        }
        if ($productId > 0) {
            header("Location: index.php?area=admin&controller=product&action=edit&id=" . $productId);
        } else {
            header("Location: index.php?area=admin&controller=product&action=index");
        }
        exit;
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

    // --- Helper Methods ---

    private function extractAndValidate(array $post, array &$errors): array
    {
        $name = trim($post['proname'] ?? '');
        $slug = trim($post['slug'] ?? '');
        $categoryId = (int)($post['category_id'] ?? 0);
        $brandId = (int)($post['brand_id'] ?? 0);
        $price = (float)($post['price'] ?? 0);
        $discountPrice = (float)($post['discount_price'] ?? 0);
        $quantity = (int)($post['quantity'] ?? 0);
        $description = trim($post['description'] ?? '');
        $status = (int)($post['status'] ?? 1);

        if ($name === '') $errors[] = 'Tên sản phẩm không được để trống.';
        if ($categoryId <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm.';
        if ($brandId <= 0) $errors[] = 'Vui lòng chọn thương hiệu.';
        if ($price <= 0) $errors[] = 'Giá gốc phải lớn hơn 0.';
        if ($discountPrice < 0) $errors[] = 'Giá bán không được nhỏ hơn 0.';
        if ($quantity < 0) $errors[] = 'Số lượng không được nhỏ hơn 0.';

        if ($slug === '' && $name !== '') {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'price' => $price,
            'discount_price' => $discountPrice,
            'quantity' => $quantity,
            'description' => $description !== '' ? $description : null,
            'status' => $status,
        ];
    }

    private function handleUpload(array $file, ?string $oldImage, array &$errors): ?string
    {
        $fileName = $file['name'] ?? '';
        if ($fileName === '') return null;

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload hình ảnh không thành công.';
            return null;
        }

        $allowExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowExtensions)) {
            $errors[] = 'Chỉ cho phép file JPG, JPEG, PNG, GIF hoặc WEBP.';
            return null;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $errors[] = 'Kích thước hình ảnh <= 2 MB.';
            return null;
        }

        $newImage = time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $newImage)) {
            if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
            return $newImage;
        }

        $errors[] = 'Lỗi khi lưu file ảnh.';
        return null;
    }

    private function handleMultipleUpload(array $files, array &$errors): array
    {
        $uploaded = [];
        if (empty($files['name']) || !is_array($files['name'])) {
            return $uploaded;
        }

        $allowExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $name = $files['name'][$i] ?? '';
            $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            $tmpName = $files['tmp_name'][$i] ?? '';
            $size = $files['size'][$i] ?? 0;

            if ($error === UPLOAD_ERR_NO_FILE || empty($name)) {
                continue;
            }

            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "Lỗi khi tải lên file ảnh phụ: " . htmlspecialchars($name);
                continue;
            }

            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowExtensions)) {
                $errors[] = "Ảnh phụ '$name' không đúng định dạng (cho phép JPG, JPEG, PNG, GIF, WEBP).";
                continue;
            }

            if ($size > 2 * 1024 * 1024) {
                $errors[] = "Ảnh phụ '$name' vượt quá kích thước 2 MB.";
                continue;
            }

            $newFileName = time() . '_' . $i . '_' . uniqid() . '.' . $extension;
            if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                $uploaded[] = $newFileName;
            } else {
                $errors[] = "Không thể lưu file ảnh phụ: " . htmlspecialchars($name);
            }
        }

        return $uploaded;
    }
}
