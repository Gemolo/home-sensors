<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


class Sensor_Light extends BaseSensor {

    protected static function url(): string {
        return 'light';
    }
}