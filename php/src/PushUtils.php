<?php
declare(strict_types=1);

namespace HomeSensors;

use Pusher\PushNotifications\PushNotifications;

class PushUtils{
    public static function getClient() : PushNotifications{
        $beamsClient = new PushNotifications(array(
            "instanceId" => "b46cc080-8e2c-4a45-98ab-8a7291b3d31d",
            "secretKey" => "46A2B8E50BA9AA3407BC7260E619E38BCB9F237A5FAC6214E55EE0932E3215B4",
        ));
        return $beamsClient;
    }
}
