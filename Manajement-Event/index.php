<?php
// Ambil dan sanitasi input controller dan action dari query string
$controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['controller'] ?? 'User');
$action = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['action'] ?? 'index');

// Buat nama class controller dan path file controller
$controllerClass = $controllerName . 'Controller';
$controllerFile = __DIR__ . "/controllers/$controllerClass.php";

// Cek apakah file controller tersedia
if (!file_exists($controllerFile)) {
    die("Controller <strong>$controllerClass</strong> tidak ditemukan.");
    exit;
}

// Load file controller
require_once $controllerFile;

// Cek apakah class controller ada
if (!class_exists($controllerClass)) {
    die("Class <strong>$controllerClass</strong> tidak ditemukan.");
    exit;
}

// Buat instance controller
$controller = new $controllerClass();

// Cek apakah method action tersedia di controller
if (!method_exists($controller, $action)) {
    die("Action <strong>$action</strong> tidak ditemukan di controller <strong>$controllerClass</strong>.");
    exit;
}

// Panggil method action
$controller->$action();
?>