-- Adminer 4.8.1 MySQL 10.9.3-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for each user',
  `name` varchar(50) NOT NULL COMMENT 'Username',
  `password` char(60) NOT NULL COMMENT 'Password hash (bcrypt hashed)',
  `token` char(64) NOT NULL COMMENT 'Token used for cookie authentication',
  `ip` char(15) DEFAULT NULL COMMENT 'Latest IP address of user',
  `joined` int(10) unsigned NOT NULL COMMENT 'Timestamp when user joined',
  `lastview` int(10) unsigned DEFAULT NULL COMMENT 'Timestamp when user last viewed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2022-10-08 14:49:05
