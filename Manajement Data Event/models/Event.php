<?php
require_once __DIR__ . '/../database/config.php';


class Event
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function all($search = null)
    {
        if ($search) {
            $statement = $this->conn->prepare("SELECT * FROM events WHERE name LIKE ? ORDER BY id DESC");
            $statement->execute(["%$search%"]);
        } else {
            $statement = $this->conn->query("SELECT * FROM events ORDER BY id DESC");
            $statement->execute();
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function find($id)
    {
        $statement = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $statement = $this->conn->prepare("INSERT INTO events (name, date, location) VALUES (?, ?, ?)");
        return $statement->execute([$data['name'], $data['date'], $data['location']]);
    }

    public function update($id, $data)
    {
        $statement = $this->conn->prepare("UPDATE events SET name=?, date=?, location=? WHERE id=?");
        return $statement->execute([$data['name'], $data['date'], $data['location'], $id]);
    }

    public function delete($id)
    {
        $statement = $this->conn->prepare("DELETE FROM events WHERE id=?");
        $statement->execute([$id]);

        // Reset AUTO_INCREMENT
        $statement = $this->conn->query("SELECT MAX(id) AS max_id FROM events");
        $maxId = $statement->fetch()['max_id'] ?? 0;
        $nextId = $maxId + 1;

        $this->conn->exec("ALTER TABLE events AUTO_INCREMENT = $nextId");
    }
}
