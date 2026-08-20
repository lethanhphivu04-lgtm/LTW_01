<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-heart-fill text-danger me-2"></i>Sản phẩm yêu thích</h3>
            <p class="text-secondary small mb-0">Danh sách các sản phẩm bạn đã lưu để xem lại hoặc mua sau</p>
        </div>
        <a href="<?= $baseUrl ?>/products" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <div class="card p-5 border-0 shadow-sm rounded-3 mx-auto" style="max-width: 500px;">
                <i class="bi bi-heart display-3 text-muted mb-3"></i>
                <h5 class="fw-bold text-secondary">Chưa có sản phẩm yêu thích nào</h5>
                <p class="small text-muted">Hãy bấm vào biểu tượng trái tim ❤️ ở bất kỳ sản phẩm nào để lưu lại tại đây.</p>
                <div>
                    <a href="<?= $baseUrl ?>/products" class="btn btn-dark btn-sm px-4 py-2 mt-2">Khám phá sản phẩm ngay</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($products as $p): ?>
                <?php
                // Chuẩn hóa dạng mảng cho layout
                $pArr = is_object($p) ? [
                    'id' => $p->id,
                    'proname' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'discount_price' => $p->discountPrice,
                    'image' => $p->image,
                    'quantity' => $p->quantity
                ] : $p;
                ?>
                <div class="col" id="wishlist-col-<?= $pArr['id'] ?>">
                    <div class="card h-100 product-card shadow-sm border-0 position-relative rounded-3 overflow-hidden">
                        <!-- Nút Xóa khỏi Wishlist -->
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle btn-wishlist-toggle" 
                                data-product-id="<?= $pArr['id'] ?>" title="Bỏ yêu thích" style="width:32px; height:32px; padding:0; z-index:10;">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        <a href="<?= $baseUrl ?>/product/<?= htmlspecialchars($pArr['slug']) ?>" class="text-center p-3 d-block bg-light">
                            <?php $imgUrl = !empty($pArr['image']) ? $baseUrl . '/uploads/products/' . $pArr['image'] : $baseUrl . '/assets/client/images/no-image.png'; ?>
                            <img src="<?= $imgUrl ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($pArr['proname']) ?>" style="height: 180px; object-fit: contain;">
                        </a>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold">
                                <a href="<?= $baseUrl ?>/product/<?= htmlspecialchars($pArr['slug']) ?>" class="text-dark text-decoration-none text-truncate d-block">
                                    <?= htmlspecialchars($pArr['proname']) ?>
                                </a>
                            </h6>

                            <div class="mt-auto">
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <?php if ($pArr['discount_price'] > 0 && $pArr['discount_price'] < $pArr['price']): ?>
                                        <span class="fs-6 fw-bold text-danger"><?= number_format($pArr['discount_price'], 0, ',', '.') ?> đ</span>
                                        <span class="small text-muted text-decoration-line-through"><?= number_format($pArr['price'], 0, ',', '.') ?> đ</span>
                                    <?php else: ?>
                                        <span class="fs-6 fw-bold text-dark"><?= number_format($pArr['price'], 0, ',', '.') ?> đ</span>
                                    <?php endif; ?>
                                </div>

                                <button class="btn btn-dark btn-sm w-100 btn-add-cart" data-productid="<?= $pArr['id'] ?>" <?= $pArr['quantity'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="bi bi-cart-plus me-1"></i> <?= $pArr['quantity'] > 0 ? 'Thêm vào giỏ' : 'Hết hàng' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
