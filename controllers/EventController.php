<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventParticipant.php';
require_once __DIR__ . '/../models/User.php';

class EventController {
    private $eventModel;
    private $participantModel;
    private $userModel;

    public function __construct() {
        $this->eventModel = new Event();
        $this->participantModel = new EventParticipant();
        $this->userModel = new User();
    }

    public function addParticipant() {
        $event_id = $_GET['id'] ?? null;
        if (!$event_id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            if ($user_id) {
                $this->participantModel->addParticipant($event_id, $user_id);
            }
        }
        header("Location: index.php?controller=Event&action=show&id=$event_id");
        exit;
    }

    public function index() {
        $search = $_GET['search'] ?? null; // ambil parameter search dari URL
        $events = $this->eventModel->all($search);  // panggil method all dengan search
        require __DIR__ . '/../views/events/index.php';
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        $event = $this->eventModel->find($id);
        $participants = $this->participantModel->getParticipantByEvent($id);
        $allUsers = $this->userModel->all(); // untuk dropdown tambah peserta
        require __DIR__ . '/../views/events/show.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'date' => $_POST['date'],
                'location' => $_POST['location']
            ];
            $this->eventModel->create($data);
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        require __DIR__ . '/../views/events/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'date' => $_POST['date'],
                'location' => $_POST['location']
            ];
            $this->eventModel->update($id, $data);
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        $event = $this->eventModel->find($id);
        require __DIR__ . '/../views/events/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->eventModel->delete($id);
        }
        header("Location: index.php?controller=Event&action=index");
        exit;
    }
}
?>
