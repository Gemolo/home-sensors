<?php
declare(strict_types=1);

namespace HomeSensors;


use Firebase\JWT\JWT;
use PDO;

class LoginUtilis {
    const JWTKey = "provatest";
    private static $loggedUser = null;
    private static $loginCheck = false;

    public static function generateToken(string $username, string $passwd): ?string {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("SELECT id, password, passwordIteration from User where username = ?");
        $stmt->bindValue(1, $username);
        $stmt->execute();
        $row = $stmt->fetch();
        if (is_array($row)) {
            $pass = $row["password"];
            if (password_verify($passwd, $pass)) {
                $payload = [
                    "iat"     => time(),
                    "user_id" => $row['id'],
                    "pw_iter" => $row["passwordIteration"],
                ];
                return JWT::encode($payload, self::JWTKey, 'HS256');
            }
        }
        return null;
    }

    public static function login(): ?array {
        if (!self::$loginCheck) {
            $token = $_COOKIE["loginToken"] ?? null;
            if ($token !== null) {
                $payload = self::verifyToken($token);
                if ($payload !== null) {
                    [$userId, $iteration] = $payload;
                    $pdo = DatabaseUtils::connect();
                    $stmt = $pdo->prepare("SELECT * from User where id = ? AND passwordIteration=?");
                    $stmt->bindValue(1, $userId);
                    $stmt->bindValue(2, $iteration);
                    $stmt->execute();
                    $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                    self::$loggedUser = $fetch === false ? null : $fetch;
                }
            }
            self::$loginCheck = true;
        }
        return self::$loggedUser;
    }

    public static function isLogged(): bool {
        return self::login() !== null;
    }

    public static function isAdmin(): bool {
        self::login();
        return self::$loggedUser !== null && self::$loggedUser["isAdmin"] === 1;
    }

    private static function verifyToken(string $token): ?array {
        try {
            $payload = JWT::decode($token, self::JWTKey, ["HS256"]);

            return [$payload->user_id, $payload->pw_iter];
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function logout(bool $logoutAllDevices): void {
        if ($logoutAllDevices) {
            $pdo = DatabaseUtils::connect();
            $user = LoginUtilis::login();

            $stmt = $pdo->prepare("UPDATE User SET passwordIteration=passwordIteration+1 WHERE id=?");
            $stmt->bindValue(1, $user['id']);
            $stmt->execute();
        }
        setcookie("loginToken", '', 0, "", Settings::domain(), true);
    }

}