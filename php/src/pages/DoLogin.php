<?php
declare(strict_types=1);

namespace HomeSensors\pages;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\Settings;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class DoLogin extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'username'         => 'required|min:4',
            'password'         => 'required|min:6'
        ]);
    }

    protected function exec() {
      $token = LoginUtilis::generateToken($_POST["username"], $_POST["password"]);
      if($token === null){
          TwigUtils::renderPage("error.twig", "Authenticator Error",[
              "errorMessage"=>"Wrong username or password"
          ]);
      } else {
          setcookie("loginToken", $token, 0, "",Settings::domain(), true);
          header("Location: " . Settings::urlRoot());
      }
    }
}