<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\InvalidEmailException;
use HomeSensors\InvalidPasswordException;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use HomeSensors\UserUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsUsersEditPost extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        try {
            UserUtils::editUserFromPostData($this->id, true);
        } catch (InvalidEmailException $e) {
            TwigUtils::renderError('Edit user', 'Invalid email: ' . $e->getMessage());
            return;
        } catch (InvalidPasswordException $e) {
            TwigUtils::renderError('Edit user', 'Invalid password: ' . $e->getMessage());
            return;
        }

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/users');

    }
}