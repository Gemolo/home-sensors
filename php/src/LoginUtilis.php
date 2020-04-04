<?php
declare(strict_types=1);

namespace HomeSensors;


use Firebase\JWT\JWT;

class LoginUtilis {
    const JWTKey = "provatest";
    private static $logUser = null;
    private static $loginCheck = false;
    private static $isAdmin = null;

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
                return JWT::encode($payload, self::JWTKey, 'HS256');
            }
        }
        return null;
    }

    public static function login(): ?string {
        if(!self::$loginCheck){
            $token = $_COOKIE["loginToken"] ?? null;
            if($token !== null) {
                self::$logUser = self::verifyToken($token);
                if(self::$logUser!==null){
                    
                }
            }
            self::$loginCheck = true;
        }
        return self::$logUser;
    }

    public static function isLogged(): bool {
        return self::login() !== null;
    }

    public static function isAdmin(): bool {
        self::login();
        return self::isAdmin() === true;
    }

    private static function verifyToken(string $token): ?string {
        try {
            $payload = JWT::decode($token, self::JWTKey,["HS256"]);
            return $payload->user;
        } catch (\Exception $e) {
            return null;
        }

    }

}