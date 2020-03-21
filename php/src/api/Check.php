<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\Page;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Check extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        echo "ciaociao";
//        PushUtils::getClient()->publishToInterests(
//            ["sensori"],
//            [
//                "fcm" => [
//                    "notification" => [
//                        "title" => "Prova",
//                        "body" => "Notifica di prova"
//                    ],
//                ],
//            ],
//            );
//        echo "notifica inviata";
    }
}