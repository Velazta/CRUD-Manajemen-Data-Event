<?php
$controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['controller'] ?? 'User');
$action = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['action'] ?? 'index');

$controllerClass = $controllerName . 'Controller';
$controllerFile = __DIR__ . "/controllers/$controllerClass.php";

if (!file_exists($controllerFile)) {
    die("Controller <strong>$controllerClass</strong> tidak ditemukan.");
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    die("Class <strong>$controllerClass</strong> tidak ditemukan.");
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    die("Action <strong>$action</strong> tidak ditemukan di controller <strong>$controllerClass</strong>.");
    exit;
}

// Panggil method action
$controller->$action();
?>