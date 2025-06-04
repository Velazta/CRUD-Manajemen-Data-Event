<?php
// Mulai session di sini karena akan digunakan oleh banyak bagian
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil dan sanitasi input controller dan action dari query string
$controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['controller'] ?? 'Auth'); // Default ke AuthController
$action = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['action'] ?? 'login');         // Default ke action login

// Jika pengguna sudah login dan mencoba mengakses halaman login/register AuthController,
// atau jika tidak ada controller yang ditentukan (default ke Auth), arahkan ke User index.
if (isset($_SESSION['admin_id'])) {
    if ($controllerName === 'Auth' && ($action === 'login' || $action === 'register')) {
        $controllerName = 'User'; // Atau controller dashboard admin Anda
        $action = 'index';
    } elseif (empty($_GET['controller'])) { // Jika tidak ada controller spesifik & sudah login
        $controllerName = 'User';
        $action = 'index';
    }
} else {
    // Jika belum login dan mencoba mengakses selain Auth, paksa ke Auth login
    if ($controllerName !== 'Auth') {
        // Simpan halaman yang ingin diakses agar bisa redirect setelah login
        // Hindari loop redirect jika sudah di Auth/login
        if (!($controllerName === 'Auth' && $action === 'login')) {
             $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        }
        $controllerName = 'Auth';
        $action = 'login';
    }
}


// Buat nama class controller dan path file controller
$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller'; // ucfirst untuk konsistensi nama file
$controllerFile = __DIR__ . "/controllers/" . $controllerClass . ".php";

// Cek apakah file controller tersedia
if (!file_exists($controllerFile)) {
    // Jika controller Auth tidak ditemukan, ini masalah besar.
    if ($controllerClass === 'AuthController') {
        die("Controller inti <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan. Sistem autentikasi tidak bisa berjalan.");
    }
    // Untuk controller lain, idealnya tampilkan halaman 404 atau log error
    // Untuk saat ini, kita bisa arahkan ke halaman login default jika controller tidak ada & belum login
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php?controller=Auth&action=login&error=controller_not_found");
        exit;
    }
    die("Controller <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan.");
}

// Load file controller
require_once $controllerFile;

// Cek apakah class controller ada
if (!class_exists($controllerClass)) {
    die("Class <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan dalam file " . htmlspecialchars($controllerFile) . ".");
}

// Buat instance controller
$controller = new $controllerClass();

// Cek apakah method action tersedia di controller
if (!method_exists($controller, $action)) {
    // Jika action tidak ada, mungkin arahkan ke action default controller (misal 'index')
    // atau tampilkan error 404.
    if (method_exists($controller, 'index')) {
        $action = 'index'; // Fallback ke method index jika action spesifik tidak ada
    } else {
        die("Action <strong>" . htmlspecialchars($action) . "</strong> tidak ditemukan di controller <strong>" . htmlspecialchars($controllerClass) . "</strong>.");
    }
}

// Panggil method action
$controller->$action();
?>