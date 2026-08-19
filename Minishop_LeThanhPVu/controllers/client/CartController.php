<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\OrderDAO;
use DAO\CustomerDAO;
use Models\Order;
use Models\Customer;

class CartController
{
    private ProductDAO $productDAO;
    private OrderDAO $orderDAO;
    private CustomerDAO $customerDAO;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->productDAO = new ProductDAO();
        $this->orderDAO = new OrderDAO();
        $this->customerDAO = new CustomerDAO();
    }

    private function getCart(): array
    {
        return $_SESSION["cart"] ?? [];
    }

    private function saveCart(array $cart): void
    {
        $_SESSION["cart"] = $cart;
    }

    private function getCartCount(): int
    {
        $count = 0;
        foreach ($this->getCart() as $item) {
            $count += (int)($item["quantity"] ?? 0);
        }
        return $count;
    }

    private function getCartTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += (float)($item["price"] ?? 0) * (int)($item["quantity"] ?? 0);
        }
        return $total;
    }

    public function index()
    {
        $cart = $this->getCart();
        $total = $this->getCartTotal();
        $pageTitle = "Giỏ hàng";

        require __DIR__ . "/../../views/client/cart/index.php";
    }

    public function add()
    {
        header('Content-Type: application/json; charset=utf-8');

        $productId = (int)($_POST["productid"] ?? 0);
        $qty = max(1, (int)($_POST["quantity"] ?? 1));

        if ($productId <= 0) {
            echo json_encode([
                "success" => false,
                "message" => "Sản phẩm không hợp lệ."
            ]);
            exit;
        }

        $product = $this->productDAO->findById($productId);
        if (!$product) {
            echo json_encode([
                "success" => false,
                "message" => "Không tìm thấy sản phẩm trong cơ sở dữ liệu."
            ]);
            exit;
        }

        $price = ($product->discountPrice > 0 && $product->discountPrice < $product->price)
            ? (float)$product->discountPrice
            : (float)$product->price;

        $cart = $this->getCart();

        // Kiểm tra tổng số lượng đã có trong giỏ + số lượng muốn thêm mới so với tồn kho
        $currentQtyInCart = isset($cart[$productId]) ? (int)$cart[$productId]["quantity"] : 0;
        $newTotalQty = $currentQtyInCart + $qty;

        if ($newTotalQty > $product->quantity) {
            $remainingCanAdd = max(0, $product->quantity - $currentQtyInCart);
            if ($remainingCanAdd <= 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "Sản phẩm trong giỏ hàng đã đạt giới hạn tồn kho (tối đa {$product->quantity} sản phẩm)."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Không thể thêm {$qty} sản phẩm. Giỏ hàng đã có {$currentQtyInCart}, bạn chỉ có thể thêm tối đa {$remainingCanAdd} sản phẩm nữa."
                ]);
            }
            exit;
        }

        if (isset($cart[$productId])) {
            $cart[$productId]["quantity"] += $qty;
        } else {
            $cart[$productId] = [
                "productid" => $product->id,
                "productname" => $product->name,
                "image" => $product->image,
                "price" => $price,
                "quantity" => $qty
            ];
        }

        $this->saveCart($cart);

        echo json_encode([
            "success" => true,
            "message" => "Đã thêm sản phẩm vào giỏ hàng thành công!",
            "cartCount" => $this->getCartCount(),
            "cartTotal" => $this->getCartTotal()
        ]);
        exit;
    }

    public function update()
    {
        header('Content-Type: application/json; charset=utf-8');

        $productId = (int)($_POST["productid"] ?? 0);
        $quantity = (int)($_POST["quantity"] ?? 0);

        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            echo json_encode([
                "success" => false,
                "message" => "Sản phẩm không tồn tại trong giỏ hàng."
            ]);
            exit;
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
            $subtotal = 0;
        } else {
            $product = $this->productDAO->findById($productId);
            if ($product && $quantity > $product->quantity) {
                echo json_encode([
                    "success" => false,
                    "message" => "Không thể cập nhật: Số lượng vượt quá tồn kho (kho còn {$product->quantity} sản phẩm)."
                ]);
                exit;
            }
            $cart[$productId]["quantity"] = $quantity;
            $subtotal = $cart[$productId]["price"] * $quantity;
        }

        $this->saveCart($cart);

        echo json_encode([
            "success" => true,
            "message" => "Cập nhật giỏ hàng thành công!",
            "subtotal" => $subtotal,
            "cartTotal" => $this->getCartTotal(),
            "cartCount" => $this->getCartCount()
        ]);
        exit;
    }

    public function remove()
    {
        header('Content-Type: application/json; charset=utf-8');

        $productId = (int)($_POST["productid"] ?? 0);
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
        }

        echo json_encode([
            "success" => true,
            "message" => "Đã xóa sản phẩm khỏi giỏ hàng.",
            "cartTotal" => $this->getCartTotal(),
            "cartCount" => $this->getCartCount()
        ]);
        exit;
    }

    public function count()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "success" => true,
            "cartCount" => $this->getCartCount(),
            "cartTotal" => $this->getCartTotal()
        ]);
        exit;
    }

    public function checkout()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $cart = $this->getCart();
            if (empty($cart)) {
                header("Location: index.php?area=client&controller=cart&action=index");
                exit;
            }

            $fullname = trim($_POST["fullname"] ?? "");
            $phone = trim($_POST["phone"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $address = trim($_POST["address"] ?? "");
            $note = trim($_POST["note"] ?? "");

            if (empty($fullname) || empty($phone) || empty($address)) {
                $_SESSION["checkout_error"] = "Vui lòng nhập đầy đủ Họ tên, Số điện thoại và Địa chỉ nhận hàng.";
                header("Location: index.php?area=client&controller=cart&action=index");
                exit;
            }

            // Kiểm tra tồn kho trước khi đặt hàng
            foreach ($cart as $pId => $item) {
                $pObj = $this->productDAO->findById((int)$pId);
                if (!$pObj || $item["quantity"] > $pObj->quantity) {
                    $stock = $pObj ? $pObj->quantity : 0;
                    $_SESSION["checkout_error"] = "Sản phẩm '" . ($item["productname"] ?? '') . "' vượt quá số lượng tồn kho (trong kho chỉ còn {$stock} sản phẩm). Vui lòng cập nhật lại giỏ hàng.";
                    header("Location: index.php?area=client&controller=cart&action=index");
                    exit;
                }
            }

            try {
                // 1. Tìm hoặc tạo Customer
                $customer = $this->customerDAO->findByPhone($phone);
                if ($customer) {
                    $customerId = $customer->id;
                    // Cập nhật địa chỉ/email mới nhất nếu có
                    if (!empty($address) || !empty($email)) {
                        $customer->address = $address ?: $customer->address;
                        $customer->email = $email ?: $customer->email;
                        $customer->fullname = $fullname ?: $customer->fullname;
                        $this->customerDAO->update($customer);
                    }
                } else {
                    $newCustomer = new Customer($fullname, $phone, $email, $address, "Khách đặt từ website", 1);
                    $customerId = $this->customerDAO->insertGetId($newCustomer);
                    if ($customerId <= 0) {
                        throw new \Exception("Không thể tạo thông tin khách hàng.");
                    }
                }

                // 2. Tạo đơn hàng (Order)
                $orderCode = "DH" . date("YmdHis") . rand(10, 99);
                $totalAmount = $this->getCartTotal();
                
                $currentUser = $_SESSION["user"] ?? null;
                $userId = null;
                if (is_object($currentUser) && isset($currentUser->id)) {
                    $userId = (int)$currentUser->id;
                } elseif (is_array($currentUser) && isset($currentUser['id'])) {
                    $userId = (int)$currentUser['id'];
                }

                $order = new Order(
                    $customerId,
                    $userId,
                    $orderCode,
                    $totalAmount,
                    $note,
                    0 // 0: Chờ xử lý
                );

                // 3. Thực thi Transaction lưu Order & OrderDetail
                $orderId = $this->orderDAO->createOrderWithDetails($order, $cart);

                if ($orderId > 0) {
                    // Xóa giỏ hàng sau khi đặt thành công
                    unset($_SESSION["cart"]);
                    $_SESSION["last_order"] = [
                        "id" => $orderId,
                        "code" => $orderCode,
                        "fullname" => $fullname,
                        "phone" => $phone,
                        "address" => $address,
                        "total" => $totalAmount,
                        "count" => count($cart)
                    ];
                    header("Location: index.php?area=client&controller=cart&action=success");
                    exit;
                } else {
                    throw new \Exception("Không thể lưu đơn hàng.");
                }
            } catch (\Exception $e) {
                $_SESSION["checkout_error"] = "Có lỗi xảy ra trong quá trình đặt hàng: " . $e->getMessage();
                header("Location: index.php?area=client&controller=cart&action=index");
                exit;
            }
        }

        // Nếu là GET thì hiển thị lại trang giỏ hàng
        $this->index();
    }

    public function success()
    {
        $orderInfo = $_SESSION["last_order"] ?? null;
        $pageTitle = "Đặt hàng thành công";
        require __DIR__ . "/../../views/client/cart/success.php";
    }

    public function tracking()
    {
        $pageTitle = "Tra cứu đơn hàng";
        $order = null;
        $details = [];
        $error = "";

        $orderCode = trim($_GET['order_code'] ?? $_POST['order_code'] ?? '');
        $phone = trim($_GET['phone'] ?? $_POST['phone'] ?? '');

        if ($orderCode !== '' && $phone !== '') {
            $order = $this->orderDAO->findByOrderCodeAndPhone($orderCode, $phone);
            if ($order) {
                $details = $this->orderDAO->getOrderDetails((int)$order['id']);
            } else {
                $error = "Không tìm thấy đơn hàng khớp với Mã đơn hàng và Số điện thoại đã nhập.";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = "Vui lòng nhập đầy đủ Mã đơn hàng và Số điện thoại.";
        }

        require __DIR__ . "/../../views/client/cart/tracking.php";
    }
}
