<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


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

    public static function fromRow(array $row): Sensor {
        return new static(
            $row['id'],
            $row['name'],
            $row['gpio'],
        );
    }

    protected static abstract function url() : string;
}