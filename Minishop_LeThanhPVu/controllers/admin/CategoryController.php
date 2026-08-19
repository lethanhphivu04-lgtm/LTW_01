<?php
namespace Controllers\Admin;

use DAO\CategoryDAO;
use Models\Category;

class CategoryController
{
    private CategoryDAO $categoryDAO;

    public function __construct()
    {
        $this->categoryDAO = new CategoryDAO();
    }

    public function index()
    {
        $pageTitle = "Quản lý Danh mục";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->categoryDAO->count("categories", "catename", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->categoryDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'id_asc' => 'ID tăng dần'
        ];

        require __DIR__ . "/../../views/admin/categories/index.php";
    }

    public function create()
    {
        $pageTitle = "Thêm danh mục mới";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->extractAndValidate($_POST, $errors);
            $image = $this->handleUpload($_FILES['image'] ?? [], null, $errors);

            if (empty($errors)) {
                $category = new Category(
                    $data['name'],
                    $data['slug'],
                    $image,
                    $data['description'],
                    $data['status']
                );
                $this->categoryDAO->insert($category);
                header("Location: index.php?area=admin&controller=category&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/categories/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryDAO->findById($id);
        if (!$category) {
            header("Location: index.php?area=admin&controller=category&action=index");
            exit;
        }

        $pageTitle = "Chỉnh sửa danh mục";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->extractAndValidate($_POST, $errors);
            $image = $this->handleUpload($_FILES['image'] ?? [], $category->image, $errors) ?? $category->image;

            if (empty($errors)) {
                $category->name = $data['name'];
                $category->slug = $data['slug'];
                $category->image = $image;
                $category->description = $data['description'];
                $category->status = $data['status'];

                $this->categoryDAO->update($category);
                header("Location: index.php?area=admin&controller=category&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/categories/edit.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryDAO->findById($id);
        if (!$category) {
            header("Location: index.php?area=admin&controller=category&action=index");
            exit;
        }

        $pageTitle = "Chi tiết danh mục - " . htmlspecialchars($category->name);
        require __DIR__ . "/../../views/admin/categories/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->categoryDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=category&action=index");
        exit;
    }

    // --- Helper Methods ---

    private function extractAndValidate(array $post, array &$errors): array
    {
        $name = trim($post['catename'] ?? '');
        $slug = trim($post['slug'] ?? '');
        $description = trim($post['description'] ?? '');
        $status = (int)($post['status'] ?? 1);

        if ($name === '') $errors[] = 'Tên danh mục không được để trống.';
        if ($slug === '' && $name !== '') {
            $slug = create_slug($name);
        }

        return [
            'name' => $name,
            'slug' => $slug,
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
        $uploadDir = __DIR__ . '/../../uploads/categories/';
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
}
