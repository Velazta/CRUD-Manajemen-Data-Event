<?php

class Database {
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db_name = "crud_db_manajementdataevent";
    private $username = "root";
    private $password = "";

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT =>true,
            PDO::ATTR_EMULATE_PREPARES =>false
        ];

        try{
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            // echo "Connection successfully";
        } catch(PDOException $e) {
            die("Connection Failed: " . $e->getMessage());
        }
    }

    public static function getConnection() {
        if(self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}

?>