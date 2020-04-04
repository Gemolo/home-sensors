<?php
declare(strict_types=1);

use HomeSensors\DatabaseUtils;

require_once __DIR__ . '/vendor/autoload.php';

if(DatabaseUtils::isDbOK()) {
    if(\HomeSensors\LoginUtilis::isLogged()){
        require_once 'router-std.php';
    } else {
        require_once 'router-login.php';
    }
} else {
    require_once 'router-install.php';
}