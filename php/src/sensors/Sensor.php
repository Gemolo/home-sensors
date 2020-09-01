<?php
declare(strict_types=1);

namespace HomeSensors\sensors;


use HomeSensors\DatabaseUtils;
use HomeSensors\InvalidSensorParamException;
use HomeSensors\SensorParam;
use PDO;

abstract class Sensor {

    private $id;
    private $name;
    private $categories;
    private $paramData;

    protected function __construct(int $id, string $name, array $paramData) {
        $this->id = $id;
        $this->name = $name;
        $this->paramData = $paramData;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getParamData(string $key) {
        return $this->paramData[$key] ?? null;
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
        return array_merge($this->paramData, [
            "name"       => $this->name,
            "id"         => $this->id,
            "type"       => static::typeId(),
            "type_name"  => static::typeName(),
            "categories" => $withCategories ? $this->getCategories() : null,
        ]);
    }

    public abstract function getSensorData(): ?string;

    public abstract function isTriggered(): ?bool;

    public abstract static function typeName(): string;

    public static abstract function typeId(): string;

    /**
     * @return SensorParam[]
     */
    public static abstract function params(): array;

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
        $type = static::typeId();
        $columns = [];
        foreach (static::params() as $param) {
            $columns[] = $param->id() . ' ' . $param->columnType();
        }
        $columns = implode(',', $columns);
        $stmt = $pdo->prepare("
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

    /**
     * @param array $data
     * @throws InvalidSensorParamException
     */
    public static function create(string $name, array $data) {
        $rowData = [];
        foreach (static::params() as $param) {
            $value = $data[$param->id()];
            $param->checkValue($value);
            $rowData[$param->id()] = $value;
        }

        $columns = implode(',', array_keys($rowData));

        $pdo = DatabaseUtils::connect();
        $table = static::tableName();

        try {
            $pdo->beginTransaction();
            $id = Sensor::createBase($name);

            $stmt = $pdo->prepare("INSERT INTO $table(id,$columns) VALUES (?" . str_repeat(',?', \count($rowData)) . ")");
            $stmt->bindValue(1, $id);
            $i = 2;
            foreach ($rowData as $d) {
                $stmt->bindValue($i++, $d);
            }
            $stmt->execute();
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
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
                $paramData = [];
                foreach (static::params() as $param) {
                    $paramData[$param->id()] = $row[$param->id()];
                }
                $ret[$row['id']] = new static($row['id'], $row['name'], $paramData);
            }

            return $ret;
        } else {
            return [];
        }
    }

    public static function types(): array {
        $ret = [];
        foreach (self::CLASSES as $class) {
            $inputs = [];
            foreach ($class::params() as $param) {
                /** @var SensorParam $param */
                $inputs[] = [
                    'id'   => $param->id(),
                    'name' => $param->name(),
                    'type' => $param->inputType(),
                ];
            }
            $ret[] = [
                "id"     => $class::typeId(),
                "name"   => $class::typeName(),
                "inputs" => $inputs,
            ];
        }
        return $ret;
    }

    public static function getClassForType(string $id): ?string {
        foreach (self::CLASSES as $class) {
            if ($class::typeId() === $id) {
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

    public static function gpioCheckCallable(): callable {
        return function ($value) {
            if ($value === null || $value === '') {
                return 'must be present';
            } elseif (!is_numeric($value)) {
                return 'must be an integer';
            } else {
                $value = (int)$value;
                return $value > 0 ? null : 'must be greater than 0';
            }
        };
    }
}