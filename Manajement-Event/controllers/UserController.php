<?php 
require_once __DIR__ . '/AuthController.php'; 
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct()
    {
        AuthController::protectPage(); // Panggil method proteksi
        $this->userModel = new User();
    }

    public function index() {
    $search = $_GET['search'] ?? null;
    $users = $this->userModel->all($search);
    require __DIR__ . '/../views/users/index.php';
}

    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=User&action=index");
            exit;
        }
        $user = $this->userModel->find($id);
        require __DIR__ . '/../views/users/show.php';
    }

     public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ];
            $this->userModel->create($data);
            header("Location: index.php?controller=User&action=index");
            exit;
        }
        require __DIR__ . '/../views/users/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=User&action=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ];
            $this->userModel->update($id, $data);
            header("Location: index.php?controller=User&action=index");
            exit;
        }

        $user = $this->userModel->find($id);
        require __DIR__ . '/../views/users/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->delete($id);
        }
        header("Location: index.php?controller=User&action=index");
        exit;
    }
    
}

?>