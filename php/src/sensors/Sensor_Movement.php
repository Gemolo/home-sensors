<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Movement extends BaseSensor {

    public static function name(): string {
        return "Movement Sensor";
    }

    protected static function type(): string {
        return 'movement';
    }
}