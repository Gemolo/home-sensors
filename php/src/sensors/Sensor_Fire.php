<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Fire extends BaseSensor {

    public static function typeName(): string {
        return "Fire Sensor";
    }

    public static function typeId(): string {
        return 'fire';
    }
}