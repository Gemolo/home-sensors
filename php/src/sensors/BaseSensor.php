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

    public static function create(string $name, array $data) {
        $class = static::class;
        $i = strripos($class, '\\');
        $table = substr($class, $i + 1);
        $id = Sensor::createBase($name);

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            INSERT INTO $table(id, gpio) VALUES (?, ?)
        ");
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $data["gpio"]);
        $stmt->execute();
    }

    public static function createInputs(): array {
        return [
            "gpio" => "number"
        ];
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