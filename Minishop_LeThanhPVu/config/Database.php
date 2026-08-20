<?php
namespace Config;

/**
 * Lớp cấu hình và thiết lập kết nối Cơ sở dữ liệu MySQL (MySQLi)
 */
class Database
{
    protected string $host = "localhost";                     // Máy chủ MySQL (XAMPP localhost)
    protected string $database = "lethanhphivu_database";     // Tên cơ sở dữ liệu
    protected string $username = "root";                     // Tài khoản kết nối mặc định
    protected string $password = "";                         // Mật khẩu kết nối
    protected ?\mysqli $conn = null;                         // Biến lưu giữ đối tượng kết nối

    public function __construct()
    {
        $this->connect();
    }

    /**
     * Khởi tạo kết nối MySQLi và đặt bảng mã UTF-8 tiếng Việt
     */
    protected function connect(): void
    {
        $this->conn = new \mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_errno) {
            throw new \Exception("Kết nối CSDL thất bại: " . $this->conn->connect_error);
        }
        // Đặt chuẩn mã hóa UTF-8 đầy đủ hỗ trợ tiếng Việt có dấu và Emoji
        $this->conn->set_charset("utf8mb4");
    }

    /**
     * Trả về đối tượng kết nối MySQLi đang hoạt động
     */
    public function getConnection(): \mysqli
    {
        return $this->conn;
    }

    /**
     * Đóng kết nối CSDL khi hoàn tất tác vụ
     */
    public function close(): void
    {
        if (isset($this->conn) && $this->conn !== null) {
            $this->conn->close();
        }
    }
}
