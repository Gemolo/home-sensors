<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsCategoriesEditPost extends Page {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $name = $_POST["name"] ?? '';
        $users = $_POST["users"] ?? [];
        $enabled = $_POST["enabled"] ?? [];

        try {
            $pdo->beginTransaction();


            //Edit name
            if (trim($name) !== '') {
                $stmt = $pdo->prepare("
                    UPDATE Category
                    SET name = ?
                    WHERE id = ?
                ");
                $stmt->bindValue(1, $name);
                $stmt->bindValue(2, $this->id);
                $stmt->execute();
            }

            //Edit users
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
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
        header("Location: " . \HomeSensors\Settings::urlRoot() . '/settings/categories');

    }
}