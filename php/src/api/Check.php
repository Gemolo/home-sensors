<?php
declare(strict_types=1);

namespace HomeSensors\api;

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
        if ($obj->fuoco === true) {
            PushUtils::getClient()->publishToInterests(
                ["sensori"],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Fuoco",
                            "body"  => "Stai andando a fuoco",
                        ],
                    ],
                ],
                );
            echo "notifica inviata";
        }
        if ($obj->gas === true) {
            PushUtils::getClient()->publishToInterests(
                ["sensori"],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Gas",
                            "body"  => "C'è del gas in casa",
                        ],
                    ],
                ],
                );
            echo "notifica inviata";
        }
        if ($obj->pioggia === true) {
            PushUtils::getClient()->publishToInterests(
                ["sensori"],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Pioggia",
                            "body"  => "Sta piovendo",
                        ],
                    ],
                ],
                );
            echo "notifica inviata";
        }
    }
}