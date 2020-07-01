<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Fire extends BaseSensor {

    public static function name(): string {
        return "Fire Sensor";
    }
    
    protected static function type(): string {
        return 'fire';
    }
}