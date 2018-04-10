<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};


 

// MYSQLI database library
$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $conn = mysqli_connect($settings['host'],$settings['user'],$settings['pass'],$settings['dbname']);
    return $conn;
};


// MYSQLI database library
$container['connect'] = function ($c) {
    $dbname = $_SESSION['dbname'];
    $settings = $c->get('settings')['db'];
    $conn = mysqli_connect($settings['host'],$settings['user'],$settings['pass'],$dbname);
    return $conn;
};
