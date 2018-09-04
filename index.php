<?php

//error_reporting(0);

function __autoload($class) {
    require __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
}

$url = $_GET['url'];

if (empty($url)) {
    $action = 'index';
    $params = [];
} else {
    $path = explode('/', $url);
    $action = array_shift($path);
    $params = $path;
}

$Clients = new \App\Controllers\Clients();
$View = new \App\View();

if (!empty($action) && method_exists($Clients, $action)) {
    try {
        $Clients->{$action}(...$params);
    } catch (Exception $e) {
        $Clients->error($e->getMessage());
    }
} else {
    header('Location: /');
}