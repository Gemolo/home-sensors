<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/login', new \HomeSensors\Login());
$klein->respond('POST', '/login', new \HomeSensors\api\DoLogin());

$klein->dispatch();
