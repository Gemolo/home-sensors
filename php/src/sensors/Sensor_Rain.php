<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Rain extends BaseSensor {

    public static function typeName(): string {
        return "Rain Sensor";
    }

    public static function typeId(): string {
        return 'rain';
    }
}