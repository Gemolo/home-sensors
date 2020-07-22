<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsSensorsEditPost extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $name = $_POST["name"] ?? '';
        $categories = $_POST["categories"] ?? [];
        $enabled = $_POST["enabled"] ?? [];

        try {
            $pdo->beginTransaction();
            //Edit name
            if (trim($name) !== '') {
                $stmt = $pdo->prepare("
                    UPDATE Sensor
                    SET name = ?
                    WHERE id = ?
                ");
                $stmt->bindValue(1, $name);
                $stmt->bindValue(2, $this->id);
                $stmt->execute();
            }

            //Edit categories
            $stmtInsert = $pdo->prepare("
                INSERT IGNORE INTO SensorCategory(sensor, category)
                VALUES (?, ?)
            ");
            $stmtInsert->bindValue(1, $this->id);

            $stmtDelete = $pdo->prepare("
                DELETE FROM SensorCategory
                WHERE sensor = ? AND  category = ?
            ");
            $stmtDelete->bindValue(1, $this->id);

            foreach ($categories as $category) {
                if (isset($enabled[(int)$category])) {
                    $stmtInsert->bindValue(2, (int)$category);
                    $stmtInsert->execute();
                } else {
                    $stmtDelete->bindValue(2, (int)$category);
                    $stmtDelete->execute();
                }
            }
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/sensors');

    }
}