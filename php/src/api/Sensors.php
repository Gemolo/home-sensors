<?php
declare(strict_types=1);

namespace HomeSensors\api;


use HomeSensors\Page;
use HomeSensors\sensors\Sensor;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Sensors extends Page {

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $sensors = Sensor::sensors();
        $ret = [];
        foreach ($sensors as $sensor) {
            $ret[$sensor->getId()] = $sensor->getSensorData();
        }
        header('Content-Type: application/json');
        echo json_encode((object)$ret);
    }
}