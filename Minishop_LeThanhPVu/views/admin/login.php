<?php
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../dao/UserDAO.php";
require_once __DIR__ . "/../../middleware/GuestMiddleware.php";
require_once __DIR__ . "/../../middleware/CsrfMiddleware.php";
GuestMiddleware::handle();
CsrfMiddleware::generateToken();

$username = "";
$password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    CsrfMiddleware::verify();
    $username = trim($_POST["username"] ?? "");

    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors["username"] = "Vui lòng nhập tên đăng nhập.";
    }
    if ($password === "") {
        $errors["password"] = "Vui lòng nhập mật khẩu.";
    }

    if (empty($errors)) {
        $userDAO = new UserDAO();
        $user = $userDAO->findByUsername($username);

        if (!$user) {
            $errors["username"] = "Tên đăng nhập không tồn tại.";
        } elseif (!password_verify($password, $user->password)) {
            $errors["password"] = "Mật khẩu không chính xác.";
        } else {
            $_SESSION["user"] = $user;
            if (!empty($_POST["remember"])) {
                $rememberToken = base64_encode($user->username . ":" . md5($user->username . "MINISHOP_SECRET_KEY"));
                setcookie("remember_user", $rememberToken, time() + 7 * 24 * 3600, "/");
            } else {
                setcookie("remember_user", "", time() - 3600, "/");
            }
            header("Location: dashboard.php");
            exit;
        }

    }
}
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
                        <form action="login.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên đăng nhập</label>
                                <input type="text" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Nhập tên đăng nhập" value="<?= htmlspecialchars($username) ?>" required>
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
                                <button type="submit" class="btn btn-primary fw-bold">Đăng nhập</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
