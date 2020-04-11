<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\DatabaseUtils;

abstract class Sensor {

    private $id;
    private $name;

    protected function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public abstract function getSensorData(): ?string;

    public abstract static function fromRow(array $row): Sensor;

    protected static function loadSensors(): array {
        $class = static::class;
        $i = strripos($class, '\\');
        $table = substr($class, $i + 1);
        $stmt = DatabaseUtils::connect()->prepare("
            SELECT *
            from Sensor s
            inner join $table s2 on s.id = s2.id 
        ");
        $ret = [];
        $stmt->execute();
        foreach ($stmt->fetchAll() as $row) {
            $ret[] = static::fromRow($row);
        }
        return $ret;
    }

    /**
     * @return Sensor[]
     */
    public static function sensors(): array {
        $classes = [
            Sensor_Light::class,
        ];
        $ret = [];
        foreach ($classes as $class) {
            $sensors = $class::loadSensors();
            if ($sensors !== []){
                array_push($ret, ...$sensors);
            }
        }
        return $ret;
    }

}