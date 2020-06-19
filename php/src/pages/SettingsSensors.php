<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\api\Sensors;
use HomeSensors\Page;
use HomeSensors\sensors\Sensor;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsSensors extends Page{

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $sensors = [];
        foreach (Sensor::sensors() as $sensor){
            $sensors[] = $sensor->getTwigData();
        }
        TwigUtils::renderPage("settings-sensors.twig", "Sensors Settings", [
            "sensorsTypes" => Sensor::types(),
            "sensors" => $sensors,
        ]);
    }
}