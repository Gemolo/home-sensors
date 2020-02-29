<?php

/*
 * SENSORI:
 * -Movimento
 * -Gas
 * -Fuoco
 * -Luce
 * -Distanza
 * -Pioggia
 *
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../twig');
$twig = new Environment($loader);
echo $twig->render('index.twig', []);
