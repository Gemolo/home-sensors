<?php
declare(strict_types=1);

namespace HomeSensors\api;

use HomeSensors\Page;
use HomeSensors\PushUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class TestNotification extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'user_id' => 'required|numeric',
        ]);
    }

    protected function exec() {
        PushUtils::getClient()->publishToUsers(
            [(string)$_POST['user_id']],
            [
                "fcm" => [
                    "notification" => [
                        "title" => 'Test notification',
                        "body"  => 'Test message',
                    ],
                ],
            ]
        );
    }
}