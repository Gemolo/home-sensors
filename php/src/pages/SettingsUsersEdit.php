<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use PDO;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class SettingsUsersEdit extends Page {

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
            SELECT *
            FROM User u
            WHERE u.id=?
        ");
        $stmt->bindValue(1, $this->id);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($users === []) {
            http_response_code(404);
        }else {
            TwigUtils::renderPage("settings-users-edit.twig", "Edit User", [
                "user" => reset($users),
            ]);
        }
    }
}