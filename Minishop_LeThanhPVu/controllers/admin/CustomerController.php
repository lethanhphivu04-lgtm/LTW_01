<?php
namespace Controllers\Admin;

use DAO\CustomerDAO;
use Models\Customer;

class CustomerController
{
    private CustomerDAO $customerDAO;

    public function __construct()
    {
        $this->customerDAO = new CustomerDAO();
    }

    public function index()
    {
        $pageTitle = "Quản lý Khách hàng";
        $keyword = trim($_GET['keyword'] ?? '');
        $sort = trim($_GET['sort'] ?? 'default');
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->customerDAO->count("customers", "fullname", $keyword);
        $totalPages = (int)ceil($totalRecords / $limit);
        $list = $this->customerDAO->getPage($limit, $offset, $keyword, $sort);

        $sortOptions = [
            'default' => 'Mới nhất',
            'name_asc' => 'Tên A-Z',
            'name_desc' => 'Tên Z-A',
            'id_asc' => 'ID tăng dần'
        ];

        require __DIR__ . "/../../views/admin/customers/index.php";
    }

    public function create()
    {
        $pageTitle = "Thêm khách hàng mới";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($fullname === '') $errors[] = 'Họ tên không được để trống.';
            if ($phone === '') $errors[] = 'Số điện thoại không được để trống.';

            if (empty($errors)) {
                $c = new Customer(
                    $fullname,
                    $phone,
                    $email !== '' ? $email : null,
                    $address !== '' ? $address : null,
                    $note !== '' ? $note : null,
                    $status
                );
                $this->customerDAO->insert($c);
                header("Location: index.php?area=admin&controller=customer&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/customers/create.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $customer = $this->customerDAO->findById($id);
        if (!$customer) {
            header("Location: index.php?area=admin&controller=customer&action=index");
            exit;
        }

        $pageTitle = "Chỉnh sửa khách hàng";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if ($fullname === '') $errors[] = 'Họ tên không được để trống.';
            if ($phone === '') $errors[] = 'Số điện thoại không được để trống.';

            if (empty($errors)) {
                $customer->fullname = $fullname;
                $customer->phone = $phone;
                $customer->email = $email !== '' ? $email : null;
                $customer->address = $address !== '' ? $address : null;
                $customer->note = $note !== '' ? $note : null;
                $customer->status = $status;

                $this->customerDAO->update($customer);
                header("Location: index.php?area=admin&controller=customer&action=index");
                exit;
            }
        }

        require __DIR__ . "/../../views/admin/customers/edit.php";
    }

    public function detail()
    {
        $id = (int)($_GET['id'] ?? 0);
        $customer = $this->customerDAO->findById($id);
        if (!$customer) {
            header("Location: index.php?area=admin&controller=customer&action=index");
            exit;
        }

        $pageTitle = "Chi tiết khách hàng - " . htmlspecialchars($customer->fullname);
        require __DIR__ . "/../../views/admin/customers/detail.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->customerDAO->delete($id);
        }
        header("Location: index.php?area=admin&controller=customer&action=index");
        exit;
    }
}
