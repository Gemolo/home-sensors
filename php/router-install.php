<?php
declare(strict_types=1);

use HomeSensors\pages\DoInstall;

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/', function () {
    require_once './src/install.php';
});
$klein->respond('POST', '/install', new DoInstall());

$klein->dispatch();
