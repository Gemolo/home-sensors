<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\InvalidSensorParamException;
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

        /** @var Sensor $sensorClass */
        $sensorClass = Sensor::getClassForType($type);
        if ($sensorClass === null) {
            http_response_code(400);
            return;
        }

        $sensorClass::createTable();
        try {
            $sensorClass::create($name, $data);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                TwigUtils::renderError('Add sensor', 'A sensor with the same name already exists');
                return;
            } else {
                throw $e;
            }
        } catch (InvalidSensorParamException $e) {
            http_response_code(400);
            TwigUtils::renderError('Add sensor', $e->getMessage());
            return;
        }

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/sensors');
    }
}