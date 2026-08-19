<?php
namespace Controllers\Admin;

use DAO\BrandDAO;
use Models\Brand;

class BrandController
{
    private BrandDAO $brandDAO;

    public function __construct()
    {
        $this->brandDAO = new BrandDAO();
    }

    public function index()
    {
        $pageTitle = "Quản lý Thương hiệu";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->brandDAO->count("brands", "brandname", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->brandDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'id_asc' => 'ID tăng dần'
        ];

        require __DIR__ . "/../../views/admin/brands/index.php";
    }

    public function create()
    {
        $pageTitle = "Thêm thương hiệu mới";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['brandname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên thương hiệu không được để trống.';
            if ($slug === '') {
                $slug = create_slug($name);
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
                    $uploadDir = __DIR__ . "/../../uploads/brands/";
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
                $brand = new Brand(
                    $name,
                    $slug,
                    $image !== '' ? $image : null,
                    $description !== '' ? $description : null,
                    $status
                );
                $this->brandDAO->insert($brand);
                header("Location: index.php?area=admin&controller=brand&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/brands/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $brand = $this->brandDAO->findById($id);
        if (!$brand) {
            header("Location: index.php?area=admin&controller=brand&action=index");
            exit;
        }

        $pageTitle = "Chỉnh sửa thương hiệu";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['brandname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($name === '') $errors[] = 'Tên thương hiệu không được để trống.';
            if ($slug === '') {
                $slug = create_slug($name);
            }

            $image = $brand->image;
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
                    $uploadDir = __DIR__ . "/../../uploads/brands/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    if (move_uploaded_file($tmpName, $uploadDir . $newImage)) {
                        if (!empty($brand->image) && file_exists($uploadDir . $brand->image)) {
                            unlink($uploadDir . $brand->image);
                        }
                        $image = $newImage;
                    } else {
                        $errors[] = "Lỗi khi lưu file ảnh.";
                    }
                }
            }

            if (empty($errors)) {
                $brand->name = $name;
                $brand->slug = $slug;
                $brand->image = $image;
                $brand->description = $description;
                $brand->status = $status;

                $this->brandDAO->update($brand);
                header("Location: index.php?area=admin&controller=brand&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/brands/edit.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $brand = $this->brandDAO->findById($id);
        if (!$brand) {
            header("Location: index.php?area=admin&controller=brand&action=index");
            exit;
        }

        $pageTitle = "Chi tiết thương hiệu - " . htmlspecialchars($brand->name);
        require __DIR__ . "/../../views/admin/brands/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->brandDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=brand&action=index");
        exit;
    }
}
