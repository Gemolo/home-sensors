<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\api\Sensors;
use HomeSensors\DatabaseUtils;

abstract class BaseSensor extends Sensor {

    private $gpio;

    protected function __construct(int $id, string $name, int $gpio) {
        parent::__construct($id, $name);
        $this->gpio = $gpio;
    }

    public function getSensorData(): ?string {
        $url = static::url();
        if ($curl = curl_init("gpio:5000/$url?gpio=" . $this->gpio)) {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            return $response;
        }
        return null;
    }

    protected function twigData(): array {
        return [
            "gpio" => $this->gpio,
        ];
    }

    public static function createInputs(): array {
        return [
            "gpio" => "number"
        ];
    }

    protected static function rowData(array $data): array {
        $gpio = (int)$data['gpio'];
        if($gpio > 0) {
            return [
                'gpio' => $gpio
            ];
        } else {
            throw new \LogicException('GPIO <= 0');
        }
    }


    public static function fromRow(array $row): Sensor {
        return new static(
            $row['id'],
            $row['name'],
            $row['gpio'],
        );
    }

    protected static function url() : string {
        return static::type();
    }

    protected static function tableColumns(): array {
        return [
            'gpio INT UNSIGNED NOT NULL'
        ];
    }


}