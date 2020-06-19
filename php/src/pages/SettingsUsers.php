<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsUsers extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT *
            FROM User
        ");
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

        TwigUtils::renderPage("settings-users.twig", "Users Settings", [
            "users" => $arr,
        ]);
    }
}