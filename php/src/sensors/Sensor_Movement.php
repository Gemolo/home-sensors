<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


class Sensor_Movement extends BaseSensor {

    public static function name(): string {
        return "Movement Sensor";
    }

    public static function createInputs(): array {
        return [
            "gpio" => "number",
        ];
    }


    protected static function url(): string {
        return 'movement';
    }
}