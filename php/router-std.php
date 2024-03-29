<?php
declare(strict_types=1);

use HomeSensors\LoginUtilis;

require_once __DIR__ . '/vendor/autoload.php';


$klein = new \Klein\Klein();


$klein->respond('GET', '/api/sensors', new \HomeSensors\api\Sensors());
//$klein->respond('GET', '/api/notify', new \HomeSensors\api\Notify());
$klein->respond('GET', '/api/check', new \HomeSensors\api\Check());
$klein->respond('GET', '/api/beams-auth', new \HomeSensors\api\BeamsAuth());

$klein->respond('GET', '/register', new \HomeSensors\pages\Register());
$klein->respond('POST', '/register', new \HomeSensors\pages\RegisterPost());
$klein->respond('GET', '/', new \HomeSensors\pages\Home());
$klein->respond('GET', '/logout', new \HomeSensors\pages\Logout());
$klein->respond('POST', '/logout', new \HomeSensors\pages\LogoutPost());

$klein->respond('GET', '/profile', new \HomeSensors\pages\Profile());
$klein->respond('POST', '/profile', new \HomeSensors\pages\ProfilePost());

$klein->respond('GET', '/profile/delete', new \HomeSensors\pages\DeleteUser(LoginUtilis::login()['id'], \HomeSensors\Settings::urlRoot() . '/profile'));
$klein->respond('POST', '/profile/delete', new \HomeSensors\pages\DoDeleteUser(LoginUtilis::login()['id'], \HomeSensors\Settings::urlRoot()));



if (LoginUtilis::isAdmin()) {
    $klein->respond('POST', '/api/generate_register_token', new \HomeSensors\api\GenerateRegisterToken());
    $klein->respond('POST', '/api/test_notification', new \HomeSensors\api\TestNotification());

    //Categorie
    $klein->respond('GET', '/settings/categories', new \HomeSensors\pages\SettingsCategories());
    $klein->respond('POST', '/settings/categories/add', new \HomeSensors\pages\SettingsCategoriesAdd());
    $klein->respond('POST', '/settings/categories/delete', new \HomeSensors\pages\SettingsCategoriesDelete());
    $klein->respond('GET', '/settings/categories/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsCategoriesEdit($id);
        $page();
    });
    $klein->respond('POST', '/settings/categories/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsCategoriesEditPost($id);
        $page();
    });

    //Sensori
    $klein->respond('GET', '/settings/sensors', new \HomeSensors\pages\SettingsSensors());
    $klein->respond('POST', '/settings/sensors/add', new \HomeSensors\pages\SettingsSensorsAdd());
    $klein->respond('POST', '/settings/sensors/delete', new \HomeSensors\pages\SettingsSensorsDelete());
    $klein->respond('GET', '/settings/sensors/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsSensorsEdit($id);
        $page();
    });
    $klein->respond('POST', '/settings/sensors/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsSensorsEditPost($id);
        $page();
    });

    //Utenti
    $klein->respond('GET', '/settings/users', new \HomeSensors\pages\SettingsUsers());
    $klein->respond('GET', '/settings/users/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsUsersEdit($id);
        $page();
    });
    $klein->respond('POST', '/settings/users/[i:id]/edit', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\SettingsUsersEditPost($id);
        $page();
    });

    $klein->respond('GET', '/settings/users/[i:id]/delete', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\DeleteUser($id, \HomeSensors\Settings::urlRoot() . '/settings/users/' . $id . '/edit');
        $page();
    });
    $klein->respond('POST', '/settings/users/[i:id]/delete', function ($request) {
        $id = (int)$request->param("id");
        $page = new \HomeSensors\pages\DoDeleteUser($id, \HomeSensors\Settings::urlRoot() . '/settings/users');
        $page();
    });
}

$klein->dispatch();
