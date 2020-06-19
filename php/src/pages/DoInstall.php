<?php
declare(strict_types=1);

namespace HomeSensors\pages;

use HomeSensors\DatabaseUtils;
use HomeSensors\Page;
use HomeSensors\Settings;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

class DoInstall extends Page {

    protected function validation(Validator $validator): Validation {
        return $validator->make($_POST, [
            'name'             => 'required',
            'username'         => 'required|min:4',
            'email'            => 'required|email',
            'password'         => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
    }

    protected function exec() {
        $pdo = DatabaseUtils::connect();
        $pdo->exec('
            DROP SCHEMA `HomeSensors`;
            CREATE SCHEMA IF NOT EXISTS `HomeSensors` DEFAULT CHARACTER SET utf8mb4;
            USE `HomeSensors`;
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`User` (
              `id` INT UNSIGNED NULL AUTO_INCREMENT,
              `name` VARCHAR(45) NULL,
              `username` VARCHAR(45) NOT NULL,
              `email` VARCHAR(60) NOT NULL,
              `password` VARCHAR(255) NOT NULL,
              `passwordIteration` INT UNSIGNED NOT NULL DEFAULT 0,
              `isAdmin` BIT(1) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `username_UNIQUE` (`username`),
              UNIQUE INDEX `email_UNIQUE` (`email`)
            )
            ENGINE = InnoDB;
          
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`Category` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(45) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `nome_UNIQUE` (`name`)
            )
            ENGINE = InnoDB;
            
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`Sensor` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(45) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name`)  
            )
            ENGINE = InnoDB;
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`Sensor_Light` (
              `id` INT UNSIGNED NOT NULL,
              gpio INT UNSIGNED NOT NULL,
              PRIMARY KEY (`id`),
              CONSTRAINT `id_light`
                FOREIGN KEY (`id`)
                REFERENCES `HomeSensors`.`Sensor` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
            )
            ENGINE = InnoDB;
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`Sensor_Movement` (
              `id` INT UNSIGNED NOT NULL,
              gpio INT UNSIGNED NOT NULL,
              PRIMARY KEY (`id`),
              CONSTRAINT `id_movement`
                FOREIGN KEY (`id`)
                REFERENCES `HomeSensors`.`Sensor` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
            )
            ENGINE = InnoDB;    
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`UsersCategory` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `user` INT UNSIGNED NULL,
              `category` INT UNSIGNED NULL,
              PRIMARY KEY (`id`),
              INDEX `user_idx` (`user`),
              INDEX `category_idx` (`category`),
              CONSTRAINT `userCategory_user`
                FOREIGN KEY (`user`)
                REFERENCES `HomeSensors`.`User` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT,
              CONSTRAINT `userCategory_category`
                FOREIGN KEY (`category`)
                REFERENCES `HomeSensors`.`Category` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
            )
            ENGINE = InnoDB;
            
            
            CREATE TABLE IF NOT EXISTS `HomeSensors`.`SensorCategory` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `category` INT UNSIGNED NULL,
              `sensor` INT UNSIGNED NULL,
              PRIMARY KEY (`id`),
              INDEX `category_idx` (`category`),
              INDEX `sensor_idx` (`sensor`),
              CONSTRAINT `sensorCategory_category`
                FOREIGN KEY (`category`)
                REFERENCES `HomeSensors`.`Category` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT,
              CONSTRAINT `sensorCategory_sensor`
                FOREIGN KEY (`sensor`)
                REFERENCES `HomeSensors`.`Sensor` (`id`)
                ON DELETE CASCADE
                ON UPDATE RESTRICT
            )
            ENGINE = InnoDB;
        ');
        $stmt = $pdo->prepare('INSERT INTO User(name, username, email, password, isAdmin) VALUES (?,?,?,?,1)');
        $name = $_POST['name'];
        if(trim($name) === '') {
            $name = null;
        }
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $_POST['username']);
        $stmt->bindValue(3, $_POST['email']);
        $stmt->bindValue(4, password_hash($_POST['password'], PASSWORD_DEFAULT));
        $stmt->execute();

        header("Location: " . Settings::urlRoot() . '/login');
    }
}