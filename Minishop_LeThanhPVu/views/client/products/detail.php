<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>" class="text-secondary text-decoration-none">Trang chủ</a></li>
            <?php if (!empty($product['category_name'])): ?>
            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/category/<?= urlencode($product['category_slug']) ?>" class="text-secondary text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active text-dark fw-semibold"><?= htmlspecialchars($product['proname']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Ảnh sản phẩm & Gallery -->
        <div class="col-lg-5">
            <div class="card border rounded-3 p-4 bg-white text-center position-relative mb-3" style="border-color: #e2e8f0 !important; min-height: 360px; display: flex; align-items: center; justify-content: center;">
                <img id="mainProductImg" 
                     src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" 
                     class="img-fluid mx-auto" 
                     alt="<?= htmlspecialchars($product['proname']) ?>" 
                     style="max-height:350px; object-fit:contain; transition: opacity 0.2s ease-in-out;"
                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="product-placeholder-icon py-5" style="display:none; align-items:center; justify-content:center; font-size: 6rem; color:#cbd5e1;">
                    <i class="bi bi-cpu"></i>
                </div>
            </div>

            <!-- Thumbnail Gallery -->
            <?php 
            $galleryImages = $galleryImages ?? [];
            $allImages = [];
            if (!empty($product['image'])) {
                $allImages[] = ['image' => $product['image'], 'is_main' => true];
            }
            foreach ($galleryImages as $g) {
                if (!empty($g['image'])) {
                    $allImages[] = ['image' => $g['image'], 'is_main' => false];
                }
            }
            ?>
            <?php if (count($allImages) > 1): ?>
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-2">
                <?php foreach ($allImages as $idx => $imgItem): ?>
                    <?php $imgUrl = $baseUrl . "/uploads/products/" . htmlspecialchars($imgItem['image']); ?>
                    <div class="gallery-thumb-wrapper <?= $idx === 0 ? 'active' : '' ?>" 
                         data-img-src="<?= $imgUrl ?>" 
                         style="cursor: pointer; width: 68px; height: 68px; border-radius: 8px; overflow: hidden; border: 2px solid <?= $idx === 0 ? '#0d6efd' : '#e2e8f0' ?>; padding: 2px; background: #fff; transition: all 0.2s ease;">
                        <img src="<?= $imgUrl ?>" 
                             alt="Thumbnail <?= $idx + 1 ?>" 
                             class="w-100 h-100" 
                             style="object-fit: cover; border-radius: 4px;"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/68?text=Thumb';">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-lg-7">
            <h1 class="fw-bold h3 mb-3 text-dark"><?= htmlspecialchars($product['proname']) ?></h1>

            <div class="d-flex align-items-center gap-2 mb-3">
                <?php if (!empty($product['category_name'])): ?>
                <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($product['category_name']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['brand_name'])): ?>
                <span class="badge bg-dark text-white px-2 py-1"><?= htmlspecialchars($product['brand_name']) ?></span>
                <?php endif; ?>
            </div>

            <?php
            $hasDiscount = isset($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
            ?>
            <?php if ($hasDiscount): ?>
            <div class="d-flex align-items-center justify-content-between p-2 px-3 mb-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(90deg, #e11d48, #f43f5e);">
                <div class="fw-bold small">
                    <i class="bi bi-fire me-1"></i>FLASH SALE ĐANG DIỄN RA
                </div>
                <div class="small fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-clock-history me-1"></i>Kết thúc trong: 
                    <span class="badge bg-dark text-white font-monospace" id="detail-countdown">02 ngày 14:35:42</span>
                </div>
            </div>
            <?php endif; ?>

            <div class="p-3 mb-4 rounded-3" style="background:#f1f5f9; border: 1px solid #e2e8f0;">
                <?php if ($hasDiscount): ?>
                    <span class="fw-bold fs-3 text-danger"><?= number_format($product['discount_price'], 0, ',', '.') ?> đ</span>
                    <span class="text-muted text-decoration-line-through fs-5 ms-2"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                    <?php $percent = round((1 - $product['discount_price'] / $product['price']) * 100); ?>
                    <span class="badge bg-danger ms-2">-<?= $percent ?>%</span>
                <?php else: ?>
                    <span class="fw-bold fs-3 text-dark"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-dark">Mô tả sản phẩm:</h6>
                <p class="text-secondary" style="line-height:1.7;"><?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả chi tiết.')) ?></p>
            </div>

            <div class="d-flex align-items-center gap-3 mb-4">
                <label class="fw-semibold text-dark">Số lượng:</label>
                <div class="input-group" style="width:130px;">
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="decrease" <?= (($product['quantity'] ?? 0) <= 0) ? 'disabled' : '' ?>>−</button>
                    <input type="number" class="form-control text-center bg-white" id="qty-input" value="<?= (($product['quantity'] ?? 0) > 0) ? 1 : 0 ?>" min="1" max="<?= (int)($product['quantity'] ?? 0) ?>" readonly>
                    <button class="btn btn-outline-secondary btn-qty" type="button" data-action="increase" <?= (($product['quantity'] ?? 0) <= 0) ? 'disabled' : '' ?>>+</button>
                </div>
                <?php if (($product['quantity'] ?? 0) > 0): ?>
                    <span class="text-muted small ms-2">(Còn <strong class="text-dark"><?= (int)$product['quantity'] ?></strong> sản phẩm trong kho)</span>
                <?php else: ?>
                    <span class="badge bg-danger ms-2 px-2 py-1">Hết hàng</span>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-3 align-items-center">
                <?php
                $isWishlisted = in_array((int)$product['id'], $_SESSION['wishlist'] ?? []);
                ?>
                <button type="button" class="btn btn-outline-danger btn-lg btn-wishlist-toggle px-3" data-product-id="<?= $product['id'] ?>" title="<?= $isWishlisted ? 'Bỏ thích' : 'Yêu thích' ?>">
                    <i class="bi <?= $isWishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' ?> fs-5"></i>
                </button>
                <?php if (($product['quantity'] ?? 0) > 0): ?>
                    <button type="button" class="btn btn-outline-dark btn-lg flex-fill btn-add-cart fw-semibold" data-productid="<?= $product['id'] ?>">
                        <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ
                    </button>
                    <button type="button" class="btn btn-dark btn-lg flex-fill btn-add-cart btn-buy-now fw-semibold" data-productid="<?= $product['id'] ?>" onclick="setTimeout(() => window.location.href='<?= $baseUrl ?>/cart', 300)">
                        <i class="bi bi-bag-check me-2"></i>Mua ngay
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-lg flex-fill fw-semibold" disabled>
                        <i class="bi bi-x-circle me-2"></i>Tạm hết hàng
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KHỐI ĐÁNH GIÁ & BÌNH LUẬN (REVIEWS) -->
    <section class="mt-5 pt-5 border-top" id="reviews-section">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-star-fill text-warning me-2"></i>Đánh giá & Nhận xét</h3>
                <p class="text-muted small mb-0">Nhận xét thực tế từ những khách hàng đã mua sản phẩm này</p>
            </div>
            <?php
            $reviews = $reviews ?? [];
            $ratingSummary = $ratingSummary ?? ['total' => count($reviews), 'avg' => 5.0];
            ?>
            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded-3 border">
                <div class="display-6 fw-bold text-dark mb-0"><?= $ratingSummary['avg'] ?></div>
                <div>
                    <div class="text-warning small mb-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star-fill <?= $i <= round($ratingSummary['avg']) ? '' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="small text-secondary"><?= $ratingSummary['total'] ?> lượt đánh giá</span>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['review_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['review_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['review_success']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Danh sách bình luận -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Nhận xét từ khách hàng (<?= count($reviews) ?>)</h6>
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square-dots display-6 d-block mb-2 text-secondary"></i>
                            <p class="small mb-0">Chưa có đánh giá nào. Hãy là người đầu tiên nhận xét về sản phẩm này!</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="p-3 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark"><i class="bi bi-person-circle text-secondary me-1"></i><?= htmlspecialchars($rev['fullname']) ?></span>
                                        <span class="small text-muted"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    <div class="text-warning small mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star-fill <?= $i <= (int)$rev['rating'] ? '' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-dark fw-semibold ms-1"><?= (int)$rev['rating'] ?>/5</span>
                                    </div>
                                    <p class="small text-secondary mb-0" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form gửi đánh giá -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-pencil-square me-1"></i>Gửi đánh giá của bạn</h6>
                    <form method="POST" action="<?= $baseUrl ?>/index.php?area=client&controller=product&action=review">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="slug" value="<?= $product['slug'] ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Họ và tên của bạn <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control form-control-sm" required placeholder="VD: Nguyễn Văn A">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Bạn chấm sản phẩm này mấy sao? <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select form-select-sm" required>
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5 sao - Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ (4 sao - Rất tốt)</option>
                                <option value="3">⭐⭐⭐ (3 sao - Bình thường)</option>
                                <option value="2">⭐⭐ (2 sao - Chưa hài lòng)</option>
                                <option value="1">⭐ (1 sao - Rất tệ)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nội dung nhận xét <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control form-control-sm" rows="3" required placeholder="Chia sẻ cảm nhận về chất lượng sản phẩm, tốc độ giao hàng..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold py-2">
                            <i class="bi bi-send-fill me-1"></i> Gửi đánh giá ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="mt-5 pt-5 border-top">
        <div class="text-center mb-4">
            <h3 class="section-title">Sản phẩm tương tự</h3>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($relatedProducts as $p): ?>
                <?php if ($p['slug'] !== $product['slug']): ?>
                    <?php include __DIR__ . "/../layouts/product-card.php"; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbs = document.querySelectorAll('.gallery-thumb-wrapper');
    const mainImg = document.getElementById('mainProductImg');

    thumbs.forEach(thumb => {
        const switchImage = () => {
            const newSrc = thumb.getAttribute('data-img-src');
            if (mainImg && newSrc && mainImg.src !== newSrc) {
                mainImg.style.opacity = '0.3';
                setTimeout(() => {
                    mainImg.src = newSrc;
                    mainImg.style.opacity = '1';
                }, 150);
            }
            thumbs.forEach(t => {
                t.style.borderColor = '#e2e8f0';
                t.classList.remove('active');
            });
            thumb.style.borderColor = '#0d6efd';
            thumb.classList.add('active');
        };

        thumb.addEventListener('click', switchImage);
        thumb.addEventListener('mouseenter', switchImage);
    });
});
</script>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
