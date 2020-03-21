<?php
declare(strict_types=1);

namespace HomeSensors;

use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

abstract class Page {

    protected abstract function validation(Validator $validator): ?Validation;

    protected abstract function exec();

    public function __invoke() {
        $validator = new Validator();
        $validation = $this->validation($validator);
        if ($validation !== null) {
            $validation->validate();
            if ($validation->fails()) {
                $errors = $validation->errors();
                http_response_code(400);
                echo "<pre>";
                print_r($errors->firstOfAll());
                echo "</pre>";
            } else {
                $this->exec();
            }
        } else {
            $this->exec();
        }
    }
}