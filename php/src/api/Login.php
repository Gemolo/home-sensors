<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\PushUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Login extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'username'         => 'required|min:4',
            'password'         => 'required|min:6'
        ]);
    }

    protected function exec() {
      $token = LoginUtilis::generateToken($_POST["username"], $_POST["password"]);
      if($token != null){
          http_response_code(401);
      } else {
          echo $token;
      }
    }
}