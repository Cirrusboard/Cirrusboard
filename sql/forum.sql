-- Adminer 4.8.1 MySQL 10.9.4-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `categories` (
  `id` tinyint(3) unsigned NOT NULL COMMENT 'ID of the category',
  `title` varchar(100) NOT NULL COMMENT 'Title of the category',
  `ord` tinyint(3) unsigned NOT NULL COMMENT 'Display order of category, ascending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `title`, `ord`) VALUES
(1,	'General',	10),
(2,	'Staff forums',	0);

CREATE TABLE `forums` (
  `id` int(10) unsigned NOT NULL COMMENT 'Incrementing ID for every forum',
  `cat` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Category forum is attached to',
  `ord` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Display order of forums, ascending',
  `title` varchar(100) NOT NULL COMMENT 'Title of the forum',
  `descr` varchar(200) DEFAULT NULL COMMENT 'Description of the forum',
  `threads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of threads in the forum',
  `posts` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total amount of posts in the forum',
  `lastdate` int(10) unsigned DEFAULT NULL COMMENT 'Date of last post in forum',
  `lastuser` int(10) unsigned DEFAULT NULL COMMENT 'Author of last post in forum',
  `lastid` int(10) unsigned DEFAULT NULL COMMENT 'ID of last post in forum',
  `minread` tinyint(4) NOT NULL DEFAULT -1 COMMENT 'Minimum powerlevel to read/view forum',
  `minthread` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Minimum powerlevel to make new threads',
  `minreply` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Minimum powerlevel to reply to threads',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `forums` (`id`, `cat`, `ord`, `title`, `descr`, `threads`, `posts`, `lastdate`, `lastuser`, `lastid`, `minread`, `minthread`, `minreply`) VALUES
(1,	1,	0,	'General Forum',	'A general forum to start out with.',	0,	0,	NULL,	NULL,	NULL,	-1,	1,	1),
(2,	2,	0,	'Staff forum',	'Forum only accessible to staff members.',	0,	0,	NULL,	NULL,	NULL,	2,	2,	2);

CREATE TABLE `forumsread` (
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `guests` (
  `ip` char(15) NOT NULL COMMENT 'The IP of the guest',
  `lastview` int(10) unsigned NOT NULL COMMENT 'Timestamp of the guest''s lastview',
  `bot` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Is guest a bot?',
  `lastforum` int(10) unsigned DEFAULT NULL COMMENT 'Last forum guest visited (NYI)',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for every post',
  `user` int(10) unsigned NOT NULL COMMENT 'The user ID of the author.',
  `thread` int(10) unsigned NOT NULL COMMENT 'The thread that this post is attached to.',
  `date` int(10) unsigned NOT NULL COMMENT 'Date timestamp of post.',
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1 COMMENT 'The current text revision of the post.',
  `ip` char(15) NOT NULL COMMENT 'IP address of the poster.',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Is post deleted?',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `poststext` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID of the post',
  `text` text NOT NULL COMMENT 'Teh text lol',
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1 COMMENT 'The revision of this post text',
  `date` int(10) unsigned DEFAULT NULL COMMENT 'Date of the last revision'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID of the thread',
  `forum` int(10) unsigned NOT NULL COMMENT 'ID of the forum the thread is posted in',
  `title` varchar(127) NOT NULL COMMENT 'Title of forum',
  `user` int(10) unsigned NOT NULL COMMENT 'User ID of the forum author',
  `posts` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Amount of posts in the thread',
  `views` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Thread views',
  `lastdate` int(10) unsigned DEFAULT NULL COMMENT 'Date of last post in thread',
  `lastuser` int(10) unsigned DEFAULT NULL COMMENT 'Author of last post in thread',
  `lastid` int(10) unsigned DEFAULT NULL COMMENT 'ID of last post in thread',
  `closed` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Is thread closed/locked?',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Is thread sticky? (Shows up at the top of the thread listing)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `threadsread` (
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  UNIQUE KEY `uid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Incrementing ID for each user',
  `name` varchar(32) NOT NULL COMMENT 'Username',
  `password` char(60) NOT NULL COMMENT 'Password hash (bcrypt hashed)',
  `token` char(64) NOT NULL COMMENT 'Token used for cookie authentication',
  `ip` char(15) DEFAULT NULL COMMENT 'Latest IP address of user',
  `url` varchar(150) DEFAULT NULL COMMENT 'Last URL user visited',
  `joined` int(10) unsigned NOT NULL COMMENT 'Timestamp when user joined',
  `lastview` int(10) unsigned DEFAULT NULL COMMENT 'Timestamp when user last viewed',
  `lastpost` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the user''s last post.',
  `lastforum` int(10) unsigned DEFAULT NULL COMMENT 'ID of the forum user was last viewing.',
  `powerlevel` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'User''s power level, controlling access and permissions. (see perm.php)',
  `tempbanned` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Is user tempbanned? (NYI)',
  `posts` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'User''s amount of posts.',
  `threads` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'User''s amount of threads.',
  `rankset` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'The current rankset of the user (NYI)',
  `blocklayouts` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Block all post layouts (signatures, headers etc)',
  `signsep` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT 'Signature separator',
  `showemail` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Whether the user''s email should be visible.',
  `ppp` tinyint(3) unsigned NOT NULL DEFAULT 20 COMMENT 'Posts per page for pagination',
  `tpp` tinyint(3) unsigned NOT NULL DEFAULT 20 COMMENT 'Threads per page for pagination',
  `theme` varchar(32) DEFAULT NULL COMMENT 'User''s theme (NYI)',
  `email` varchar(100) DEFAULT NULL COMMENT 'User''s email address.',
  `title` varchar(255) DEFAULT NULL COMMENT 'Custom user title.',
  `avatar` tinyint(1) unsigned DEFAULT NULL COMMENT 'Does user have an avatar?',
  `birthday` varchar(10) DEFAULT NULL COMMENT 'Birthday (in Y-M-d format)',
  `customcolour` char(6) DEFAULT NULL COMMENT 'Custom username colour',
  `timezone` varchar(32) DEFAULT NULL COMMENT 'The user''s timezone',
  `location` varchar(150) DEFAULT NULL COMMENT 'User''s location',
  `header` text DEFAULT NULL COMMENT 'Post layout header',
  `signature` text DEFAULT NULL COMMENT 'Post layout signature',
  `bio` text DEFAULT NULL COMMENT 'Stuff to write that can be displayed on the user''s profile',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2023-01-02 18:28:54
