<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\api\Sensors;
use HomeSensors\DatabaseUtils;
use HomeSensors\SensorParam;

abstract class BaseSensor extends Sensor {

    public static function params(): array {
        return [
            new SensorParam('gpio', 'GPIO port', 'INT UNSIGNED NOT NULL', 'number', function($value) {
                return is_int($value) && $value > 0;
            })
        ];
    }

    public function getSensorData(): ?string {
        $url = static::url();
        if ($curl = curl_init("gpio:5000/$url?gpio=" . $this->getParamData('gpio'))) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            return $response;
        }
        return null;
    }

    protected static function url() : string {
        return static::typeId();
    }


}