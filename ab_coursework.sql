DROP DATABASE IF EXISTS 'abcoursework_db';

CREATE DATABASE 'abcoursework_db' COLLATE utf8_unicode_ci;

CREATE USER IF NOT EXISTS 'abcoursework_user'@'localhost' IDENTIFIED BY 'abcoursework_pass';
GRANT SELECT,INSERT ON abcoursework_db.* TO 'abcoursework_user'@localhost;

USE abcoursework_db;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(256) NOT NULL,
	`password` VARCHAR(256) NOT NULL,
	`email` VARCHAR(300) NOT NULL,
	`phone` INT(13) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`source` INT(13) NOT NULL,
	`destination` INT(13) NOT NULL,
	`received` TIMESTAMP NOT NULL,
	`bearer` VARCHAR(4) NOT NULL,
	`ref` INT NOT NULL,
	`message` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
