<?php
declare(strict_types=1);

namespace HomeSensors;


final class SensorParam {

    private $id; //DB column name, <input> name, twig data name
    private $name;
    private $columnType;
    private $inputType;
    private $checkValue;

    public function __construct(string $id, string $name, string $columnType, string $inputType, callable $checkValue) {
        $this->id = $id;
        $this->name = $name;
        $this->columnType = $columnType;
        $this->inputType = $inputType;
        $this->checkValue = $checkValue;
    }

    public function id(): string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function columnType(): string {
        return $this->columnType;
    }

    public function inputType(): string {
        return $this->inputType;
    }

    /**
     * @throws InvalidSensorParamException
     */
    public function checkValue($value): void {
        $msg = ($this->checkValue)($value);
        if ($msg !== null) {
            throw new InvalidSensorParamException('Invalid value for param "' . $this->name . '": ' . $msg);
        }
    }
}