<?php
declare(strict_types=1);

namespace HomeSensors;


use Firebase\JWT\JWT;

class LoginUtilis {
    const JWTKey = "provatest";
    private static $logUser = null;

    public static function generateToken(string $username, string $passwd): ?string {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
        SELECT password from User where username = ?
    ");
        $stmt->bindValue(1, $username);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row !== null) {
            $pass = $row["password"];
            if (password_verify($passwd, $pass)) {
                $payload = [
                    "iat"  => time(),
                    "user" => $username,
                ];
                return JWT::encode($payload, self::JWTKey);
            }
        }
        return null;
    }

    public static function login(): bool {
        $token = $_COOKIE["logintoken"] ?? null;
        //self::verifyToken($token);
    }

    private static function verifyToken(string $token): ?string {
        try {
            $payload = JWT::decode($token, self::JWTKey);
            return $payload["user"];
        } catch (\Exception $e) {
            return null;
        }

    }

}