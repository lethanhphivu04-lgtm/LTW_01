<?php
namespace DAO;

use Config\Database;

/**
 * Lớp DAO Cơ sở (Base Data Access Object) kế thừa kết nối Database
 * Cung cấp các hàm dùng chung: thực thi câu lệnh SQL, chuẩn bị Statement và đếm tổng dòng.
 */
class BaseDAO extends Database
{
    /**
     * Thực thi câu lệnh SQL trực tiếp
     */
    protected function executeQuery(string $sql): \mysqli_result|false
    {
        return $this->conn->query($sql);
    }

    /**
     * Chuẩn bị câu lệnh truy vấn có tham số (Prepared Statement chống SQL Injection)
     */
    protected function prepare(string $sql): \mysqli_stmt|false
    {
        return $this->conn->prepare($sql);
    }

    /**
     * Đếm tổng số bản ghi trong bảng (hỗ trợ phân trang và tìm kiếm)
     */
    public function count(string $table, string $column = "", string $keyword = ""): int
    {
        if ($keyword === "" || $column === "") {
            $sql = "SELECT COUNT(*) AS total FROM $table";
            $result = $this->conn->query($sql);
            $row = $result ? $result->fetch_assoc() : null;
            return (int)($row["total"] ?? 0);
        }
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE $column LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("s", $kw);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row["total"] ?? 0);
    }
}
