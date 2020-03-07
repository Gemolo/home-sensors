<?php
declare(strict_types=1);

namespace HomeSensors;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigUtils {

    public static function renderPage(string $page, string $title, array $data = []): void {
        $loader = new FilesystemLoader(__DIR__ . '/../twig');
        $twig = new Environment($loader);
        echo $twig->render($page, array_merge($data, [
            'title' => $title,
            'root'  => Settings::urlRoot(),
        ]));
    }
}