<?php
declare(strict_types=1);

namespace HomeSensors;


class Settings {

    public static function domain(): string {
        $domain = getenv('HOMESENSORS_DOMAIN');
        if ($domain === false) {
            throw new \LogicException('Please define HOMESENSORS_DOMAIN environment variable');
        } else {
            return $domain;
        }
    }

    public static function urlRoot(): string {
        $root = getenv('HOMESENSORS_URL_ROOT');
        if ($root === false) {
            return '/';
        } else {
            return $root;
        }
    }
}