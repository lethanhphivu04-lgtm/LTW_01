<?php
namespace Controllers\Admin;

use DAO\CouponDAO;
use Models\Coupon;

/**
 * Controller Quản lý Mã giảm giá (Coupon / Voucher Management)
 */
class CouponController
{
    private CouponDAO $couponDAO;

    public function __construct()
    {
        $this->couponDAO = new CouponDAO();
    }

    /**
     * Hiển thị danh sách tất cả các Mã giảm giá
     */
    public function index()
    {
        $coupons = $this->couponDAO->getAll();
        $pageTitle = "Quản lý Mã giảm giá";
        require __DIR__ . "/../../views/admin/coupons/index.php";
    }

    /**
     * Thêm mới Mã giảm giá
     */
    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $code = strtoupper(trim($_POST["code"] ?? ""));
            $discountType = $_POST["discount_type"] ?? "percent";
            $discountValue = (float)($_POST["discount_value"] ?? 0);
            $minOrderValue = (float)($_POST["min_order_value"] ?? 0);
            $maxDiscount = !empty($_POST["max_discount"]) ? (float)$_POST["max_discount"] : null;
            $expiryDate = !empty($_POST["expiry_date"]) ? $_POST["expiry_date"] : null;
            $status = (int)($_POST["status"] ?? 1);

            if (empty($code) || $discountValue <= 0) {
                $_SESSION["error"] = "Vui lòng nhập đầy đủ mã và giá trị giảm.";
                header("Location: index.php?area=admin&controller=coupon&action=create");
                exit;
            }

            $coupon = new Coupon($code, $discountType, $discountValue, $minOrderValue, $maxDiscount, $expiryDate, $status);
            if ($this->couponDAO->insert($coupon)) {
                $_SESSION["success"] = "Thêm mã giảm giá thành công.";
                header("Location: index.php?area=admin&controller=coupon&action=index");
                exit;
            } else {
                $_SESSION["error"] = "Không thể thêm mã giảm giá (có thể mã đã tồn tại).";
            }
        }

        $pageTitle = "Thêm mới Mã giảm giá";
        require __DIR__ . "/../../views/admin/coupons/create.php";
    }

    /**
     * Chỉnh sửa Mã giảm giá
     */
    public function edit()
    {
        $id = (int)($_GET["id"] ?? 0);
        $coupon = $this->couponDAO->findById($id);
        if (!$coupon) {
            header("Location: index.php?area=admin&controller=coupon&action=index");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $coupon->code = strtoupper(trim($_POST["code"] ?? ""));
            $coupon->discountType = $_POST["discount_type"] ?? "percent";
            $coupon->discountValue = (float)($_POST["discount_value"] ?? 0);
            $coupon->minOrderValue = (float)($_POST["min_order_value"] ?? 0);
            $coupon->maxDiscount = !empty($_POST["max_discount"]) ? (float)$_POST["max_discount"] : null;
            $coupon->expiryDate = !empty($_POST["expiry_date"]) ? $_POST["expiry_date"] : null;
            $coupon->status = (int)($_POST["status"] ?? 1);

            if (empty($coupon->code) || $coupon->discountValue <= 0) {
                $_SESSION["error"] = "Vui lòng nhập đầy đủ mã và giá trị giảm.";
            } else {
                if ($this->couponDAO->update($coupon)) {
                    $_SESSION["success"] = "Cập nhật mã giảm giá thành công.";
                    header("Location: index.php?area=admin&controller=coupon&action=index");
                    exit;
                } else {
                    $_SESSION["error"] = "Không thể cập nhật mã giảm giá.";
                }
            }
        }

        $pageTitle = "Chỉnh sửa Mã giảm giá";
        require __DIR__ . "/../../views/admin/coupons/edit.php";
    }

    /**
     * Xóa Mã giảm giá
     */
    public function delete()
    {
        $id = (int)($_GET["id"] ?? 0);
        if ($this->couponDAO->delete($id)) {
            $_SESSION["success"] = "Đã xóa mã giảm giá thành công.";
        } else {
            $_SESSION["error"] = "Không thể xóa mã giảm giá.";
        }
        header("Location: index.php?area=admin&controller=coupon&action=index");
        exit;
    }
}
