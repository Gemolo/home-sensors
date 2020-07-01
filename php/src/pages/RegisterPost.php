<?php
declare(strict_types=1);

namespace HomeSensors\pages;


use Firebase\JWT\ExpiredException;
use HomeSensors\DatabaseUtils;
use HomeSensors\LoginUtilis;
use HomeSensors\Page;
use HomeSensors\RegisterUtils;
use HomeSensors\sensors\Sensor;
use HomeSensors\TwigUtils;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class RegisterPost extends Page {

    protected function validation(Validator $validator): ?Validation {
        return $validator->make($_POST, [
            'name'             => 'required',
            'username'         => 'required|min:4',
            'email'            => 'required|email',
            'password'         => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
    }

    protected function exec() {
        $token = $_POST['token'] ?? null;
        if ($token === null) {
            http_response_code(403);
            exit();
        } else {
            try {
                $payload = RegisterUtils::verifyToken($token);
                RegisterUtils::registerUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['name'] ?? null, $payload['admin'] ?? false);
                header('Location: ' . \HomeSensors\Settings::urlRoot() . '/login');
            } catch (ExpiredException $e) {
                TwigUtils::renderPage('error.twig', 'Error', ['errorMessage' => 'Token has expired']);
            } catch (\Exception $e) {
                TwigUtils::renderPage('error.twig', 'Error', ['errorMessage' => 'Token is invalid']);
            }
        }
    }
}