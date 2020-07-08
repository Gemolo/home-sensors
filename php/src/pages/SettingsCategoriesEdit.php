<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategoriesEdit extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, IF(uc.id IS NULL, 0, 1) AS belongs
            FROM User u
            LEFT OUTER JOIN UsersCategory uc ON uc.user = u.id AND uc.category = ?
            ORDER BY u.username
        ");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT name
            FROM Category
            WHERE id = ?
        ");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

        TwigUtils::renderPage("settings-categories-edit.twig", "Edit Users", [
            "name"  => $name,
            "users" => $users,
        ]);
    }
}