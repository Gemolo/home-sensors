<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Rain extends BaseSensor {

    public static function name(): string {
        return "Rain Sensor";
    }
    
    protected static function type(): string {
        return 'rain';
    }
}