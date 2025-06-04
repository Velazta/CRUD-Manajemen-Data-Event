<?php
require_once __DIR__ . '/../database/config.php';

class Admin {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    
    // Membuat admin baru.
    public function create($data) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $data['password']); // Pastikan password sudah di-hash sebelum memanggil method ini
            return $stmt->execute();
        } catch (PDOException $e) {
            // Anda bisa menambahkan logging error di sini
            return false;
        }
    }

    
    // Mencari admin berdasarkan email.

    public function findByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    
    //  * Mencari admin berdasarkan ID.
    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    
    // Menyimpan remember_token untuk admin.
    public function setRememberToken($id, $token) {
        try {
            $stmt = $this->conn->prepare("UPDATE admins SET remember_token = :token WHERE id = :id");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    
    //  Mencari admin berdasarkan remember_token.
     
    public function findByRememberToken($token) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE remember_token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>