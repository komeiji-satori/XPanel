-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `balance`;
CREATE TABLE `balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `balance` double(64,2) NOT NULL DEFAULT '30.00',
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text,
  `realname` varchar(255) DEFAULT NULL,
  `password` text,
  `regtime` text,
  `regip` varchar(64) DEFAULT NULL,
  `lastlogin` bigint(20) DEFAULT NULL,
  `is_genuine` int(4) DEFAULT '0',
  `invite_limit` int(4) DEFAULT '1',
  `x` double DEFAULT '0',
  `y` double DEFAULT '0',
  `z` double DEFAULT '0',
  `world` varchar(255) DEFAULT 'world',
  `email` varchar(255) DEFAULT 'your@email.com',
  `isLogged` smallint(6) DEFAULT '0',
  `sync_complete` varchar(5) DEFAULT 'true',
  `login_ip` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2017-05-21 05:07:11
