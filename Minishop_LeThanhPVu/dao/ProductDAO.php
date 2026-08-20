<?php
namespace DAO;

use Models\Product;

class ProductDAO extends BaseDAO
{
    private string $clientSelect = "SELECT p.*, c.catename AS category_name, c.slug AS category_slug, b.brandname AS brand_name, b.slug AS brand_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.status = 1";

    // Chuyển đổi dữ liệu từ MySQL row sang Object Product
    private function mapRow(array $row): Product
    {
        $p = new Product(
            (int)$row["category_id"],
            (int)$row["brand_id"],
            $row["proname"],
            $row["slug"],
            (float)$row["price"],
            (float)$row["discount_price"],
            (int)$row["quantity"],
            $row["image"],
            $row["description"],
            (int)$row["status"]
        );
        $p->id = (int)$row["id"];
        $p->createdAt = $row["created_at"] ?? '';
        $p->updatedAt = $row["updated_at"] ?? '';
        return $p;
    }

    // Lấy toàn bộ sản phẩm
    public function getAll(): array
    {
        $list = [];
        $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at 
                FROM products 
                ORDER BY id DESC";
        $result = $this->executeQuery($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRow($row);
            }
        }
        return $list;
    }

    // Tìm sản phẩm theo ID
    public function findById(int $id): ?Product
    {
        $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at 
                FROM products 
                WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $this->mapRow($row);
        }
        return null;
    }

    // Thêm mới sản phẩm
    public function insert(Product $p): int|bool
    {
        $sql = "INSERT INTO products(category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "iissddissi",
            $p->categoryId,
            $p->brandId,
            $p->name,
            $p->slug,
            $p->price,
            $p->discountPrice,
            $p->quantity,
            $p->image,
            $p->description,
            $p->status
        );
        if ($stmt->execute()) {
            return $this->conn->insert_id ?: true;
        }
        return false;
    }

    // Cập nhật sản phẩm
    public function update(Product $p): bool
    {
        $sql = "UPDATE products 
                SET category_id=?, brand_id=?, proname=?, slug=?, price=?, discount_price=?, quantity=?, image=?, description=?, status=? 
                WHERE id=?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param(
            "iissddissii",
            $p->categoryId,
            $p->brandId,
            $p->name,
            $p->slug,
            $p->price,
            $p->discountPrice,
            $p->quantity,
            $p->image,
            $p->description,
            $p->status,
            $p->id
        );
        return $stmt->execute();
    }

    // Xóa sản phẩm và xóa file ảnh vật lý liên quan
    public function delete(int $id): bool
    {
        // 1. Xóa file ảnh phụ trong gallery
        $galleryImages = $this->getImagesByProductId($id);
        foreach ($galleryImages as $gImg) {
            if (!empty($gImg['image'])) {
                $gPath = __DIR__ . "/../uploads/products/" . $gImg['image'];
                if (file_exists($gPath)) {
                    unlink($gPath);
                }
            }
        }

        // 2. Xóa file ảnh chính
        $prod = $this->findById($id);
        if ($prod && !empty($prod->image)) {
            $mainPath = __DIR__ . "/../uploads/products/" . $prod->image;
            if (file_exists($mainPath)) {
                unlink($mainPath);
            }
        }

        // 3. Xóa record trong DB
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Lấy 1 sản phẩm kèm tên danh mục và thương hiệu
    public function findByIdWithJoin(int $id): ?array
    {
        $sql = "SELECT p.*, c.catename, b.brandname 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                INNER JOIN brands b ON p.brand_id = b.id 
                WHERE p.id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // Đếm tổng số sản phẩm (hỗ trợ tìm kiếm theo từ khóa)
    public function count(string $table = "products", string $column = "proname", string $keyword = ""): int
    {
        if ($keyword === '') {
            return parent::count("products");
        }
        $sql = "SELECT COUNT(*) AS total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?";
        $stmt = $this->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("sss", $kw, $kw, $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    // Phân trang và tìm kiếm cho trang Quản trị (Admin)
    public function getPage(int $limit, int $offset, string $keyword = '', string $sort = ''): array
    {
        $list = [];
        $sql = "SELECT p.*, c.catename, b.brandname 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id";
        
        if ($keyword !== '') {
            $sql .= " WHERE p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?";
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY p.proname ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.proname DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY p.discount_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.discount_price DESC";
                break;
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }
        $sql .= " LIMIT ? OFFSET ?";

        $stmt = $this->prepare($sql);
        if ($keyword !== '') {
            $kw = "%$keyword%";
            $stmt->bind_param("sssii", $kw, $kw, $kw, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    public function countAll(): int
    {
        return $this->count("products");
    }

    // Lấy 5 sản phẩm mới nhất cho Dashboard
    public function getNewest(int $limit = 5): array
    {
        $list = [];
        $sql = "SELECT p.*, c.catename, b.brandname 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                ORDER BY p.id DESC 
                LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['category_name'] = $row['catename'] ?? '';
            $row['brand_name'] = $row['brandname'] ?? '';
            $list[] = $row;
        }
        return $list;
    }

    // Lấy danh sách sản phẩm bán chạy nhất cho Trang chủ Client
    public function getBestSelling(int $limit = 4): array
    {
        $list = [];
        $sql = "SELECT p.*, c.catename AS category_name, c.slug AS category_slug, b.brandname AS brand_name,
                       COALESCE(SUM(od.quantity), 0) AS total_sold
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN order_details od ON p.id = od.product_id
                WHERE p.status = 1
                GROUP BY p.id
                ORDER BY total_sold DESC, p.id ASC
                LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Thêm ảnh phụ gallery cho sản phẩm
    public function insertImage(int $productId, string $image): bool
    {
        $sql = "INSERT INTO product_images(product_id, image) VALUES (?, ?)";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("is", $productId, $image);
        return $stmt->execute();
    }

    // Lấy danh sách ảnh phụ gallery
    public function getImagesByProductId(int $productId): array
    {
        $list = [];
        $sql = "SELECT id, product_id, image, sort_order, created_at 
                FROM product_images 
                WHERE product_id = ? 
                ORDER BY id DESC";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Xóa ảnh phụ gallery (xóa file vật lý trước khi xóa DB)
    public function deleteImage(int $id): bool
    {
        $sqlSelect = "SELECT image FROM product_images WHERE id = ?";
        $stmtSelect = $this->prepare($sqlSelect);
        $stmtSelect->bind_param("i", $id);
        $stmtSelect->execute();
        $res = $stmtSelect->get_result();
        if ($row = $res->fetch_assoc()) {
            $filePath = __DIR__ . "/../uploads/products/" . $row['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $sqlDelete = "DELETE FROM product_images WHERE id = ?";
        $stmtDelete = $this->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $id);
        return $stmtDelete->execute();
    }

    // --- Client Queries ---

    // Lấy sản phẩm theo Slug danh mục
    public function getByCategorySlug(string $slug, int $limit = 12, int $offset = 0): array
    {
        $list = [];
        $sql = $this->clientSelect . " AND c.slug = ? ORDER BY p.id DESC LIMIT ? OFFSET ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("sii", $slug, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Đếm sản phẩm theo Slug danh mục
    public function countByCategorySlug(string $slug): int
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 AND c.slug = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy sản phẩm theo Slug thương hiệu
    public function getByBrandSlug(string $slug, int $limit = 12, int $offset = 0): array
    {
        $list = [];
        $sql = $this->clientSelect . " AND b.slug = ? ORDER BY p.id DESC LIMIT ? OFFSET ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("sii", $slug, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Đếm sản phẩm theo Slug thương hiệu
    public function countByBrandSlug(string $slug): int
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE p.status = 1 AND b.slug = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    // Tìm sản phẩm theo Slug (Trang chi tiết)
    public function findBySlug(string $slug): ?array
    {
        $sql = $this->clientSelect . " AND p.slug = ? LIMIT 1";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    // Tìm kiếm cơ bản
    public function search(string $keyword, int $limit = 12, int $offset = 0): array
    {
        return $this->searchAdvanced(['keyword' => $keyword], $limit, $offset);
    }

    public function countSearch(string $keyword): int
    {
        return $this->countSearchAdvanced(['keyword' => $keyword]);
    }

    // Tìm kiếm nâng cao có bộ lọc
    public function searchAdvanced(array $filters, int $limit = 12, int $offset = 0): array
    {
        $list = [];
        $conditions = ["p.status = 1"];
        $params = [];
        $types = "";

        if (!empty($filters['keyword'])) {
            $conditions[] = "(p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?)";
            $kw = "%" . $filters['keyword'] . "%";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $types .= "sss";
        }

        if (!empty($filters['category'])) {
            $conditions[] = "c.slug = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['brand'])) {
            $conditions[] = "b.slug = ?";
            $params[] = $filters['brand'];
            $types .= "s";
        }

        if (!empty($filters['price_min'])) {
            $conditions[] = "p.discount_price >= ?";
            $params[] = (float)$filters['price_min'];
            $types .= "d";
        }

        if (!empty($filters['price_max'])) {
            $conditions[] = "p.discount_price <= ?";
            $params[] = (float)$filters['price_max'];
            $types .= "d";
        }

        if (!empty($filters['on_sale'])) {
            $conditions[] = "p.discount_price > 0 AND p.discount_price < p.price";
        }

        $sql = "SELECT p.*, c.catename AS category_name, c.slug AS category_slug, b.brandname AS brand_name, b.slug AS brand_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE " . implode(" AND ", $conditions);

        // Xử lý sắp xếp
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.discount_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.discount_price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.proname ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.proname DESC";
                break;
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Đếm kết quả tìm kiếm nâng cao
    public function countSearchAdvanced(array $filters): int
    {
        $conditions = ["p.status = 1"];
        $params = [];
        $types = "";

        if (!empty($filters['keyword'])) {
            $conditions[] = "(p.proname LIKE ? OR c.catename LIKE ? OR b.brandname LIKE ?)";
            $kw = "%" . $filters['keyword'] . "%";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $types .= "sss";
        }

        if (!empty($filters['category'])) {
            $conditions[] = "c.slug = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['brand'])) {
            $conditions[] = "b.slug = ?";
            $params[] = $filters['brand'];
            $types .= "s";
        }

        if (!empty($filters['price_min'])) {
            $conditions[] = "p.discount_price >= ?";
            $params[] = (float)$filters['price_min'];
            $types .= "d";
        }

        if (!empty($filters['price_max'])) {
            $conditions[] = "p.discount_price <= ?";
            $params[] = (float)$filters['price_max'];
            $types .= "d";
        }

        if (!empty($filters['on_sale'])) {
            $conditions[] = "p.discount_price > 0 AND p.discount_price < p.price";
        }

        $sql = "SELECT COUNT(*) AS total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id 
                WHERE " . implode(" AND ", $conditions);

        $stmt = $this->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    // Lấy danh sách sản phẩm giảm giá cho Trang chủ
    public function getDiscounted(int $limit = 8): array
    {
        $list = [];
        $sql = $this->clientSelect . " AND p.discount_price < p.price AND p.discount_price > 0 ORDER BY (p.price - p.discount_price) DESC LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Lấy sản phẩm mới nhất
    public function getNewestClient(int $limit = 8): array
    {
        $list = [];
        $sql = $this->clientSelect . " ORDER BY p.id DESC LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    // Lấy tất cả sản phẩm cho Client có phân trang
    public function getAllClient(int $limit = 12, int $offset = 0): array
    {
        $list = [];
        $sql = $this->clientSelect . " ORDER BY p.id DESC LIMIT ? OFFSET ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    public function countAllClient(): int
    {
        $res = $this->executeQuery("SELECT COUNT(*) AS total FROM products WHERE status = 1");
        return $res ? (int)$res->fetch_assoc()['total'] : 0;
    }
}
