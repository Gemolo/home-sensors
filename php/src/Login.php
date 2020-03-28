<?php
declare(strict_types=1);

namespace HomeSensors;


use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Login extends Page{

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {

    }
}