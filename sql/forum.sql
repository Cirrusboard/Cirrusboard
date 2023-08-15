-- Adminer 4.8.1 MySQL 10.11.3-MariaDB-1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `categories` (
  `id` tinyint(3) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `ord` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `title`, `ord`) VALUES
(1,	'General',	10),
(2,	'Staff forums',	0);

CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL,
  `cat` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ord` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL,
  `descr` varchar(200) DEFAULT NULL,
  `threads` int(10) unsigned NOT NULL DEFAULT 0,
  `posts` int(10) unsigned NOT NULL DEFAULT 0,
  `lastdate` int(10) unsigned DEFAULT NULL,
  `lastuser` int(10) unsigned DEFAULT NULL,
  `lastid` int(10) unsigned DEFAULT NULL,
  `minread` tinyint(4) NOT NULL DEFAULT -1,
  `minthread` tinyint(4) NOT NULL DEFAULT 1,
  `minreply` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`),
  CONSTRAINT `forums_ibfk_1` FOREIGN KEY (`cat`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `forums` (`id`, `cat`, `ord`, `title`, `descr`, `threads`, `posts`, `lastdate`, `lastuser`, `lastid`, `minread`, `minthread`, `minreply`) VALUES
(1,	1,	0,	'General Forum',	'A general forum to start out with.',	0,	0,	NULL,	NULL,	NULL,	-1,	1,	1),
(2,	2,	0,	'Staff forum',	'Forum only accessible to staff members.',	0,	0,	NULL,	NULL,	NULL,	2,	2,	2);

CREATE TABLE `forumsread` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`),
  KEY `fid` (`fid`),
  CONSTRAINT `forumsread_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forumsread_ibfk_3` FOREIGN KEY (`fid`) REFERENCES `forums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `guests` (
  `ip` char(15) NOT NULL,
  `lastview` int(10) unsigned NOT NULL,
  `bot` tinyint(1) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `thread` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1,
  `ip` char(15) NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `threadid` (`thread`),
  KEY `user` (`user`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`thread`) REFERENCES `threads` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `poststext` (
  `id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1,
  `date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`,`revision`),
  CONSTRAINT `poststext_ibfk_2` FOREIGN KEY (`id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum` int(10) unsigned NOT NULL,
  `title` varchar(127) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `posts` int(10) unsigned NOT NULL DEFAULT 1,
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  `lastdate` int(10) unsigned DEFAULT NULL,
  `lastuser` int(10) unsigned DEFAULT NULL,
  `lastid` int(10) unsigned DEFAULT NULL,
  `closed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `forum` (`forum`),
  KEY `user` (`user`),
  KEY `lastdate` (`lastdate`),
  KEY `lastid` (`lastid`),
  KEY `lastuser` (`lastuser`),
  CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`forum`) REFERENCES `forums` (`id`),
  CONSTRAINT `threads_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `threads_ibfk_7` FOREIGN KEY (`lastid`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `threads_ibfk_8` FOREIGN KEY (`lastuser`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `threadsread` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  UNIQUE KEY `uid` (`uid`,`tid`),
  KEY `tid` (`tid`),
  CONSTRAINT `threadsread_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `threadsread_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` char(60) NOT NULL,
  `token` char(64) NOT NULL,
  `ip` char(15) DEFAULT NULL,
  `joined` int(10) unsigned NOT NULL,
  `lastview` int(10) unsigned DEFAULT NULL,
  `lastpost` int(10) unsigned DEFAULT NULL,
  `rank` tinyint(4) NOT NULL DEFAULT 1,
  `posts` int(10) unsigned NOT NULL DEFAULT 0,
  `threads` int(10) unsigned NOT NULL DEFAULT 0,
  `signsep` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `showemail` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ppp` tinyint(3) unsigned NOT NULL DEFAULT 20,
  `tpp` tinyint(3) unsigned NOT NULL DEFAULT 20,
  `theme` varchar(32) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `avatar` tinyint(1) unsigned DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `customcolour` char(6) NOT NULL DEFAULT '000000',
  `timezone` varchar(32) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `header` text DEFAULT NULL,
  `signature` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2023-08-15 14:51:09
