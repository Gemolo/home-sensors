<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\PushUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class BeamsAuth extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $user = LoginUtilis::login();
        $userId = $user['id'];
        $auth = PushUtils::getClient()->generateToken((string)$userId);
        echo json_encode($auth);
    }
}