<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chính sách & Hỗ trợ</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Menu Tabs -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="bi bi-shield-check me-2"></i>CHÍNH SÁCH & HỖ TRỢ
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=warranty" 
                       class="list-group-item list-group-item-action py-3 <?= $type === 'warranty' ? 'active bg-primary text-white border-primary' : 'text-dark' ?>">
                        <i class="bi bi-patch-check me-2"></i>Chính sách bảo hành
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=return" 
                       class="list-group-item list-group-item-action py-3 <?= $type === 'return' ? 'active bg-primary text-white border-primary' : 'text-dark' ?>">
                        <i class="bi bi-arrow-repeat me-2"></i>Chính sách đổi trả 30 ngày
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=shipping" 
                       class="list-group-item list-group-item-action py-3 <?= $type === 'shipping' ? 'active bg-primary text-white border-primary' : 'text-dark' ?>">
                        <i class="bi bi-truck me-2"></i>Chính sách giao hàng
                    </a>
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=home&action=policy&type=payment" 
                       class="list-group-item list-group-item-action py-3 <?= $type === 'payment' ? 'active bg-primary text-white border-primary' : 'text-dark' ?>">
                        <i class="bi bi-credit-card me-2"></i>Hướng dẫn mua & Thanh toán
                    </a>
                </div>
            </div>

            <!-- Box hỗ trợ nhanh -->
            <div class="card border-0 shadow-sm rounded-3 mt-4 p-4 text-center" style="background:#f8fafc;">
                <i class="bi bi-headset display-4 text-primary mb-2"></i>
                <h6 class="fw-bold mb-1">Cần hỗ trợ trực tiếp?</h6>
                <p class="small text-muted mb-3">Đội ngũ kỹ thuật viên luôn sẵn sàng giải đáp thắc mắc 24/7</p>
                <a href="tel:0123456789" class="btn btn-dark btn-sm fw-semibold w-100 py-2">
                    <i class="bi bi-telephone-fill me-1"></i> Hotline: 0123-456-789
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-3 p-4 p-md-5">
                
                <!-- TAB 1: BẢO HÀNH -->
                <?php if ($type === 'warranty'): ?>
                    <h3 class="fw-bold text-dark mb-3"><i class="bi bi-patch-check text-primary me-2"></i>Chính Sách Bảo Hành Chính Hãng</h3>
                    <p class="text-muted">MiniShop cam kết toàn bộ linh kiện và thiết bị công nghệ đều là hàng chính hãng 100%, được bảo hành theo đúng tiêu chuẩn nghiêm ngặt của nhà sản xuất.</p>
                    <hr class="my-4">

                    <h5 class="fw-bold text-dark mb-3">1. Thời hạn bảo hành tiêu chuẩn</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhóm linh kiện / Sản phẩm</th>
                                    <th>Thời gian bảo hành</th>
                                    <th>Hình thức bảo hành</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Chuột, Bàn phím, Tai nghe Gaming</strong></td>
                                    <td><span class="badge bg-success">12 - 24 tháng</span></td>
                                    <td>Đổi mới trong 30 ngày đầu, bảo hành theo hãng</td>
                                </tr>
                                <tr>
                                    <td><strong>Màn hình hiển thị, Laptop</strong></td>
                                    <td><span class="badge bg-success">24 - 36 tháng</span></td>
                                    <td>Bảo hành chính hãng tận nơi hoặc tại TTBH</td>
                                </tr>
                                <tr>
                                    <td><strong>Ổ cứng SSD / HDD, Ram</strong></td>
                                    <td><span class="badge bg-success">36 - 60 tháng</span></td>
                                    <td>1 đổi 1 trong suốt thời gian bảo hành</td>
                                </tr>
                                <tr>
                                    <td><strong>Phụ kiện, Cáp chuyển đổi, Hub</strong></td>
                                    <td><span class="badge bg-info text-dark">06 - 12 tháng</span></td>
                                    <td>1 đổi 1 ngay lập tức</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold text-dark mb-3">2. Điều kiện được bảo hành</h5>
                    <ul class="text-secondary" style="line-height:1.8;">
                        <li>Sản phẩm còn trong thời hạn bảo hành tính từ ngày mua hàng.</li>
                        <li>Tem bảo hành và mã vạch Serial Number (S/N) còn nguyên vẹn, không có dấu hiệu bị rách, chắp vá hay tẩy xóa.</li>
                        <li>Sản phẩm gặp sự cố kỹ thuật do lỗi linh kiện từ nhà sản xuất.</li>
                    </ul>

                    <h5 class="fw-bold text-dark mb-3">3. Trường hợp từ chối bảo hành</h5>
                    <ul class="text-secondary" style="line-height:1.8;">
                        <li>Sản phẩm bị hư hỏng do va đập, rơi rớt, móp méo, trầy xước nặng hoặc vào nước, chập cháy do nguồn điện không ổn định.</li>
                        <li>Sản phẩm đã bị can thiệp, tự ý tháo mở hoặc sửa chữa bởi cá nhân/đơn vị không được ủy quyền.</li>
                    </ul>

                <!-- TAB 2: ĐỔI TRẢ 30 NGÀY -->
                <?php elseif ($type === 'return'): ?>
                    <h3 class="fw-bold text-dark mb-3"><i class="bi bi-arrow-repeat text-primary me-2"></i>Chính Sách Đổi Trả Trong 30 Ngày</h3>
                    <p class="text-muted">Nhằm đảm bảo quyền lợi tối đa cho khách hàng, MiniShop áp dụng chính sách đổi trả linh hoạt và nhanh chóng trong vòng 30 ngày.</p>
                    <hr class="my-4">

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <i class="bi bi-box-seam fs-1 text-primary mb-2"></i>
                                <h6 class="fw-bold">1 Đổi 1 Miễn Phí</h6>
                                <p class="small text-muted mb-0">Áp dụng cho sản phẩm bị lỗi phần cứng trong vòng 30 ngày đầu tiên.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <i class="bi bi-clock-history fs-1 text-success mb-2"></i>
                                <h6 class="fw-bold">Xử Lý Nhanh Chóng</h6>
                                <p class="small text-muted mb-0">Kiểm tra và đổi sản phẩm mới ngay tại quầy trong 15 - 30 phút.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <i class="bi bi-cash-coin fs-1 text-warning mb-2"></i>
                                <h6 class="fw-bold">Hoàn Tiền 100%</h6>
                                <p class="small text-muted mb-0">Hoàn tiền nếu không còn sản phẩm cùng loại để đổi thay thế.</p>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3">Quy trình đổi trả sản phẩm:</h5>
                    <ol class="text-secondary" style="line-height:2;">
                        <li><strong>Bước 1:</strong> Liên hệ Hotline <code>0123-456-789</code> hoặc mang sản phẩm trực tiếp đến cửa hàng MiniShop.</li>
                        <li><strong>Bước 2:</strong> Kỹ thuật viên kiểm tra tình trạng lỗi của sản phẩm.</li>
                        <li><strong>Bước 3:</strong> Xác nhận lỗi và tiến hành đổi mới ngay sản phẩm tương đương.</li>
                    </ol>

                <!-- TAB 3: GIAO HÀNG -->
                <?php elseif ($type === 'shipping'): ?>
                    <h3 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-primary me-2"></i>Chính Sách Giao Hàng Toàn Quốc</h3>
                    <p class="text-muted">MiniShop hợp tác cùng các đơn vị vận chuyển hàng đầu (Viettel Post, GHTK, GrabExpress) để giao hàng nhanh chóng, an toàn đến tận tay quý khách.</p>
                    <hr class="my-4">

                    <h5 class="fw-bold text-dark mb-3">1. Thời gian giao hàng</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card p-3 border h-100 bg-light">
                                <h6 class="fw-bold text-primary"><i class="bi bi-lightning-charge me-1"></i>Nội thành TP. Hồ Chí Minh</h6>
                                <p class="small text-secondary mb-1">• Giao hỏa tốc trong <strong>2 - 4 giờ</strong> (áp dụng cho đơn cần gấp).</p>
                                <p class="small text-secondary mb-0">• Giao tiêu chuẩn: Nhận hàng trong <strong>ngày</strong> hoặc ngày hôm sau.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 border h-100 bg-light">
                                <h6 class="fw-bold text-primary"><i class="bi bi-geo-alt me-1"></i>Các tỉnh thành khác toàn quốc</h6>
                                <p class="small text-secondary mb-1">• Các thành phố lớn: <strong>1 - 2 ngày</strong> làm việc.</p>
                                <p class="small text-secondary mb-0">• Tuyến huyện/xã: <strong>2 - 4 ngày</strong> làm việc.</p>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3">2. Phí vận chuyển & Miễn phí ship</h5>
                    <ul class="text-secondary" style="line-height:1.8;">
                        <li><strong class="text-success">Miễn phí giao hàng toàn quốc</strong> cho tất cả đơn hàng có tổng giá trị từ <strong>1.000.000 đ</strong> trở lên.</li>
                        <li>Đơn hàng dưới 1.000.000 đ: Phí giao hàng đồng giá chỉ <strong>30.000 đ</strong> toàn quốc.</li>
                        <li>Khách hàng được quyền <strong>kiểm tra hàng (đồng kiểm)</strong> trước khi nhận và thanh toán.</li>
                    </ul>

                <!-- TAB 4: HƯỚNG DẪN MUA & THANH TOÁN -->
                <?php elseif ($type === 'payment'): ?>
                    <h3 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card text-primary me-2"></i>Hướng Dẫn Mua Hàng & Thanh Toán</h3>
                    <p class="text-muted">Hướng dẫn các bước đặt mua linh kiện và các phương thức thanh toán an toàn tại MiniShop.</p>
                    <hr class="my-4">

                    <h5 class="fw-bold text-dark mb-3">1. Quy trình mua hàng 4 bước đơn giản:</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <span class="badge bg-dark rounded-circle p-2 mb-2 fs-6">1</span>
                                <h6 class="fw-bold">Chọn sản phẩm</h6>
                                <p class="small text-muted mb-0">Tìm kiếm linh kiện và bấm "Thêm vào giỏ".</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <span class="badge bg-dark rounded-circle p-2 mb-2 fs-6">2</span>
                                <h6 class="fw-bold">Kiểm tra giỏ hàng</h6>
                                <p class="small text-muted mb-0">Cập nhật số lượng và xem lại tổng số tiền.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <span class="badge bg-dark rounded-circle p-2 mb-2 fs-6">3</span>
                                <h6 class="fw-bold">Điền thông tin</h6>
                                <p class="small text-muted mb-0">Nhập Họ tên, SĐT, Email và Địa chỉ giao hàng.</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded-3 bg-light text-center h-100">
                                <span class="badge bg-dark rounded-circle p-2 mb-2 fs-6">4</span>
                                <h6 class="fw-bold">Chọn thanh toán</h6>
                                <p class="small text-muted mb-0">Chọn COD hoặc VNPay rồi bấm "Đặt hàng".</p>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-3">2. Các phương thức thanh toán được hỗ trợ</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card p-3 border border-success h-100">
                                <h6 class="fw-bold text-success"><i class="bi bi-cash-stack me-1"></i>1. Thanh toán khi nhận hàng (COD)</h6>
                                <p class="small text-secondary mb-0">Khách hàng kiểm tra sản phẩm khi shipper giao tới và thanh toán bằng tiền mặt trực tiếp. An toàn và tiện lợi.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 border border-primary h-100">
                                <h6 class="fw-bold text-primary"><i class="bi bi-qr-code-scan me-1"></i>2. Cổng thanh toán Online VNPay</h6>
                                <p class="small text-secondary mb-0">Thanh toán tức thì bằng cách quét mã VNPay-QR trên App ngân hàng hoặc dùng thẻ ATM/Visa/MasterCard nội địa.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
