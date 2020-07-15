<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Movement extends BaseSensor {

    public static function typeName(): string {
        return "Movement Sensor";
    }

    public static function typeId(): string {
        return 'movement';
    }
}