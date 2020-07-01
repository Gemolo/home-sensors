<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategoriesEditUsers extends Page {

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
        TwigUtils::renderPage("settings-categories-edit-users.twig", "Edit Users", [
            "users" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    }
}