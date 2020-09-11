<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use \HomeSensors\Settings;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class DeleteUser extends Page {

    private $userId, $backPage;

    public function __construct(int $userId, string $backPage) {
        $this->userId = $userId;
        $this->backPage = $backPage;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $stmt = $pdo->prepare("SELECT username, email, id FROM User WHERE id = ?");
        $stmt->bindValue(1, $this->userId);
        $stmt->execute();

        $row = $stmt->fetch();

        if (is_array($row)) {
            TwigUtils::renderPage('confirm-user-deletion.twig', "Confirm user deletion", [
                'user'      => $row,
                'back_page' => $this->backPage,
            ]);
        } else {
            header('Location: ' . $this->backPage);
        }
    }
}