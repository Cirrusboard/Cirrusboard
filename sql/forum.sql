-- Adminer 4.8.1 MySQL 10.9.2-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for every forum',
  `ord` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Display order of forums, ascending',
  `title` varchar(127) NOT NULL COMMENT 'Title of the forum',
  `descr` varchar(127) DEFAULT NULL COMMENT 'Description of the forum',
  `threads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of threads in the forum',
  `posts` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of posts in the forum',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for every post',
  `user` int(10) unsigned NOT NULL COMMENT 'The user ID of the author.',
  `thread` int(10) unsigned NOT NULL COMMENT 'The thread that this post is attached to.',
  `date` int(10) unsigned NOT NULL COMMENT 'Date timestamp of post.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `poststext` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID of the post',
  `text` text NOT NULL COMMENT 'Teh text lol',
  `revision` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'The revision of this post text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum` int(10) unsigned NOT NULL,
  `title` varchar(127) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `posts` int(10) unsigned NOT NULL DEFAULT 1,
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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


-- 2022-10-13 11:13:39
