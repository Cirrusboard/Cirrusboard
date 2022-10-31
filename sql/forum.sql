-- Adminer 4.8.1 MySQL 10.9.3-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `categories` (
  `id` tinyint(3) unsigned NOT NULL COMMENT 'ID of the category',
  `title` varchar(127) NOT NULL COMMENT 'Title of the category',
  `ord` tinyint(3) unsigned NOT NULL COMMENT 'Display order of category, ascending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for every forum',
  `cat` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Category forum is attached to',
  `ord` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Display order of forums, ascending',
  `title` varchar(127) NOT NULL COMMENT 'Title of the forum',
  `descr` varchar(127) DEFAULT NULL COMMENT 'Description of the forum',
  `threads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of threads in the forum',
  `posts` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of posts in the forum',
  `lastdate` int(10) unsigned DEFAULT NULL COMMENT 'Date of last post in forum',
  `lastuser` int(10) unsigned DEFAULT NULL COMMENT 'Author of last post in forum',
  `lastid` int(10) unsigned DEFAULT NULL COMMENT 'ID of last post in forum',
  `minread` tinyint(4) NOT NULL DEFAULT -1 COMMENT 'Minimum powerlevel to read/view forum',
  `minthread` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Minimum powerlevel to make new threads',
  `minreply` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Minimum powerlevel to reply to threads',
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the thread',
  `forum` int(10) unsigned NOT NULL COMMENT 'ID of the forum the thread is posted in',
  `title` varchar(127) NOT NULL COMMENT 'Title of forum',
  `user` int(10) unsigned NOT NULL COMMENT 'User ID of the forum author',
  `posts` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Amount of posts in the thread',
  `views` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Thread views',
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
  `lastpost` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the user''s last post.',
  `powerlevel` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'User''s power level, controlling access and permissions. (see perm.php)',
  `posts` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'User''s amount of posts.',
  `threads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'User''s amount of threads.',
  `title` varchar(255) DEFAULT NULL COMMENT 'Custom user title.',
  `avatar` tinyint(1) unsigned DEFAULT NULL COMMENT 'Does user have an avatar?',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2022-10-31 14:24:56
