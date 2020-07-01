<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\DatabaseUtils;
use PDO;

abstract class Sensor {

    private $id;
    private $name;
    private $categories;

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

    public function getCategories() {
        if ($this->categories === null) {
            $pdo = DatabaseUtils::connect();
            $stmt = $pdo->prepare("
                SELECT c.id, c.name
                FROM SensorCategory sc
                INNER JOIN Category c ON c.id = sc.category
                WHERE sc.sensor = ?
            ");
            $stmt->bindValue(1, $this->id);
            $stmt->execute();
            $this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->categories;
    }

    public function getTwigData(bool $withCategories = true): array {
        return array_merge($this->twigData(), [
            "name"       => $this->name,
            "id"         => $this->id,
            "type"       => static::type(),
            "type_name"  => static::name(),
            "categories" => $withCategories ? $this->getCategories() : null,
        ]);
    }

    public abstract function getSensorData(): ?string;

    protected abstract function twigData(): array;

    public abstract static function fromRow(array $row): Sensor;

    protected abstract static function rowData(array $data): array;

    public abstract static function createInputs(): array;

    public abstract static function name(): string;

    protected static abstract function type(): string;

    protected static abstract function tableColumns(): array;

    public const CLASSES = [
        Sensor_Distance::class,
        Sensor_Fire::class,
        Sensor_Gas::class,
        Sensor_Light::class,
        Sensor_Movement::class,
        Sensor_Rain::class,
    ];

    //region STRUCTURAL
    protected static function createBase(string $name): int {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            INSERT INTO Sensor(name) VALUES (?)
        ");
        $stmt->bindValue(1, $name);
        $stmt->execute();
        return (int)$pdo->lastInsertId();
    }

    public static function createTable(): void {
        $pdo = DatabaseUtils::connect();
        $table = static::tableName();
        $type = static::type();
        $columns = implode(',', static::tableColumns());
        $stmt = $pdo->prepare($q="
           CREATE TABLE IF NOT EXISTS `HomeSensors`.`$table` (
              `id` INT UNSIGNED NOT NULL,
              PRIMARY KEY (`id`),
              CONSTRAINT `id_$type`
                FOREIGN KEY (`id`)
                REFERENCES `HomeSensors`.`Sensor` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT,              
              $columns
            )
            ENGINE = InnoDB
        ");

        $stmt->execute();
    }

    protected static function tableName(): string {
        $class = static::class;
        $i = strripos($class, '\\');
        return substr($class, $i + 1);
    }

    protected static function tableExists(): bool {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT * 
            FROM information_schema.tables
            WHERE table_schema = 'HomeSensors' AND table_name = ?;
        ");
        $stmt->bindValue(1, static::tableName());
        $stmt->execute();
        return $stmt->fetch() !== false;
    }

    //endregion

    public static function create(string $name, array $data) {
        $rowData = static::rowData($data);
        $columns = implode(',', array_keys($rowData));

        $id = Sensor::createBase($name);

        $pdo = DatabaseUtils::connect();
        $table = static::tableName();
        $stmt = $pdo->prepare("INSERT INTO $table(id,$columns) VALUES (?" . str_repeat(',?', \count($rowData)) . ")");

        $stmt->bindValue(1, $id);
        $i = 2;
        foreach ($rowData as $d) {
            $stmt->bindValue($i++, $d);
        }
        $stmt->execute();
    }

    protected static function loadSensors(): array {
        $pdo = DatabaseUtils::connect();

        if (static::tableExists()) {
            $table = static::tableName();
            $stmt = $pdo->prepare("
                SELECT *
                FROM Sensor s
                INNER JOIN $table s2 ON s.id = s2.id 
            ");
            $ret = [];
            $stmt->execute();
            foreach ($stmt->fetchAll() as $row) {
                $ret[$row['id']] = static::fromRow($row);
            }

            return $ret;
        } else {
            return [];
        }
    }

    public static function types(): array {
        $ret = [];
        foreach (self::CLASSES as $class) {
            $ret[] = [
                "inputs" => $class::createInputs(),
                "name"   => $class::name(),
            ];
        }
        return $ret;
    }

    public static function getClassForTypeName(string $name): ?string {
        foreach (self::CLASSES as $class) {
            if ($class::name() === $name) {
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
            if ($sensors !== []) {
                $ret = $ret + $sensors;
            }
        }
        return $ret;
    }

}