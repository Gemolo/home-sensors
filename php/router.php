<?php
declare(strict_types=1);

use HomeSensors\DatabaseUtils;

require_once __DIR__ . '/vendor/autoload.php';

if(DatabaseUtils::isDbOK()) {
    require_once 'router-std.php';
} else {
    require_once 'router-install.php';
}