<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategoriesEditUsersPost extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $users = $_POST["users"] ?? [];
        $enabled = $_POST["enabled"] ?? [];
        $stmtInsert = $pdo->prepare("
            INSERT IGNORE INTO UsersCategory(category, user)
            VALUES (?, ?)
        ");
        $stmtInsert->bindValue(1, $this->id);

        $stmtDelete = $pdo->prepare("
            DELETE FROM UsersCategory
            WHERE category = ? AND user = ?
        ");
        $stmtDelete->bindValue(1, $this->id);

        foreach ($users as $user) {
            if (isset($enabled[(int)$user])) {
                $stmtInsert->bindValue(2, (int)$user);
                $stmtInsert->execute();
            } else {
                $stmtDelete->bindValue(2, (int)$user);
                $stmtDelete->execute();
            }
        }

        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/categories');

    }
}