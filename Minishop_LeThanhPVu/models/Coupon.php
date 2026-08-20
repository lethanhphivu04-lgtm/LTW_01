<?php
namespace Models;

/**
 * Model đại diện cho Mã giảm giá (Coupon / Voucher)
 */
class Coupon
{
    public ?int $id = null;           // Mã định danh voucher (Khóa chính)
    public string $code;              // Mã code giảm giá (VD: MINI10, TECH50K, FREESHIP...)
    public string $discountType;      // Loại giảm giá: 'percent' (Phần trăm) hoặc 'fixed' (Số tiền cố định VNĐ)
    public float $discountValue;      // Giá trị giảm (% hoặc số tiền)
    public float $minOrderValue;      // Giá trị đơn hàng tối thiểu để áp dụng mã
    public ?float $maxDiscount;       // Mức giảm tối đa (nếu là loại phần trăm %)
    public ?string $expiryDate;       // Ngày hết hạn của mã (NULL = Vô thời hạn)
    public int $status;               // Trạng thái: 1 = Hoạt động, 0 = Tạm khóa
    public string $createdAt;         // Thời gian tạo

    public function __construct(
        string $code,
        string $discountType = 'percent',
        float $discountValue = 0,
        float $minOrderValue = 0,
        ?float $maxDiscount = null,
        ?string $expiryDate = null,
        int $status = 1
    ) {
        $this->code = strtoupper(trim($code));
        $this->discountType = $discountType;
        $this->discountValue = $discountValue;
        $this->minOrderValue = $minOrderValue;
        $this->maxDiscount = $maxDiscount;
        $this->expiryDate = $expiryDate;
        $this->status = $status;
    }
}
