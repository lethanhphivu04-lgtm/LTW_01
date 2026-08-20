<?php
namespace Models;

/**
 * Model đại diện cho Phiếu nhập kho (Stock Receipt)
 */
class StockReceipt
{
    public ?int $id = null;           // Mã định danh phiếu nhập (Khóa chính)
    public string $receiptCode;       // Mã phiếu nhập (VD: PNK-20260820-143015)
    public int $productId;            // ID sản phẩm được nhập thêm tồn kho (Khóa ngoại)
    public int $quantity;             // Số lượng nhập thêm
    public float $importPrice;        // Đơn giá vốn nhập (VNĐ)
    public ?string $supplierName;     // Tên nhà cung cấp / đối tác phân phối
    public ?string $note;             // Ghi chú lý do / tình trạng đợt nhập
    public string $createdBy;         // Tên nhân viên/admin lập phiếu
    public string $createdAt;         // Thời gian lập phiếu nhập

    public function __construct(
        string $receiptCode,
        int $productId,
        int $quantity,
        float $importPrice,
        ?string $supplierName = null,
        ?string $note = null,
        string $createdBy = 'admin'
    ) {
        $this->receiptCode = $receiptCode;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->importPrice = $importPrice;
        $this->supplierName = $supplierName;
        $this->note = $note;
        $this->createdBy = $createdBy;
    }
}
