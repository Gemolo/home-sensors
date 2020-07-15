<?php
declare(strict_types=1);

namespace HomeSensors;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigUtils {

    public static function renderPage(string $page, string $title, array $data = []): void {
        $loader = new FilesystemLoader(__DIR__ . '/../twig');
        $twig = new Environment($loader);
        $user = LoginUtilis::login();
        echo $twig->render($page, array_merge($data, [
            'title'       => $title,
            'root'        => Settings::urlRoot(),
            'domain'      => Settings::domain(),
            'logged_user' => $user,
        ]));
    }

    public static function renderError(string $title, $message) {
        TwigUtils::renderPage("error.twig", $title, [
            "error_message" => $message,
        ]);
    }
}