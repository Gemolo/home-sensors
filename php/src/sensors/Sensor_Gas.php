<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Gas extends BaseSensor {

    public static function typeName(): string {
        return "Gas Sensor (MQ)";
    }

    public static function typeId(): string {
        return 'gas';
    }
}