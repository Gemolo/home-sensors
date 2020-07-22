<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\InvalidEmailException;
use HomeSensors\InvalidPasswordException;
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
        try {
            UserUtils::editUserFromPostData(LoginUtilis::login()['id'], LoginUtilis::login()['isAdmin'] === 1);
        } catch (InvalidEmailException $e) {
            TwigUtils::renderError('Edit user', 'Invalid email: ' . $e->getMessage());
            return;
        } catch (InvalidPasswordException $e) {
            TwigUtils::renderError('Edit user', 'Invalid password: ' . $e->getMessage());
            return;
        }

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/profile');

    }
}