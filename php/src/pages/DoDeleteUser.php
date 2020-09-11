<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class DoDeleteUser extends Page {

    private $userId, $backPage;

    public function __construct(int $userId, string $backPage) {
        $this->userId = $userId;
        $this->backPage = $backPage;
    }

    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        $loggedUser = LoginUtilis::login();
        if ($this->userId !== 1 && (($loggedUser !== null && $loggedUser['id'] === $this->userId) || LoginUtilis::isAdmin())) {
            $pdo = DatabaseUtils::connect();
            $stmt = $pdo->prepare('DELETE FROM User WHERE id=?');
            $stmt->bindValue(1, $this->userId);
            $stmt->execute();

            header('Location: ' . $this->backPage);
        } else {
            http_response_code(403);
            TwigUtils::renderError("Forbidden", "You cannot remove this user");
        }
    }
}