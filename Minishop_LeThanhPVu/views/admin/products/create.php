<?php
$pageTitle = $pageTitle ?? "Thêm sản phẩm";
$errors = $errors ?? [];
ob_start();
?>
<h4 class="fw-bold mb-3">THÊM SẢN PHẨM MỚI</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=product&action=create" enctype="multipart/form-data" class="card card-body shadow-sm">
    <div class="row g-3">
        <!-- Thông tin cơ bản -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($_POST['proname'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug (để trống tự tạo)</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= (isset($_POST['category_id']) && (int)$_POST['category_id'] === $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= (isset($_POST['brand_id']) && (int)$_POST['brand_id'] === $b->id) ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= htmlspecialchars($_POST['discount_price'] ?? '0') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($_POST['quantity'] ?? '10') ?>" required>
        </div>

        <!-- Ảnh chính -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Hình ảnh chính</label>
            <input type="file" id="imageInput" name="image" class="form-control" accept="image/*">
            <div class="form-text">Ảnh đại diện chính của sản phẩm.</div>
            <div class="mt-2 text-center p-2 border rounded bg-light" id="mainPreviewBox" style="display:none;">
                <img src="" class="img-thumbnail" id="mainPreview" style="max-height: 140px; object-fit: contain;">
            </div>
        </div>

        <!-- Trạng thái & Ảnh phụ -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Trạng thái</label>
            <select name="status" class="form-select mb-3">
                <option value="1" <?= (!isset($_POST['status']) || $_POST['status'] == 1) ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ngừng bán</option>
            </select>

            <label class="form-label fw-semibold">Ảnh phụ (Album / Gallery)</label>
            <input type="file" id="galleryInput" name="gallery[]" class="form-control" accept="image/*" multiple>
            <div class="form-text">Giữ phím <kbd>Ctrl</kbd> hoặc <kbd>Shift</kbd> để chọn nhiều ảnh cùng lúc.</div>
        </div>

        <!-- Xem trước ảnh phụ -->
        <div class="col-12" id="newGalleryPreviewWrapper" style="display: none;">
            <label class="form-label fw-semibold text-primary"><i class="bi bi-images me-1"></i> Ảnh phụ đã chọn:</label>
            <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" id="newGalleryPreview"></div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="col-12 mt-3">
            <button class="btn btn-success" type="submit"><i class="bi bi-plus-circle me-1"></i> Lưu sản phẩm</button>
            <a href="index.php?area=admin&controller=product&action=index" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</form>

<script>
// Preview ảnh chính khi chọn file
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const box = document.getElementById('mainPreviewBox');
    const preview = document.getElementById('mainPreview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            if (preview) preview.src = evt.target.result;
            if (box) box.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        if (box) box.style.display = 'none';
    }
});

// Preview các ảnh phụ khi chọn nhiều file
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
