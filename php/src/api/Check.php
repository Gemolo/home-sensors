<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\PushUtils;
use HomeSensors\sensors\Sensor;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Check extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $triggered = [];
        foreach (Sensor::sensors() as $sensor) {
            if ($sensor->isTriggered()) {
                $triggered[$sensor->getId()] = [
                    'name'  => $sensor->getName(),
                    'users' => self::sendPush($sensor),
                ];
            }
        }
        header("Content-Type: application/json");
        http_response_code(200);
        echo json_encode($triggered);
    }

    public static function sendPush(Sensor $sensor): int {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare('
            SELECT DISTINCT uc.user AS id
            FROM SensorCategory sc
            INNER JOIN UsersCategory uc ON sc.category = uc.category
            WHERE sc.sensor=?

            UNION

            SELECT u.id
            FROM User u
            WHERE u.isAdmin=1+0
        ');
        $stmt->bindValue(1, $sensor->getId());
        $stmt->execute();
        $ids = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $item) {
            $ids[] = (string)$item['id'];
        }

        if (\count($ids) > 0) {
            PushUtils::getClient()->publishToUsers(
                $ids,
                [
                    "fcm" => [
                        "notification" => [
                            "title" => 'Sensor is firing',
                            "body"  => $sensor->getName() . " is firing! ID: " . $sensor->getId() . ", type: " . $sensor::typeName(),
                        ],
                    ],
                ]
            );
        }
        return \count($ids);
    }
}