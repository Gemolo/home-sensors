<?php
declare(strict_types=1);

namespace HomeSensors;


class Settings {

    public static function domain() : string {
        return 'kevingemolo.ddns.net';
    }

    public static function urlRoot() : string {
        return '/sensori';
    }
}