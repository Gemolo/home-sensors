<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategoriesAdd extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'name' => 'required',
        ]);
    }

    protected function exec() {

        $name = $_POST["name"];

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("INSERT INTO Category(name) VALUES (?)");
        $stmt->bindValue(1, $name);
        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                http_response_code(409);
                TwigUtils::renderError('Add category', 'A category with the same name already exists!');
                return;
            } else {
                throw $e;
            }
        }
        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/categories');
    }
}