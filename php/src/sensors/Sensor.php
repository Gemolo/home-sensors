<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\DatabaseUtils;
use PDO;

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

    public function getTwigData() : array {

        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT c.id, c.name
            FROM SensorCategory sc
            INNER JOIN Category c ON c.id = sc.category
            WHERE sc.sensor = ?
        ");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();


        return array_merge($this->twigData(), [
            "name" => $this->name,
            "id" => $this->id,
            "type" => static::name(),
            "categories" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    }

    public abstract function getSensorData(): ?string;

    protected abstract function twigData(): array ;

    public abstract static function fromRow(array $row): Sensor;

    public abstract static function create(string $name, array $data);

    public abstract static function createInputs() : array;

    public abstract static function name() : string;

    public const CLASSES = [
        Sensor_Light::class,
        Sensor_Movement::class,
    ];

    protected static function createBase(string $name) : int{
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            INSERT INTO Sensor(name) VALUES (?)
        ");
        $stmt->bindValue(1, $name);
        $stmt->execute();
        return (int)$pdo->lastInsertId();
    }

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

    public static function types() : array {
        $ret = [];
        foreach (self::CLASSES as $class){
            $ret[] = [
                "inputs" => $class::createInputs(),
                "name" => $class::name(),
            ];
        }
        return $ret;
    }

    public static function getClassForTypeName(string $name) : ?string {
        foreach (self::CLASSES as $class){
            if($class::name() === $name){
                return $class;
            }
        }
        return null;
    }

    /**
     * @return Sensor[]
     */
    public static function sensors(): array {
        $ret = [];
        foreach (self::CLASSES as $class) {
            $sensors = $class::loadSensors();
            if ($sensors !== []){
                array_push($ret, ...$sensors);
            }
        }
        return $ret;
    }

}