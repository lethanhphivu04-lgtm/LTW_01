<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\OrderDAO;
use DAO\CustomerDAO;
use Models\Order;
use Models\Customer;
use Services\VNPayService;

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

    public function apply_coupon()
    {
        header('Content-Type: application/json; charset=utf-8');
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit;
        }

        $couponDAO = new \DAO\CouponDAO();
        $coupon = $couponDAO->findByCode($code);
        if (!$coupon) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hiệu lực.']);
            exit;
        }

        if (!empty($coupon->expiryDate) && strtotime($coupon->expiryDate) < strtotime(date('Y-m-d'))) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã hết hạn sử dụng.']);
            exit;
        }

        $cartTotal = $this->getCartTotal();
        if ($cartTotal < $coupon->minOrderValue) {
            echo json_encode([
                'success' => false,
                'message' => 'Đơn hàng tối thiểu từ ' . number_format($coupon->minOrderValue, 0, ',', '.') . ' đ để dùng mã này.'
            ]);
            exit;
        }

        $discount = 0;
        if ($coupon->discountType === 'percent') {
            $discount = $cartTotal * ($coupon->discountValue / 100);
            if ($coupon->maxDiscount !== null && $discount > $coupon->maxDiscount) {
                $discount = $coupon->maxDiscount;
            }
        } else {
            $discount = min($coupon->discountValue, $cartTotal);
        }

        $_SESSION['coupon'] = [
            'code' => $coupon->code,
            'discount_type' => $coupon->discountType,
            'discount_value' => $coupon->discountValue,
            'discount_amount' => $discount
        ];

        $finalTotal = max(0, $cartTotal - $discount);

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon_code' => $coupon->code,
            'discount_amount' => $discount,
            'discount_formatted' => number_format($discount, 0, ',', '.') . ' đ',
            'final_total' => $finalTotal,
            'final_total_formatted' => number_format($finalTotal, 0, ',', '.') . ' đ'
        ]);
        exit;
    }

    public function remove_coupon()
    {
        header('Content-Type: application/json; charset=utf-8');
        unset($_SESSION['coupon']);
        $cartTotal = $this->getCartTotal();
        echo json_encode([
            'success' => true,
            'message' => 'Đã hủy áp dụng mã giảm giá.',
            'cart_total' => $cartTotal,
            'cart_total_formatted' => number_format($cartTotal, 0, ',', '.') . ' đ'
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
            $paymentMethod = trim($_POST["payment_method"] ?? "cod");

            if (empty($fullname) || empty($phone) || empty($address)) {
                $_SESSION["checkout_error"] = "Vui lòng nhập đầy đủ Họ tên, Số điện thoại và Địa chỉ nhận hàng.";
                header("Location: index.php?area=client&controller=cart&action=index");
                exit;
            }

            // Regex kiểm tra số điện thoại Việt Nam (10 số, bắt đầu bằng 03, 05, 07, 08, 09)
            if (!preg_match('/^(0|\+84)[35789][0-9]{8}$/', $phone)) {
                $_SESSION["checkout_error"] = "Số điện thoại không đúng định dạng (VD: 0901234567).";
                header("Location: index.php?area=client&controller=cart&action=index");
                exit;
            }

            // Regex kiểm tra email nếu có nhập
            if (!empty($email) && !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                $_SESSION["checkout_error"] = "Địa chỉ email không đúng định dạng.";
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

                // 2. Tính tiền và áp dụng mã giảm giá (nếu có)
                $orderCode = "DH" . date("YmdHis") . rand(10, 99);
                $cartTotal = $this->getCartTotal();
                $couponCode = $_SESSION['coupon']['code'] ?? null;
                $discountAmount = (float)($_SESSION['coupon']['discount_amount'] ?? 0);
                $finalTotalAmount = max(0, $cartTotal - $discountAmount);
                
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
                    $finalTotalAmount,
                    $note,
                    0, // 0: Chờ xử lý
                    $paymentMethod,
                    $couponCode,
                    $discountAmount
                );

                // 3. Thực thi Transaction lưu Order & OrderDetail
                $orderId = $this->orderDAO->createOrderWithDetails($order, $cart);

                if ($orderId > 0) {
                    // Xóa giỏ hàng và coupon sau khi đặt thành công
                    unset($_SESSION["cart"]);
                    unset($_SESSION["coupon"]);

                    // Nếu thanh toán VNPay → redirect sang cổng thanh toán
                    if ($paymentMethod === 'vnpay') {
                        $_SESSION["pending_vnpay_order"] = [
                            "id" => $orderId,
                            "code" => $orderCode,
                            "fullname" => $fullname,
                            "phone" => $phone,
                            "address" => $address,
                            "total" => $finalTotalAmount
                        ];
                        $ipAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                        $orderInfo = "Thanh toan don hang " . $orderCode;
                        $paymentUrl = VNPayService::createPaymentUrl($orderCode, $finalTotalAmount, $orderInfo, $ipAddr);
                        header("Location: " . $paymentUrl);
                        exit;
                    }

                    // COD → Gửi email xác nhận (nếu có email) & hiển thị trang thành công
                    if (!empty($email)) {
                        \Services\EmailService::sendOrderConfirmation(
                            $email,
                            $fullname,
                            $orderCode,
                            $cart,
                            $finalTotalAmount,
                            $address,
                            $phone,
                            'cod'
                        );
                    }

                    $_SESSION["last_order"] = [
                        "id" => $orderId,
                        "code" => $orderCode,
                        "fullname" => $fullname,
                        "phone" => $phone,
                        "address" => $address,
                        "total" => $finalTotalAmount,
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

    /**
     * Xử lý callback từ VNPay sau khi thanh toán
     */
    public function vnpay_return()
    {
        $vnpData = $_GET;
        $result = VNPayService::validateReturn($vnpData);

        $orderCode = $vnpData['vnp_TxnRef'] ?? '';
        $vnpTransactionNo = $vnpData['vnp_TransactionNo'] ?? '';
        $vnpAmount = isset($vnpData['vnp_Amount']) ? (float)$vnpData['vnp_Amount'] / 100 : 0;
        $vnpBankCode = $vnpData['vnp_BankCode'] ?? '';

        $order = $this->orderDAO->findByOrderCode($orderCode);

        if ($result['isValid'] && $result['isSuccess'] && $order) {
            // Thanh toán thành công → cập nhật trạng thái đơn hàng sang "Đang xử lý" (1)
            $this->orderDAO->updatePaymentStatus($orderCode, 1);
            $paymentSuccess = true;

            // Gửi email xác nhận thanh toán thành công
            if (!empty($order['customer_email'])) {
                $orderDetails = $this->orderDAO->getOrderDetails((int)$order['id']);
                \Services\EmailService::sendOrderConfirmation(
                    $order['customer_email'],
                    $order['customer_name'] ?? 'Quý khách',
                    $orderCode,
                    $orderDetails,
                    (float)$order['total_amount'],
                    $order['customer_address'] ?? '',
                    $order['customer_phone'] ?? '',
                    'vnpay'
                );
            }
        } else {
            $paymentSuccess = false;
        }

        $pageTitle = $paymentSuccess ? "Thanh toán thành công" : "Thanh toán thất bại";
        $pendingOrder = $_SESSION["pending_vnpay_order"] ?? null;
        unset($_SESSION["pending_vnpay_order"]);

        require __DIR__ . "/../../views/client/cart/vnpay_return.php";
    }

    public function invoice()
    {
        $code = trim($_GET['code'] ?? '');
        $order = $this->orderDAO->findByOrderCode($code);
        if (!$order) {
            header("Location: index.php?area=client&controller=cart&action=tracking");
            exit;
        }
        $details = $this->orderDAO->getOrderDetails((int)$order['id']);
        $pageTitle = "Hóa đơn bán hàng #" . htmlspecialchars($order['order_code']);
        require __DIR__ . "/../../views/admin/orders/invoice.php";
    }
}
