<?php

class Database
{
    private const HOST_NAME = 'localhost';
    private const USER_NAME = 'root';
    private const PASSWORD = '';
    private const DATA_BASE = '';
    private  $conn;
    private $stmt;

    public function query($sql, $params)
    {
        $stmt = mysqli_prepare($this->conn, $sql);
        $data_types = '';
        foreach ($params as $param) {
            if (is_string($param)) {
                $data_types .= 's';
            } elseif (is_int($param)) {
                $data_types .= 'i';
            }
        }


        mysqli_stmt_bind_param($stmt, $data_types, ...$params);
        mysqli_stmt_execute($stmt);

        $this->stmt = $stmt;
    }

    public function create($table, $data)
    {
        $sql = "INSERT INTO `$table`(`" . implode('`, `', array_keys($data)) . "`) VALUES (" . substr(str_repeat('?,', count($data)), 0, -1) . ')';
        $this->query($sql, array_values($data));
        return mysqli_affected_rows($this->conn);
    }


    public function update($table, $data, $where)
    {
        $sql = "UPDATE `$table` SET " . implode(' = ?, ', array_keys($data)) . " = ? WHERE " . implode(' =? AND ', array_keys($where)) . " =?";
        $this->query($sql, array_merge(array_values($data), array_values($where)));
        return mysqli_affected_rows($this->conn);
    }

    public function select($table, $columns = [], $where = [])
    {
        $sql = "SELECT `" . implode('` ,`', $columns) . "` FROM `$table` WHERE " . implode(' = ? AND ', array_keys($where)) . ' = ?';

        $this->query($sql, array_values($where));
        $result = mysqli_stmt_get_result($this->stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM `$table` WHERE " . implode(' =? AND ', array_keys($where)) . " =?";
        $this->query($sql, array_values($where));
        return mysqli_affected_rows($this->conn);
    }


    public function __construct()
    {
        $this->conn = mysqli_connect(self::HOST_NAME, self::USER_NAME, self::PASSWORD, self::DATA_BASE);
    }

    public function __destruct()
    {
        mysqli_close($this->conn);
    }
}
