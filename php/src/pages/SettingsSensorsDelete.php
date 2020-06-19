<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsSensorsDelete extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'id' => 'required|numeric',
        ]);
    }

    protected function exec() {

        $id = $_POST["id"];

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            DELETE FROM Sensor WHERE id = ?;
        ");
        $stmt->bindValue(1, $id);
        $stmt->execute();

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/sensors');
    }
}