<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsUsersEditPost extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? null;
        $confirmPassword = $_POST["confirm_password"] ?? null;
        $isAdmin = ($_POST["is_admin"] ?? null) === "on";


        $stmt = $pdo->prepare("UPDATE User SET name=?, email=? WHERE id=?");
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $this->id);
        $stmt->execute();

        if ($password !== null && strlen($password) > 6 && $password === $confirmPassword) {
            $stmt = $pdo->prepare("UPDATE User SET password=?, passwordIteration=passwordIteration+1 WHERE id=?");
            $stmt->bindValue(1, password_hash($password, PASSWORD_DEFAULT));
            $stmt->bindValue(2, $this->id);
            $stmt->execute();
        }

        if($this->id !== 1) {
            $stmt = $pdo->prepare("UPDATE User SET isAdmin=? WHERE id=?");
            $stmt->bindValue(1, $isAdmin ? 1 : 0);
            $stmt->bindValue(2, $this->id);
            $stmt->execute();
        }

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/users');

    }
}