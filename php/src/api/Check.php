<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\PushUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Check extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {

        if ($curl = curl_init('gpio:5000')) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $obj = (json_decode($response));

        }
        //https://pusher.com/docs/beams/concepts/authenticated-users
        if ($obj->fuoco === true) {
            self::sendPush('Fuoco', 'Stai andando a fuoco');
        }
        if ($obj->gas === true) {
            self::sendPush('Gas', 'C\'è del gas in casa');
        }
        if ($obj->pioggia === true) {
            self::sendPush('Pioggia', 'Sta piovendo');
        }
    }

    public static function sendPush(string $title, string $message) {
        PushUtils::getClient()->publishToUsers(
            ['1'], //TODO selzionare utenti
            [
                "fcm" => [
                    "notification" => [
                        "title" => $title,
                        "body"  => $message,
                    ],
                ],
            ]
        );
    }
}