<?php
require_once __DIR__ . '/../database/config.php';

class User
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function all($search = null)
    {
        if ($search) {
            $statement = $this->conn->prepare("SELECT * FROM users WHERE name LIKE ? ORDER BY id DESC");
            $statement->execute(["%$search%"]);
        } else {
            $statement = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
            $statement->execute();
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $statement = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $statement = $this->conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
        return $statement->execute([$data['name'], $data['email'], $data['role']]);
    }

    public function update($id, $data)
    {
        $statement = $this->conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        return $statement->execute([$data['name'], $data['email'], $data['role'], $id]);
    }

    public function delete($id)
    {
        $statement = $this->conn->prepare("DELETE FROM users WHERE id=?");
        $statement->execute([$id]);

        // Reset AUTO_INCREMENT
        $statement = $this->conn->query("SELECT MAX(id) AS max_id FROM users");
        $maxId = $statement->fetch()['max_id'] ?? 0;
        $nextId = $maxId + 1;

        $this->conn->exec("ALTER TABLE users AUTO_INCREMENT = $nextId");
    }
}


