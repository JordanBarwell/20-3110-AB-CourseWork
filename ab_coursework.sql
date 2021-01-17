DROP DATABASE IF EXISTS abcoursework_db;

CREATE DATABASE abcoursework_db COLLATE utf8_unicode_ci;

CREATE USER IF NOT EXISTS 'abcoursework_user'@'localhost' IDENTIFIED BY 'abcoursework_pass';
GRANT SELECT,INSERT ON abcoursework_db.* TO 'abcoursework_user'@localhost;

USE abcoursework_db;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(26) NOT NULL,
	`password` VARCHAR(70) NOT NULL,
	`email` VARCHAR(300) NOT NULL,
	`phone` BIGINT(15) NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT UC_User UNIQUE (`username`, `email`, `phone`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`source` BIGINT(15) NOT NULL,
	`received` DATETIME NOT NULL,
	`bearer` VARCHAR(4) NOT NULL,
	`ref` BIGINT NOT NULL,
	`temperature` INT(3),
	`fan` VARCHAR(7) NOT NULL,
	`keypad` INT(4),
	`switchOne` VARCHAR(3) NOT NULL,
	`switchTwo` VARCHAR(3) NOT NULL,
	`switchThree` VARCHAR(3) NOT NULL,
	`switchFour` VARCHAR(3) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `users` (`username`, `password`, `email`, `phone`) VALUES ('admin', '$2y$14$55DAPtAKbA1oP/yfrS1Ys.u.R0YOyKnzL53X3mBVLyLnA8ZTjVu.m', 'P2430436@my365.dmu.ac.uk', '447752046618');

INSERT INTO `messages` (`source`, `received`, `bearer`, `ref`, `temperature`, `fan`, `keypad`, `switchOne`, `switchTwo`, `switchThree`, `switchFour`) VALUES (447752046618, NOW(), 'SMS', 0, NULL, 'Forward', 2345, 'On', 'On', 'Off', 'Off');
INSERT INTO `messages` (`source`, `received`, `bearer`, `ref`, `temperature`, `fan`, `keypad`, `switchOne`, `switchTwo`, `switchThree`, `switchFour`) VALUES (447752046618, NOW(), 'SMS', 0, -20, 'Reverse', 1234, 'Off', 'Off', 'Off', 'Off');
INSERT INTO `messages` (`source`, `received`, `bearer`, `ref`, `temperature`, `fan`, `keypad`, `switchOne`, `switchTwo`, `switchThree`, `switchFour`) VALUES (447752046618, NOW(), 'SMS', 0, 0, 'N/A', 8765, 'On', 'On', 'Off', 'On');
