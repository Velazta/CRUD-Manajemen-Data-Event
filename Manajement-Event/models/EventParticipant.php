<?php
require_once __DIR__ . '/../database/config.php';

class EventParticipant
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getParticipantByEvent($event_id)
    {
        $statement = $this->conn->prepare(
            "SELECT u.* FROM users u
            JOIN events_participants ep ON u.id = ep.user_id
            WHERE ep.event_id = ?"
        );
        $statement->execute([$event_id]);
        return $statement->fetchall(PDO::FETCH_ASSOC);
    }

    public function addParticipant($event_id, $user_id)
    {
        $statement = $this->conn->prepare("INSERT INTO events_participants (event_id, user_id) VALUES (?, ?)");
        return $statement->execute([$event_id, $user_id]);
    }

    public function removeParticipant($event_id, $user_id)
    {
        $statement = $this->conn->prepare("DELETE FROM events_participants WHERE event_id=? AND user_id=?");
        return $statement->execute([$event_id, $user_id]);
    }
}


