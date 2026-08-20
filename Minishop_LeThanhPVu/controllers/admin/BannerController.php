<?php
namespace Controllers\Admin;

use DAO\BannerDAO;
use Models\Banner;

class BannerController
{
    private BannerDAO $bannerDAO;

    public function __construct()
    {
        $this->bannerDAO = new BannerDAO();
    }

    public function index()
    {
        $banners = $this->bannerDAO->getAll();
        $pageTitle = "Quản lý Banner Slider";
        require __DIR__ . "/../../views/admin/banners/index.php";
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = trim($_POST["title"] ?? "");
            $subtitle = trim($_POST["subtitle"] ?? "");
            $link = trim($_POST["link"] ?? "/products");
            $badgeText = trim($_POST["badge_text"] ?? "Khám phá ngay");
            $sortOrder = (int)($_POST["sort_order"] ?? 0);
            $status = (int)($_POST["status"] ?? 1);

            if (empty($title)) {
                $_SESSION["error"] = "Vui lòng nhập tiêu đề banner.";
                header("Location: index.php?area=admin&controller=banner&action=create");
                exit;
            }

            // Xử lý upload ảnh
            $imageName = null;
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                if (in_array($ext, $allowed)) {
                    $imageName = "banner_" . time() . "_" . uniqid() . "." . $ext;
                    $targetDir = __DIR__ . "/../../uploads/banners/";
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $imageName);
                }
            }

            $banner = new Banner($title, $subtitle, $imageName, $link, $badgeText, $sortOrder, $status);
            if ($this->bannerDAO->insert($banner)) {
                $_SESSION["success"] = "Thêm banner thành công.";
                header("Location: index.php?area=admin&controller=banner&action=index");
                exit;
            } else {
                $_SESSION["error"] = "Không thể thêm banner.";
            }
        }

        $pageTitle = "Thêm mới Banner";
        require __DIR__ . "/../../views/admin/banners/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET["id"] ?? 0);
        $banner = $this->bannerDAO->findById($id);
        if (!$banner) {
            header("Location: index.php?area=admin&controller=banner&action=index");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = trim($_POST["title"] ?? "");
            $subtitle = trim($_POST["subtitle"] ?? "");
            $link = trim($_POST["link"] ?? "/products");
            $badgeText = trim($_POST["badge_text"] ?? "Khám phá ngay");
            $sortOrder = (int)($_POST["sort_order"] ?? 0);
            $status = (int)($_POST["status"] ?? 1);

            if (empty($title)) {
                $_SESSION["error"] = "Vui lòng nhập tiêu đề banner.";
                header("Location: index.php?area=admin&controller=banner&action=edit&id=" . $id);
                exit;
            }

            // Xử lý upload ảnh mới
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                if (in_array($ext, $allowed)) {
                    $newImageName = "banner_" . time() . "_" . uniqid() . "." . $ext;
                    $targetDir = __DIR__ . "/../../uploads/banners/";
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $newImageName)) {
                        // Xóa ảnh cũ nếu có
                        if (!empty($banner->image) && file_exists($targetDir . $banner->image)) {
                            unlink($targetDir . $banner->image);
                        }
                        $banner->image = $newImageName;
                    }
                }
            }

            $banner->title = $title;
            $banner->subtitle = $subtitle;
            $banner->link = $link;
            $banner->badgeText = $badgeText;
            $banner->sortOrder = $sortOrder;
            $banner->status = $status;

            if ($this->bannerDAO->update($banner)) {
                $_SESSION["success"] = "Cập nhật banner thành công.";
                header("Location: index.php?area=admin&controller=banner&action=index");
                exit;
            } else {
                $_SESSION["error"] = "Không thể cập nhật banner.";
            }
        }

        $pageTitle = "Chỉnh sửa Banner";
        require __DIR__ . "/../../views/admin/banners/edit.php";
    }

    public function delete()
    {
        $id = (int)($_GET["id"] ?? 0);
        if ($id > 0) {
            $banner = $this->bannerDAO->findById($id);
            if ($banner && !empty($banner->image)) {
                $filePath = __DIR__ . "/../../uploads/banners/" . $banner->image;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $this->bannerDAO->delete($id);
            $_SESSION["success"] = "Đã xóa banner.";
        }
        header("Location: index.php?area=admin&controller=banner&action=index");
        exit;
    }
}
