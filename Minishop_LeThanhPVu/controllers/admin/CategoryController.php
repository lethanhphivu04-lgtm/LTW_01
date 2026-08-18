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
            $name = trim($_POST['catename'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên danh mục không được để trống.';
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
                    $uploadDir = __DIR__ . "/../../uploads/categories/";
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
                $category = new Category(
                    $name,
                    $slug,
                    $image !== '' ? $image : null,
                    $description !== '' ? $description : null,
                    $status
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
            $name = trim($_POST['catename'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên danh mục không được để trống.';
            if ($slug === '') {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            }

            $image = $category->image;
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
                    $uploadDir = __DIR__ . "/../../uploads/categories/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    if (move_uploaded_file($tmpName, $uploadDir . $newImage)) {
                        if (!empty($category->image) && file_exists($uploadDir . $category->image)) {
                            unlink($uploadDir . $category->image);
                        }
                        $image = $newImage;
                    } else {
                        $errors[] = "Lỗi khi lưu file ảnh.";
                    }
                }
            }

            if (empty($errors)) {
                $category->name = $name;
                $category->slug = $slug;
                $category->image = $image;
                $category->description = $description;
                $category->status = $status;

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
}
