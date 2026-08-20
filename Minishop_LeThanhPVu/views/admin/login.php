<?php
if (!class_exists('Middleware\CsrfMiddleware')) {
    require_once __DIR__ . "/../../autoload.php";
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
\Middleware\CsrfMiddleware::generateToken();

// Xác định base URL
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$pos = strpos($scriptDir, '/views');
$baseUrl = ($pos !== false) ? substr($scriptDir, 0, $pos) : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/LTW_01/Minishop_LeThanhPVu';

$username = $username ?? '';
$errors = $errors ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - MiniShop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">ĐĂNG NHẬP</h3>

                        <form action="<?= $baseUrl ?>/index.php?area=admin&controller=auth&action=login" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên đăng nhập</label>
                                <input type="text" id="usernameInput" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Nhập tên đăng nhập" value="<?= htmlspecialchars($username) ?>" required>
                                <?php if (isset($errors["username"])): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors["username"]) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mật khẩu</label>
                                <div class="input-group">
                                    <input type="password" id="passwordInput" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Nhập mật khẩu" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <?php if (isset($errors["password"])): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors["password"]) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-bold py-2">Đăng nhập</button>
                            </div>
                        </form>

                        <hr class="my-4 text-muted">

                        <!-- Dropdown tài khoản mẫu -->
                        <div class="p-3 bg-light rounded border border-primary-subtle">
                            <label for="sampleAccountSelect" class="form-label fw-semibold text-primary mb-1">
                                <i class="bi bi-person-badge me-1"></i>Chọn tài khoản mẫu (Demo):
                            </label>
                            <select id="sampleAccountSelect" class="form-select border-primary">
                                <option value="">-- Chọn tài khoản để điền tự động --</option>
                                <option value="admin" data-password="123456">👑 Quản trị viên (admin / 123456)</option>
                                <option value="nv_an" data-password="123456">👤 NV: Nguyễn Văn An (nv_an / 123456)</option>
                                <option value="nv_bich" data-password="123456">👤 NV: Trần Thị Bích (nv_bich / 123456)</option>
                                <option value="nv_cuong" data-password="123456">👤 NV: Phạm Minh Cường (nv_cuong / 123456)</option>
                                <option value="nv_dung" data-password="123456">👤 NV: Hoàng Anh Dũng (nv_dung / 123456)</option>
                            </select>
                        </div>

                        <!-- Nút quay lại trang người dùng -->
                        <div class="text-center mt-4 pt-2 border-top">
                            <a href="<?= $baseUrl ?>/" class="btn btn-outline-secondary btn-sm w-100 py-2 text-decoration-none fw-semibold">
                                <i class="bi bi-house-door me-1"></i> Quay lại trang chủ người dùng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('sampleAccountSelect')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const username = this.value;
        const password = selected.getAttribute('data-password') || '';
        
        if (username) {
            document.getElementById('usernameInput').value = username;
            document.getElementById('passwordInput').value = password;
        }
    });

    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const pwd = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        const isSecret = pwd.type === 'password';
        pwd.type = isSecret ? 'text' : 'password';
        icon.className = isSecret ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
    </script>
</body>
</html>
