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
        try {
            if ($search) {
                $searchTerm = '%' . trim($search) . '%';
                $statement = $this->conn->prepare("SELECT * FROM events WHERE name LIKE ? ORDER BY id DESC");
                $statement->execute([$searchTerm]);
            } else {
                $statement = $this->conn->query("SELECT * FROM events ORDER BY id DESC");
            }
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // error_log("Error fetching all events: " . $e->getMessage()); // Opsional
            return []; 
        }
    }

    public function find($id)
    {
        try {
            $statement = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
            $statement->execute([$id]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // error_log("Error finding event ID $id: " . $e->getMessage()); // Opsional
            return false;
        }
    }

    public function create($data)
    {
        try {
            $statement = $this->conn->prepare("INSERT INTO events (name, date, location) VALUES (?, ?, ?)");
            return $statement->execute([$data['name'], $data['date'], $data['location']]);
        } catch (PDOException $e) {
            // error_log("Error creating event: " . $e->getMessage()); // Opsional
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $statement = $this->conn->prepare("UPDATE events SET name=?, date=?, location=? WHERE id=?");
            return $statement->execute([$data['name'], $data['date'], $data['location'], $id]);
        } catch (PDOException $e) {
            // error_log("Error updating event ID $id: " . $e->getMessage()); // Opsional
            return false;
        }
    }

    public function delete($id)
{
    if (!$this->conn) {
        return false;
    }
    try {
        $this->conn->beginTransaction();
        $statement = $this->conn->prepare("DELETE FROM events WHERE id=?");
        $deleteSuccess = $statement->execute([$id]);
        $rowCount = $statement->rowCount();

        if ($deleteSuccess && $rowCount > 0) {
            $this->conn->commit();
            return true;
        } else {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    } catch (PDOException $e) {
        if ($this->conn && $this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
        return false;
    }
}
}