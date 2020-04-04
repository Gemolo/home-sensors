<?php
declare(strict_types=1);

use HomeSensors\Settings;

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/login', new \HomeSensors\pages\Login());
$klein->respond('POST', '/login', new \HomeSensors\pages\DoLogin());
$klein->respond('GET', '/', function(){
    header("Location:" . Settings::urlRoot() . "/login");
    exit();
});


$klein->dispatch();
