<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/', function () {
    require_once './src/index.php';
});
$klein->respond('GET', '/api/sensors', function () {
    require_once './src/api/sensors.php';
});

$klein->dispatch();
