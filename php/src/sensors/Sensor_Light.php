<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Light extends BaseSensor {

    public static function typeName(): string {
        return "Light Sensor";
    }

    public static function typeId(): string {
        return 'light';
    }
}