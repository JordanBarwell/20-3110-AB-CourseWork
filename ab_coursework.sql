DROP DATABASE IF EXISTS 'abcoursework_db';

CREATE DATABASE 'abcoursework_db' COLLATE utf8_unicode_ci;

CREATE USER IF NOT EXISTS 'abcoursework_user'@'localhost' IDENTIFIED BY 'abcoursework_pass';
GRANT SELECT,INSERT ON abcoursework_db.* TO 'abcoursework_user'@localhost;

USE abcoursework_db;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` int(100) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(256) NOT NULL ,
	`password` varchar(256) NOT NULL ,
	`phone` int(13) NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
	`id` int(100) unsigned NOT NULL AUTO_INCREMENT,
	`source` int(13) NOT NULL,
	`destination` int(13) NOT NULL,
	`received` timestamp NOT NULL,
	`bearer` varchar(4) NOT NULL,
	`ref` int(100) NOT NULL,
	`message` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
