<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\sensors\Sensor;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Home extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $allSensors = Sensor::sensors();

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.name, GROUP_CONCAT(sc.sensor) AS sensors
            FROM UsersCategory uc
            INNER JOIN Category c ON c.id = uc.category
            INNER JOIN SensorCategory sc ON sc.category = c.id
            WHERE uc.user = ?
            GROUP BY c.id
        ");
        $stmt->bindValue(1, LoginUtilis::login()['id']);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($categories as &$cat) {
            $sensorsIds = explode(',', $cat['sensors']);
            $sensors = array_map(function (int $id) use ($allSensors) {
                return $allSensors[$id]->getTwigData(false);
            }, $sensorsIds);
            $cat['sensors'] = $sensors;
        }


        TwigUtils::renderPage("home.twig", "Home", [
            'categories' => $categories,
        ]);
    }
}