<?php
declare(strict_types=1);

namespace HomeSensors;

class DatabaseUtils {

    private static $pdo;

    public static function connect(): \PDO {
        if (self::$pdo === null) {
            $dsn = 'mysql:dbname=HomeSensors;host=db;port=3306;charset=utf8';
            self::$pdo = new \PDO($dsn, "root", "sensori");

            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        }
        return self::$pdo;
    }

    public static function isDbOK() : bool {
        $stmt = self::connect()->prepare("SHOW TABLES LIKE 'User'");
        $stmt->execute();
        return \count($stmt->fetchAll()) > 0;
    }

}