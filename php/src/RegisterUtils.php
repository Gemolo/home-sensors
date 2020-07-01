<?php
declare(strict_types=1);

namespace HomeSensors;


use Firebase\JWT\JWT;
use PDO;

class RegisterUtils {
    const JWTKey = "provatest";

    public static function generateToken(int $hours, bool $admin): string {
        return JWT::encode([
            "iat"   => time(),
            "exp"   => time() + $hours * 60 * 60,
            "admin" => $admin,
        ], self::JWTKey, 'HS256');
    }

    public static function verifyToken(string $token): array {
        $payload = JWT::decode($token, self::JWTKey, ["HS256"]);
        return (array)$payload;
    }

    public static function registerUser(string $username, string $email, string $password, ?string $name, bool $isAdmin) {
        if ($name !== null && trim($name) === '') {
            $name = null;
        }


        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare('INSERT INTO User(username, email, password, name, isAdmin) VALUES (?,?,?,?,?+0)');

        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindValue(4, $name);
        $stmt->bindValue(5, $isAdmin ? 1 : 0);
        $stmt->execute();
    }

}