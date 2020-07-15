<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\SensorParam;

final class Sensor_Distance extends Sensor {


    public static function params(): array {
        $gpioCheck = function ($value) {
            return is_int($value) && $value > 0;
        };
        return [
            new SensorParam('transmitter_gpio', 'Transmitter GPIO port', 'INT UNSIGNED NOT NULL', 'number', $gpioCheck),
            new SensorParam('receiver_gpio', 'Receiver GPIO port', 'INT UNSIGNED NOT NULL', 'number', $gpioCheck),
        ];
    }

    public static function typeName(): string {
        return "Distance Sensor";
    }


    public static function typeId(): string {
        return 'distance';
    }

    public function getSensorData(): ?string {
        if ($curl = curl_init("gpio:5000/distance?transmitter=" . $this->getParamData('transmitter_gpio') . '&receiver=' . $this->getParamData('receiver_gpio'))) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            if ($response === false) {
                return null;
            } else {
                return $response;
            }
        }
        return null;
    }
}