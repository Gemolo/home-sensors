<?php
declare(strict_types=1);

namespace HomeSensors\pages;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class LogoutPost extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $logoutAllDevices = $_POST['logoutAllDevices'] ?? '';
        LoginUtilis::logout(strcasecmp($logoutAllDevices, 'on') === 0);

        header('Location: ' . \HomeSensors\Settings::urlRoot());
    }
}