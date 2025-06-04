<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/EventParticipant.php';
require_once __DIR__ . '/../models/User.php';

class EventController
{
    private $eventModel;
    private $participantModel;
    private $userModel;

    public function __construct()
    {
        AuthController::protectPage();
        $this->eventModel = new Event();
        $this->participantModel = new EventParticipant();
        $this->userModel = new User();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    // 1
    private function sanitizeInput($data)
    {
        $data = trim($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    // 1
    private function validateEventData($data)
    {
        $errors = [];

        // validasi nama event
        if (empty($data['name'])) {
            $errors['name'] = "nama event wajib diisi.";
        } elseif (strlen($data['name']) > 150) {
            $errors['name'] = "nama event tidak boleh lebih dari 150 kata";
        }

        // validasi tanggal
        if (empty($data['date'])) {
            $errors['date'] = "tanggal event wajib diisi";
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $data['date']);
            if (!$d || $d->format('Y-m-d') !== $data['date']) {
                $errors['date'] = "format tanggal tidak valid";
            }
        }

        // validasi lokasi
        if (empty($data['location'])) {
            $errors['location'] = "lokasi event wajib diisi";
        } elseif (strlen($data['location']) > 255) {
            $errors['location'] = "lokasi event tidak boleh lebih dari 255 kata";
        }

        return $errors;
    }

    public function index()
    {
        $search = $_GET['search'] ?? null;
        if ($search !== null) {
            $search = $this->sanitizeInput($search);
        }
        $events = $this->eventModel->all($search);
        require __DIR__ . '/../views/events/index.php';
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        // Sanitasi ID 
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            // Handle error ID tidak valid
            $_SESSION['error_message_global'] = "ID Event tidak valid.";
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        $event = $this->eventModel->find($id);
        if (!$event) {
            $_SESSION['error_message_global'] = "Event tidak ditemukan.";
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        $participants = $this->participantModel->getParticipantByEvent($id);
        $allUsers = $this->userModel->all();
        require __DIR__ . '/../views/events/show.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitasi data input
            $inputData = [
                'name' => $this->sanitizeInput($_POST['name'] ?? ''),
                'date' => $this->sanitizeInput($_POST['date'] ?? ''), // format YYYY-MM-DD
                'location' => $this->sanitizeInput($_POST['location'] ?? '')
            ];

            // Validasi data
            $errors = $this->validateEventData($inputData);

            if (empty($errors)) {
                // Jika tidak ada error, buat event
                if ($this->eventModel->create($inputData)) {
                    $_SESSION['success_message_global'] = "Event berhasil ditambahkan!";
                    // Hapus old input dari session jika sukses
                    unset($_SESSION['old_event_input']);
                    header("Location: index.php?controller=Event&action=index");
                    exit;
                } else {
                    $_SESSION['form_errors'] = ['general' => 'Gagal menyimpan event ke database.'];
                }
            } else {
                // error, simpan error dan input lama ke session
                $_SESSION['form_errors'] = $errors;
                $_SESSION['old_event_input'] = $inputData; 
            }
            header("Location: index.php?controller=Event&action=create");
            exit;
        }
        // Menampilkan form create (GET request)
        $form_errors = $_SESSION['form_errors'] ?? [];
        $old_input = $_SESSION['old_event_input'] ?? [];
        unset($_SESSION['form_errors']); 
        unset($_SESSION['old_event_input']); 

        require __DIR__ . '/../views/events/create.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            $_SESSION['error_message_global'] = "ID Event tidak valid.";
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        $event = $this->eventModel->find($id);
        if (!$event) {
            $_SESSION['error_message_global'] = "Event tidak ditemukan.";
            header("Location: index.php?controller=Event&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitasi data input
            $inputData = [
                'name' => $this->sanitizeInput($_POST['name'] ?? ''),
                'date' => $this->sanitizeInput($_POST['date'] ?? ''),
                'location' => $this->sanitizeInput($_POST['location'] ?? '')
            ];

            // Validasi data
            $errors = $this->validateEventData($inputData);

            if (empty($errors)) {
                if ($this->eventModel->update($id, $inputData)) {
                    $_SESSION['success_message_global'] = "Event berhasil diperbarui!";
                    unset($_SESSION['old_event_input']); // Hapus old input dari session jika sukses
                    header("Location: index.php?controller=Event&action=index");
                    exit;
                } else {
                    $_SESSION['form_errors'] = ['general' => 'Gagal memperbarui event di database.'];
                }
            } else {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['old_event_input'] = $inputData; // Simpan input yang gagal validasi
            }
            // Redirect kembali ke form edit untuk menampilkan error dan input lama
            header("Location: index.php?controller=Event&action=edit&id=" . $id);
            exit;
        }

        $form_errors = $_SESSION['form_errors'] ?? [];
        $old_input_session = $_SESSION['old_event_input'] ?? null;


        $display_data = $event;
        if ($old_input_session) {
            $display_data['name'] = $old_input_session['name'] ?? $event['name'];
            $display_data['date'] = $old_input_session['date'] ?? $event['date'];
            $display_data['location'] = $old_input_session['location'] ?? $event['location'];
        }

        unset($_SESSION['form_errors']); 
        unset($_SESSION['old_event_input']); 

        $event = $display_data; 
        require __DIR__ . '/../views/events/edit.php';
    }


    public function delete() {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id !== false) {
            $delete_result = $this->eventModel->delete($id); 

            // DEBUGGING:
            var_dump($delete_result); 

            if ($delete_result) {
                 $_SESSION['success_message_global'] = "Event berhasil dihapus.";
            } else {
                 $_SESSION['error_message_global'] = "Gagal menghapus event. ";
            }
        } else {
             $_SESSION['error_message_global'] = "ID Event tidak valid untuk dihapus. (Debug: ID tidak valid)";
        }
    } else {
         $_SESSION['error_message_global'] = "ID Event tidak disediakan untuk dihapus. (Debug: ID tidak ada)";
    }
    header("Location: index.php?controller=Event&action=index");
    exit;
}

    public function addParticipant()
    {
        $event_id = $_GET['id'] ?? null;
        if (!$event_id) {
            header("Location: index.php?controller=Event&action=index");
            exit;
        }
        $event_id = filter_var($event_id, FILTER_VALIDATE_INT);
        if ($event_id === false) {
            $_SESSION['error_message_global'] = "ID Event tidak valid untuk menambah peserta.";
            header("Location: index.php?controller=Event&action=index");
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? null;
            $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

            if ($user_id && $user_id !== false) {
                // Cek apakah event dan user ada
                if (!$this->eventModel->find($event_id) || !$this->userModel->find($user_id)) {
                    $_SESSION['error_message_event_show'] = "Event atau User tidak ditemukan.";
                    header("Location: index.php?controller=Event&action=show&id=$event_id");
                    exit;
                }

                // Cek apakah user sudah menjadi peserta
                $current_participants = $this->participantModel->getParticipantByEvent($event_id);
                $is_already_participant = false;
                foreach ($current_participants as $p) {
                    if ($p['id'] == $user_id) {
                        $is_already_participant = true;
                        break;
                    }
                }

                if ($is_already_participant) {
                    $_SESSION['error_message_event_show'] = "User tersebut sudah menjadi peserta event ini.";
                } else {
                    if ($this->participantModel->addParticipant($event_id, $user_id)) {
                        $_SESSION['success_message_event_show'] = "Peserta berhasil ditambahkan.";
                    } else {
                        $_SESSION['error_message_event_show'] = "Gagal menambahkan peserta.";
                    }
                }
            } else {
                $_SESSION['error_message_event_show'] = "User ID tidak valid.";
            }
        }
        header("Location: index.php?controller=Event&action=show&id=$event_id");
        exit;
    }
}
