<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\Page;
use HomeSensors\PushUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Notify extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        PushUtils::getClient()->publishToInterests(
            ["sensori"],
            [
                "fcm" => [
                    "notification" => [
                        "title" => "Prova",
                        "body" => "Notifica di prova"
                    ],
                ],
            ],
            );
        echo "notifica inviata";
    }
}