<?php
declare(strict_types=1);

namespace HomeSensors\pages;

use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\RegisterUtils;
use HomeSensors\Settings;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class Install extends Page {


    protected function validation(Validator $validator): ?Validation {
        return null;
    }

    protected function exec() {
        TwigUtils::renderPage('install.twig', 'Install');
    }
}