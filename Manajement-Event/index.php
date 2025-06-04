<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['controller'] ?? 'Auth');
$action = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['action'] ?? 'login');        

if (isset($_SESSION['admin_id'])) {
    if ($controllerName === 'Auth' && ($action === 'login' || $action === 'register')) {
        $controllerName = 'User'; 
        $action = 'index';
    } elseif (empty($_GET['controller'])) { 
        $controllerName = 'User';
        $action = 'index';
    }
} else {
    if ($controllerName !== 'Auth') {
        if (!($controllerName === 'Auth' && $action === 'login')) {
             $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        }
        $controllerName = 'Auth';
        $action = 'login';
    }
}

$controllerClass = ucfirst(strtolower($controllerName)) . 'Controller';
$controllerFile = __DIR__ . "/controllers/" . $controllerClass . ".php";

if (!file_exists($controllerFile)) {
    if ($controllerClass === 'AuthController') {
        die("Controller inti <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan. Sistem autentikasi tidak bisa berjalan.");
    }
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php?controller=Auth&action=login&error=controller_not_found");
        exit;
    }
    die("Controller <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan.");
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    die("Class <strong>" . htmlspecialchars($controllerClass) . "</strong> tidak ditemukan dalam file " . htmlspecialchars($controllerFile) . ".");
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    if (method_exists($controller, 'index')) {
        $action = 'index';
    } else {
        die("Action <strong>" . htmlspecialchars($action) . "</strong> tidak ditemukan di controller <strong>" . htmlspecialchars($controllerClass) . "</strong>.");
    }
}

$controller->$action();
?>