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

class Home extends Page{

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $sensors = Sensor::sensors();

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.name, GROUP_CONCAT(sc.sensor)
            FROM UserCategory uc
            INNER JOIN Category c ON c.id = uc.category
            INNER JOIN SensorCategory sc ON sc.category = c.id
            WHERE uc.user = ?
            GROUP BY c.id
        ");
        //TODO
//        $stmt->bindValue(1, LoginUtilis::)

        TwigUtils::renderPage("login.twig", "Login");
    }
}