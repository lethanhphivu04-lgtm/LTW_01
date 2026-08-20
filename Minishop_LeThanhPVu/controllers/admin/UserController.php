<?php
namespace Controllers\Admin;

use DAO\UserDAO;
use Models\User;
use Middleware\RoleMiddleware;

class UserController
{
    private UserDAO $userDAO;

    public function __construct()
    {
        RoleMiddleware::requireAdmin();
        $this->userDAO = new UserDAO();
    }

    public function index()
    {
        $pageTitle = "Quản lý Người dùng";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->userDAO->count("users", "fullname", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->userDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'id_asc' => 'ID tăng dần'
        ];

        require __DIR__ . "/../../views/admin/users/index.php";
    }

    public function create()
    {
        $pageTitle = "Thêm người dùng mới";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $role = (int)($_POST['role'] ?? 0);
            $status = (int)($_POST['status'] ?? 1);

            if ($fullname === '') $errors[] = 'Họ và tên không được để trống.';
            if ($username === '') $errors[] = 'Tên đăng nhập không được để trống.';
            if ($password === '') $errors[] = 'Mật khẩu không được để trống.';
            if ($email === '') {
                $errors[] = 'Email không được để trống.';
            } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = 'Email không đúng định dạng.';
            }
            if ($phone !== '' && !preg_match('/^(0|\+84)[35789][0-9]{8}$/', $phone)) {
                $errors[] = 'Số điện thoại không đúng định dạng VN (VD: 0901234567).';
            }

            // Kiểm tra username đã tồn tại chưa
            if ($username !== '' && $this->userDAO->findByUsername($username)) {
                $errors[] = 'Tên đăng nhập đã được sử dụng.';
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $u = new User(
                    $fullname,
                    $username,
                    $hashedPassword,
                    $email,
                    $phone !== '' ? $phone : null,
                    $address !== '' ? $address : null,
                    $role,
                    $status
                );
                $this->userDAO->insert($u);
                header("Location: index.php?area=admin&controller=user&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/users/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userDAO->findById($id);
        if (!$user) {
            header("Location: index.php?area=admin&controller=user&action=index");
            exit;
        }

        $pageTitle = "Chỉnh sửa người dùng";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $role = (int)($_POST['role'] ?? 0);
            $status = (int)($_POST['status'] ?? 1);

            if ($fullname === '') $errors[] = 'Họ và tên không được để trống.';
            if ($username === '') $errors[] = 'Tên đăng nhập không được để trống.';
            if ($email === '') {
                $errors[] = 'Email không được để trống.';
            } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = 'Email không đúng định dạng.';
            }
            if ($phone !== '' && !preg_match('/^(0|\+84)[35789][0-9]{8}$/', $phone)) {
                $errors[] = 'Số điện thoại không đúng định dạng VN (VD: 0901234567).';
            }

            $existing = $this->userDAO->findByUsername($username);
            if ($existing && $existing->id !== $id) {
                $errors[] = 'Tên đăng nhập đã được sử dụng bởi người dùng khác.';
            }

            if (empty($errors)) {
                $user->fullname = $fullname;
                $user->username = $username;
                if (!empty($password)) {
                    $user->password = password_hash($password, PASSWORD_DEFAULT);
                }
                $user->email = $email;
                $user->phone = $phone !== '' ? $phone : null;
                $user->address = $address !== '' ? $address : null;
                $user->role = $role;
                $user->status = $status;

                $this->userDAO->update($user);
                header("Location: index.php?area=admin&controller=user&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/users/edit.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userDAO->findById($id);
        if (!$user) {
            header("Location: index.php?area=admin&controller=user&action=index");
            exit;
        }

        $pageTitle = "Chi tiết người dùng - " . htmlspecialchars($user->fullname);
        require __DIR__ . "/../../views/admin/users/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        $currentUser = $_SESSION['user'] ?? null;
        if ($id > 0 && (!$currentUser || $currentUser->id !== $id)) {
            $this->userDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=user&action=index");
        exit;
    }
}
