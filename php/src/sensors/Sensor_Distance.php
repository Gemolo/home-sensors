<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


final class Sensor_Distance extends Sensor {

    private $transmitterGPIO, $receiverGPIO;

    protected function __construct(int $id, string $name, int $transmitterGPIO, int $receiverGPIO) {
        parent::__construct($id, $name);
        $this->transmitterGPIO = $transmitterGPIO;
        $this->receiverGPIO = $receiverGPIO;
    }

    public static function name(): string {
        return "Distance Sensor";
    }

    public static function createInputs(): array {
        return [
            'transmitter_gpio' => 'number',
            'receiver_gpio'    => 'number',
        ];
    }

    protected static function type(): string {
        return 'distance';
    }

    protected static function tableColumns(): array {
        return [
            'transmitter_gpio INT UNSIGNED NOT NULL',
            'receiver_gpio INT UNSIGNED NOT NULL',
        ];
    }

    public function getSensorData(): ?string {
        if ($curl = curl_init("gpio:5000/distance?transmitter=" . $this->transmitterGPIO . '&receiver=' . $this->receiverGPIO)) {
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

    protected function twigData(): array {
        return [
            'transmitter_gpio' => $this->transmitterGPIO,
            'receiver_gpio'    => $this->receiverGPIO,
        ];
    }

    public static function fromRow(array $row): Sensor {
        return new Sensor_Distance(
            $row['id'],
            $row['name'],
            $row['transmitter_gpio'],
            $row['receiver_gpio']
        );
    }

    protected static function rowData(array $data): array {
        $transmitter = (int)$data['transmitter_gpio'];
        $receiver = (int)$data['receiver_gpio'];
        if ($transmitter <= 0) {
            throw new \LogicException('Transmitter GPIO <= 0');
        } elseif ($receiver <= 0) {
            throw new \LogicException('Receiver GPIO <= 0');
        } else {
            return [
                'transmitter_gpio' => $transmitter,
                'receiver_gpio'    => $receiver,
            ];
        }
    }
}