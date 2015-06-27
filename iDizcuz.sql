/**
 * Create database for grouple
 *
 */
CREATE DATABASE IF NOT EXISTS `idizcuz` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;

use `idizcuz`;


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
    `desc` char(80) NOT NULL DEFAULT '' COMMENT 'a short description for userself',
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

/*
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` int unsigned NOT NULL COMMENT 'user id, same as account id'
);
*/

DROP TABLE IF EXISTS `topics`; 

CREATE TABLE `topics` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `cid` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'category id default is 0',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'topic type, 0 is discuss and 1 is arguments',
    `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'topic status, 0 is not public and 1 is public',
    `title` varchar( 80 ) NOT NULL COMMENT 'title of this topic',
    `desc` varchar( 255 ) NOT NULL COMMENT 'description for this topic',
    `start` timestamp NOT NULL DEFAULT NOW() COMMENT 'the time to start this topic',
    `end` timestamp NOT NULL COMMENT 'the time to stop this topic',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id`)
);

DROP TABLE IF EXISTS `topics_data`;

CREATE TABLE `topics_data` (
    `id` int unsigned NOT NULL,
    `post_cnt` int unsigned NOT NULL DEFAULT 0 COMMENT 'number of post under this topic',
    `agree` int unsigned NOT NULL DEFAULT 0,
    `disagree` int unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `points`;

CREATE TABLE `points` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `topic_id` int unsigned NOT NULL,
    `title` varchar( 80 ) NOT NULL COMMENT 'title of this point',
    `desc` varchar( 255 ) NOT NULL COMMENT 'description for this post',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `points_data`;

CREATE TABLE `points_data` (
    `id` int unsigned NOT NULL,
    `topic_id` int unsigned NOT NULL,
    `post_cnt` int unsigned NOT NULL DEFAULT 0,
    `agree` int unsigned NOT NULL DEFAULT 0,
    `disagree` int unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `drafts`;

CREATE TABLE `drafts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `account_id` int unsigned NOT NULL,
    `content` varchar(21500) NOT NULL,
    `topic_id` int unsigned NOT NULL,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    `mtime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `account_id` int unsigned NOT NULL,
    `content` varchar(21500) NOT NULL,
    `topic_id` int unsigned NOT NULL,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `reply_id` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `posts_data`;

CREATE TABLE `posts_data` (
    `id` int unsigned NOT NULL,
    `topic_id` int unsigned NOT NULL,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `comments_cnt` int unsigned NOT NULL DEFAULT 0,
    `agree` int unsigned NOT NULL DEFAULT 0,
    `disagree` int unsigned NOT NULL DEFAULT 0,
    `status` tinyint unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `votes`;

CREATE TABLE `votes` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `post_id` int unsigned NOT NULL,
    `account_id` int unsigned NOT NULL,
    `opinion` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'opinion for this post, 0 means agree, 1 means disagree',
    `value` tinyint unsigned NOT NULL DEFAULT 1,
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'send method, 0 means normal, 1 means score, 2 coin',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `marks`;

CREATE TABLE `marks` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `account_id` int unsigned NOT NULL,
    `post_id` int unsigned NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments`(
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `post_id` int unsigned NOT NULL,
    `account_id` int unsigned NOT NULL,
    `content` varchar(1000) NOT NULL,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    `mtime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `post_id` int unsigned NOT NULL,
    `account_id` int unsigned NOT NULL,
    `reason` tinyint NOT NULL DEFAULT 0,
    `status` tinyint NOT NULL DEFAULT 0,
    `result` varchar( 255 ),
    `desc` varchar( 255 ),
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    `mtime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `topic_events`;

CREATE TABLE `topic_events` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `tip` varchar(255) NOT NULL,
    `time` timestamp,
    `origin` varchar( 10 ) NOT NULL DEFAULT '',
    `origin_url` varchar( 128 ) NOT NULL DEFAULT '',
    `origin_logo` varchar( 128 ) NOT NULL DEFAULT '',
    `topic_id` varchar( 255 ) NOT NULL DEFAULT '',
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    `mtime` timestamp NOT NULL DEFAULT NOW()
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
