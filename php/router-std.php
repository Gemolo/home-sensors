<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();


$klein->respond('GET', '/api/sensors', function () {
    require_once './src/api/sensors.php';
});
//$klein->respond('GET', '/api/notify', new \HomeSensors\api\Notify());
$klein->respond('GET', '/api/check', new \HomeSensors\api\Check());


$klein->respond('GET', '/', function () {
    require_once './src/index.php';
});

if(true) {
    $klein->respond('GET', '/settings', new \HomeSensors\pages\Settings());
    $klein->respond('GET', '/settings/sensors', new \HomeSensors\pages\SettingsSensors());
}

$klein->dispatch();
