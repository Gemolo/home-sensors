<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\Page;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Login extends Page{

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        TwigUtils::renderPage("login.twig", "Login");
    }
}