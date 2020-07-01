<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategories extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.name, COUNT(sc.id) AS sensorsCount
            FROM Category c
            LEFT OUTER JOIN SensorCategory sc ON c.id = sc.category
            GROUP BY c.id
            ORDER BY c.name
        ");
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($arr as &$cat) {
            $stmt = $pdo->prepare("
                SELECT u.*
                FROM User u
                INNER JOIN UsersCategory uc ON uc.user = u.id
                WHERE uc.category = ?
            ");
            $stmt->bindValue(1, $cat['id']);
            $stmt->execute();
            $cat['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        TwigUtils::renderPage("settings-categories.twig", "Categories Settings", [
            "categories" => $arr,
        ]);
    }
}