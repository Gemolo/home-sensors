<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsSensorsEdit extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.name, IF(sc.id IS NULL, 0, 1) AS belongs
            FROM Category c
            LEFT OUTER JOIN SensorCategory sc ON sc.category = c.id AND sc.sensor = ?
            ORDER BY c.name
        ");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        TwigUtils::renderPage("settings-sensors-edit-categories.twig", "Edit Categories", [
            "categories" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    }
}