<?php
namespace DAO;

use Config\Database;

class BaseDAO extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    // Thực thi câu lệnh SELECT
    protected function executeQuery(string $sql): \mysqli_result|false
    {
        return $this->conn->query($sql);
    }

    protected function prepare(string $sql): \mysqli_stmt|false
    {
        return $this->conn->prepare($sql);
    }

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
