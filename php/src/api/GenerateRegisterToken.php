<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\PushUtils;
use HomeSensors\RegisterUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class GenerateRegisterToken extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'hours' => 'required|numeric',
            'will_be_admin' => 'required',
        ]);
    }

    protected function exec() {
        $hours = min(max(1, (int) $_POST['hours']), 168);

        $token = RegisterUtils::generateToken($hours, $_POST['will_be_admin'] === true);

        echo json_encode($token);
    }
}