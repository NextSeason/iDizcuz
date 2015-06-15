/**
 * Create database for grouple
 *
 */
CREATE DATABASE IF NOT EXISTS `idizcuz` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;


/**
 * create table accounts that used to record data for accounts in grouple
 */
DROP TABLE IF EXISTS `accounts`;

CREATE TABLE IF NOT EXISTS `accounts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `email` char(80) NOT NULL,
    `passwd` char(40) NOT NULL,
    `salt` char(32) NOT NULL,
    `uname` char(30) NOT NULL,
    `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'account type reserved column',
    `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'status of account',
    `reg_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address for registration',
    `login_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address of last login client',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last login time',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_email_unique` (`email`)
);

DROP TABLE IF EXISTS `topics`; 

CREATE TABLE IF NOT EXISTS `topics` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `cid` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'category id default is 0',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'topic type, 0 is discuss and 1 is arguments',
    `title` varchar( 120 ) NOT NULL COMMENT 'title of this topic',
    `post_cnt` int unsigned NOT NULL DEFAULT 0 COMMENT 'number of post under this topic',
    `desc` varchar( 255 ) NOT NULL COMMENT 'description for this topic',
    `points` text COMMENT 'a string decode from json, to record all points',
    `points_desc` text COMMENT 'a string decode from json, to record descriptions for each points',
    `points_post_cnt` varchar(255) COMMENT 'a string decode from json, to record the number of points',
    `start` timestamp NOT NULL DEFAULT NOW() COMMENT 'the time to start this topic',
    `end` timestamp NOT NULL COMMENT 'the time to stop this topic',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id`)
);

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `author_id` int unsigned NOT NULL,
    `content` text NOT NULL,
    `point` varchar( 80 ),
    `zan` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `zan`;

CREATE TABLE `zan` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `post_id` int unsigned NOT NULL,
    `account_id` int unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

/*
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name`,
    `sex`,
    `birthday`,
    `position`
    `address`,
    `postcode`,
    `phone`,
    `email`,
    `hometown`,
    `primary_school`,
    `junior_high_school`,
    `senior_high_school`,
    `college`,
    `introduce`,
    `security`
);

CREATE TABLE IF EXISTS `edu_experience` (
);

CREATE TABLE IF EXISTS `work_experience` (
);
*/
