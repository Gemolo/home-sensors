<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Gas extends BaseSensor {

    public static function name(): string {
        return "Gas Sensor (MQ)";
    }
    
    protected static function type(): string {
        return 'gas';
    }
}