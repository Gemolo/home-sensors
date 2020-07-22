<?php
declare(strict_types=1);

namespace HomeSensors\pages;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\Settings;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class LoginPost extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }

    protected function exec() {
        $token = LoginUtilis::generateToken($_POST["username"], $_POST["password"]);
        if ($token === null) {
            http_response_code(401);
            TwigUtils::renderError("Authenticator Error", "Wrong username or password");
        } else {
            setcookie("loginToken", $token, 0, "", Settings::domain(), true);
            header("Location: " . Settings::urlRoot());
        }
    }
}