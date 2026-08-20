<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Phiếu nhập kho') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #0f172a;
        }
        .receipt-container {
            max-width: 850px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .receipt-header {
            margin-bottom: 25px;
        }
        .receipt-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .table-receipt th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
            text-align: center;
        }
        .table-receipt td, .table-receipt th {
            padding: 10px 12px;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Thanh công cụ in (Tự động ẩn khi in ra giấy hoặc lưu PDF) -->
<div class="no-print bg-dark text-white py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center" style="max-width: 850px;">
        <div class="fw-semibold">
            <i class="bi bi-box-arrow-in-down me-2 text-success"></i>Xem & In Phiếu Nhập Kho #<?= htmlspecialchars($receipt['receipt_code']) ?>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm fw-bold px-3">
                <i class="bi bi-printer-fill me-1"></i> In phiếu nhập kho
            </button>
            <button onclick="window.close()" class="btn btn-outline-light btn-sm px-3">
                Đóng
            </button>
        </div>
    </div>
</div>

<div class="receipt-container">
    <!-- Header Thông tin doanh nghiệp & Mẫu biểu chuẩn -->
    <div class="receipt-header d-flex justify-content-between align-items-start">
        <div>
            <div class="fw-bold fs-5 text-primary text-uppercase">Cửa hàng công nghệ MINISHOP</div>
            <div class="small text-secondary">Địa chỉ: Số 12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, TP.HCM</div>
            <div class="small text-secondary">Bộ phận: Quản lý Kho vận & Tiếp vận</div>
        </div>
        <div class="text-end">
            <div class="fw-bold small text-uppercase">Mẫu số 01 - VT</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ban hành theo TT số 200/2014/TT-BTC)</div>
            <div class="small mt-1"><strong>Số phiếu:</strong> <span class="text-danger fw-bold"><?= htmlspecialchars($receipt['receipt_code']) ?></span></div>
        </div>
    </div>

    <!-- Tiêu đề Phiếu nhập kho -->
    <div class="text-center my-4">
        <div class="receipt-title text-uppercase">PHIẾU NHẬP KHO</div>
        <div class="text-muted small">
            <?php 
            $receiptTime = strtotime($receipt['created_at']);
            $ngay = date('d', $receiptTime);
            $thang = date('m', $receiptTime);
            $nam = date('Y', $receiptTime);
            ?>
            <em>Ngày <?= $ngay ?> tháng <?= $thang ?> năm <?= $nam ?></em>
        </div>
    </div>

    <!-- Thông tin chi tiết đợt nhập -->
    <div class="mb-4 small">
        <div class="row mb-2">
            <div class="col-md-7">
                <strong>Họ tên người giao hàng / Đối tác:</strong> <?= htmlspecialchars($receipt['supplier_name'] ?? 'Nhà cung cấp chính hãng') ?>
            </div>
            <div class="col-md-5 text-md-end">
                <strong>Người lập phiếu:</strong> <?= htmlspecialchars($receipt['created_by']) ?>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <strong>Theo chứng từ số:</strong> HĐ-<?= date('Ymd', $receiptTime) ?>-<?= $receipt['id'] ?> &nbsp;|&nbsp; <strong>Nhập tại kho:</strong> Kho trung tâm MiniShop (IUH TP.HCM)
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <strong>Lý do nhập kho:</strong> <?= htmlspecialchars($receipt['note'] ?? 'Nhập bổ sung tồn kho hàng hóa') ?>
            </div>
        </div>
    </div>

    <!-- Bảng chi tiết hàng hóa nhập kho -->
    <?php 
    $qty = (int)$receipt['quantity'];
    $price = (float)$receipt['import_price'];
    $totalAmount = $qty * $price;
    ?>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-receipt align-middle">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 45px;">STT</th>
                    <th rowspan="2">Tên, nhãn hiệu, quy cách phẩm chất vật tư, dụng cụ, hàng hóa</th>
                    <th rowspan="2" style="width: 80px;">Mã số</th>
                    <th rowspan="2" style="width: 60px;">ĐVT</th>
                    <th colspan="2">Số lượng</th>
                    <th rowspan="2" class="text-end" style="width: 130px;">Đơn giá vốn</th>
                    <th rowspan="2" class="text-end" style="width: 140px;">Thành tiền</th>
                </tr>
                <tr>
                    <th style="width: 70px;">Chứng từ</th>
                    <th style="width: 70px;">Thực nhập</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($receipt['product_name']) ?></div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            Danh mục: <?= htmlspecialchars($receipt['category_name'] ?? 'Công nghệ') ?> | Thương hiệu: <?= htmlspecialchars($receipt['brand_name'] ?? 'Chính hãng') ?>
                        </div>
                    </td>
                    <td class="text-center font-monospace">SP-<?= $receipt['product_id'] ?></td>
                    <td class="text-center">Cái</td>
                    <td class="text-center fw-semibold"><?= $qty ?></td>
                    <td class="text-center fw-bold text-success"><?= $qty ?></td>
                    <td class="text-end"><?= number_format($price, 0, ',', '.') ?> đ</td>
                    <td class="text-end fw-bold text-danger"><?= number_format($totalAmount, 0, ',', '.') ?> đ</td>
                </tr>
                <!-- Hàng tổng cộng -->
                <tr class="table-light fw-bold">
                    <td colspan="4" class="text-center text-uppercase">Cộng tổng tiền vốn nhập:</td>
                    <td class="text-center"><?= $qty ?></td>
                    <td class="text-center text-success"><?= $qty ?></td>
                    <td class="text-end">-</td>
                    <td class="text-end text-danger fs-6"><?= number_format($totalAmount, 0, ',', '.') ?> đ</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Tổng tiền và ghi chú bằng chữ -->
    <div class="mb-5 small">
        <p class="mb-1"><strong>Tổng số tiền (Viết bằng số):</strong> <span class="fs-6 fw-bold text-danger"><?= number_format($totalAmount, 0, ',', '.') ?> VNĐ</span></p>
        <p class="mb-0"><strong>Số chứng từ gốc kèm theo:</strong> 01 Hóa đơn GTGT & Biên bản bàn giao hàng hóa.</p>
    </div>

    <!-- 4 Chữ ký xác nhận theo quy chuẩn kế toán kho -->
    <div class="row text-center mt-4">
        <div class="col-3">
            <div class="fw-bold">Người lập phiếu</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký, ghi rõ họ tên)</div>
            <div style="height: 65px;"></div>
            <div class="fw-semibold small"><?= htmlspecialchars($receipt['created_by']) ?></div>
        </div>
        <div class="col-3">
            <div class="fw-bold">Người giao hàng</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký, ghi rõ họ tên)</div>
            <div style="height: 65px;"></div>
            <div class="fw-semibold small"><?= htmlspecialchars($receipt['supplier_name'] ?? 'Bên giao') ?></div>
        </div>
        <div class="col-3">
            <div class="fw-bold">Thủ kho</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký, ghi rõ họ tên)</div>
            <div style="height: 65px;"></div>
            <div class="fw-semibold small">Lê Thanh Phi Vũ</div>
        </div>
        <div class="col-3">
            <div class="fw-bold">Kế toán trưởng</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký, đóng dấu)</div>
            <div style="height: 65px;"></div>
            <div class="fw-semibold small">MiniShop Admin</div>
        </div>
    </div>
</div>

</body>
</html>
