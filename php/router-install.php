<?php
declare(strict_types=1);

use HomeSensors\pages\DoInstall;
use HomeSensors\pages\Install;

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$klein->respond('GET', '/', new Install());
$klein->respond('POST', '/install', new DoInstall());

$klein->dispatch();
