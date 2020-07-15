<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use HomeSensors\UserUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class ProfilePost extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        UserUtils::editUserFromPostData(LoginUtilis::login()['id'], LoginUtilis::login()['isAdmin'] === 1);

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/profile');

    }
}