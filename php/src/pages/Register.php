<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\sensors\Sensor;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Register extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        TwigUtils::renderPage("register.twig", "Register", [
            'token' => $_GET['token'] ?? ''
        ]);
    }
}