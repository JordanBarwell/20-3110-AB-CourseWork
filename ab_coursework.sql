DROP DATABASE IF EXISTS 'abcoursework_db';

CREATE TABLE IF NOT EXISTS 'abcousework_db';

GRANT SELECT,INSERT ON abcoursework_db.* TO 'abcoursework_user'@localhost IDENTIFIED BY 'abcoursework_pass';


USE ab_coursework.db;

CREATE TABLE IF NOT EXISTS 'users'(
	'key' int(100) unsigned NOT NULL AUTO_INCREMENT,
	'username' varchar(256) NOT NULL ,
	'password' varchar(256) NOT NULL ,
	'phone' int(13) NOT NULL ,
	PRIMARY KEY ('key')
	)
	
CREATE TABLE IF NOT EXISTS 'messages'(
	'key' int(100) unsigned NOT NULL AUTO_INCREMENT,
	'source' int(13) NOT NULL,
	'destination' int(13) NOT NULL,
	'recieved' timestamp NOT NULL,
	'bearer' varchar(4) NOT NULL,
	'ref' int(100) NOT NULL,
	'message' text NOT NULL,
	PRIMARY KEY ('key')
	)
	
	
	
