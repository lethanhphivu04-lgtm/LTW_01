<?php
$pageTitle = $pageTitle ?? "Cập nhật sản phẩm";
$errors = $errors ?? [];
$galleryImages = $galleryImages ?? [];
$id = $product->id ?? 0;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">CẬP NHẬT SẢN PHẨM</h4>
    <a href="index.php?area=admin&controller=product&action=detail&id=<?= $id ?>" class="btn btn-outline-info btn-sm">
        <i class="bi bi-eye"></i> Xem chi tiết
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> Cập nhật sản phẩm và hình ảnh thành công!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=product&action=edit&id=<?= $id ?>" enctype="multipart/form-data" class="card card-body shadow-sm">
    <div class="row g-3">
        <!-- Thông tin cơ bản -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($_POST['proname'] ?? $product->name) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? $product->slug) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= ((int)($_POST['category_id'] ?? $product->categoryId) === $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= ((int)($_POST['brand_id'] ?? $product->brandId) === $b->id) ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? $product->price) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= htmlspecialchars($_POST['discount_price'] ?? $product->discountPrice) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($_POST['quantity'] ?? $product->quantity) ?>" required>
        </div>

        <!-- Ảnh chính -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Thay đổi hình ảnh chính</label>
            <input type="file" id="imageInput" name="image" class="form-control" accept="image/*">
            <div class="form-text">Chọn ảnh mới để thay thế ảnh đại diện chính của sản phẩm.</div>
            <div class="mt-2 text-center p-2 border rounded bg-light" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                <?php if (!empty($product->image)): ?>
                    <img src="uploads/products/<?= htmlspecialchars($product->image) ?>" 
                         class="img-thumbnail" 
                         id="mainPreview" 
                         style="max-height: 140px; object-fit: contain;"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Image';">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150?text=No+Image" class="img-thumbnail" id="mainPreview" style="max-height: 140px;">
                <?php endif; ?>
            </div>
        </div>

        <!-- Trạng thái & Mô tả ngắn -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Trạng thái</label>
            <select name="status" class="form-select mb-3">
                <?php $currentStatus = (int)($_POST['status'] ?? $product->status); ?>
                <option value="1" <?= $currentStatus === 1 ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= $currentStatus === 0 ? 'selected' : '' ?>>Ngừng bán</option>
            </select>

            <label class="form-label fw-semibold">Thêm ảnh phụ mới (Album / Gallery)</label>
            <input type="file" id="galleryInput" name="gallery[]" class="form-control" accept="image/*" multiple>
            <div class="form-text">Giữ phím <kbd>Ctrl</kbd> hoặc <kbd>Shift</kbd> để chọn nhiều ảnh phụ cùng lúc.</div>
        </div>

        <!-- Xem trước ảnh phụ mới chọn -->
        <div class="col-12" id="newGalleryPreviewWrapper" style="display: none;">
            <label class="form-label fw-semibold text-primary"><i class="bi bi-images me-1"></i> Ảnh phụ mới chuẩn bị tải lên:</label>
            <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" id="newGalleryPreview"></div>
        </div>

        <!-- Danh sách ảnh phụ hiện có -->
        <div class="col-12">
            <label class="form-label fw-semibold"><i class="bi bi-collection me-1"></i> Danh sách ảnh phụ hiện có (<?= count($galleryImages) ?> ảnh)</label>
            <div class="p-3 border rounded bg-light">
                <?php if (!empty($galleryImages)): ?>
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                        <?php foreach ($galleryImages as $gImg): ?>
                            <div class="col text-center position-relative">
                                <div class="card h-100 shadow-sm border p-1 bg-white">
                                    <img src="uploads/products/<?= htmlspecialchars($gImg['image']) ?>" 
                                         class="card-img-top rounded" 
                                         style="height: 110px; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/120?text=No+Image';">
                                    <div class="card-body p-1 pt-2">
                                        <a href="index.php?area=admin&controller=product&action=deleteGalleryImage&image_id=<?= $gImg['id'] ?>&product_id=<?= $id ?>" 
                                           class="btn btn-outline-danger btn-sm w-100 py-1"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa ảnh phụ này?');"
                                           title="Xóa ảnh này">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> Chưa có ảnh phụ nào cho sản phẩm này. Hãy chọn file ở mục <strong>"Thêm ảnh phụ mới"</strong> ở trên để thêm.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? $product->description ?? '') ?></textarea>
        </div>

        <div class="col-12 mt-3">
            <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle me-1"></i> Cập nhật sản phẩm</button>
            <a href="index.php?area=admin&controller=product&action=index" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</form>

<script>
// Preview ảnh chính khi chọn file
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            const preview = document.getElementById('mainPreview');
            if (preview) preview.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Preview các ảnh phụ mới khi chọn nhiều file
document.getElementById('galleryInput')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('newGalleryPreview');
    const wrapper = document.getElementById('newGalleryPreviewWrapper');
    
    if (files.length > 0) {
        previewContainer.innerHTML = '';
        wrapper.style.display = 'block';

        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const img = document.createElement('img');
                    img.src = evt.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        wrapper.style.display = 'none';
        previewContainer.innerHTML = '';
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
