<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\sensors\Sensor;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsSensorsAdd extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'type' => 'required',
            'name' => 'required',
            'data' => 'required|array',
        ]);
    }

    protected function exec() {

        $type = $_POST["type"];
        $name = $_POST["name"];
        $data = $_POST["data"];

        $sensorClass = Sensor::getClassForTypeName($type);

        $sensorClass::create($name, $data);

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/sensors');
    }
}