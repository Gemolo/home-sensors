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

\HomeSensors\DatabaseUtils::connect();

\HomeSensors\TwigUtils::renderPage('index.twig', "Home");