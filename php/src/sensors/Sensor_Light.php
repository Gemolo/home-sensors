<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


class Sensor_Light extends BaseSensor {

    public static function name(): string {
        return "Light Sensor";
    }


    protected static function url(): string {
        return 'light';
    }
}