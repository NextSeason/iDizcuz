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
    `reg_ip` int NOT NULL DEFAULT 0 COMMENT 'ip address for registration',
    `login_ip` int NOT NULL DEFAULT 0 COMMENT 'ip address of last login client',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last login time',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_email_unique` (`email`)
);

DROP TABLE IF EXISTS `accounts_data`;

CREATE TABLE `accounts_data` (
    `id` int unsigned NOT NULL,
    `post_cnt` int unsigned NOT NULL DEFAULT 0,
    `agree` int unsigned NOT NULL DEFAULT 0,
    `disagree` int unsigned NOT NULL DEFAULT 0,
    `score` int unsigned NOT NULL DEFAULT 0,
    `mark` int unsigned NOT NULL DEFAULT 0,
    `unread_msg` int unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `accounts_info`;

CREATE TABLE `accounts_info` (
    `id` int unsigned NOT NULL COMMENT 'user id, same as account id',
    `sex` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'sex of user, 1 means male, 2 means female, 0 means not selected',
    `industry` tinyint unsigned NOT NULL DEFAULT 0,
    `employment` varchar(20) NOT NULL DEFAULT '',
    `position` varchar(10) NOT NULL DEFAULT '',
    `desc` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `topics`; 

CREATE TABLE `topics` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `cid` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'category id default is 0',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'topic type, 0 is discuss and 1 is arguments',
    `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'topic status, 0 is not public and 1 is public',
    `img` char(40) NOT NULL DEFAULT '',
    `title` varchar( 80 ) NOT NULL COMMENT 'title of this topic',
    `desc` varchar( 3000 ) NOT NULL COMMENT 'description for this topic',
    `events` varchar( 255 ) NOT NULL DEFAULT '',
    `start` timestamp NOT NULL DEFAULT NOW() COMMENT 'the time to start this topic',
    `end` timestamp NOT NULL COMMENT 'the time to stop this topic',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    PRIMARY KEY(`id`)
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
    `title` varchar( 255 ) NOT NULL DEFAULT '',
    `content` varchar(21500) NOT NULL,
    `to` int unsigned NOT NULL DEFAULT 0,
    `topic_id` int unsigned NOT NULL,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `account_id` int unsigned NOT NULL,
    `title` varchar(255) NOT NULL DEFAULT '',
    `content` varchar(21500) NOT NULL,
    `to` int unsigned NOT NULL DEFAULT 0,
    `topic_id` int unsigned NOT NULL,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `reply_id` int unsigned NOT NULL DEFAULT 0,
    `ip` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last update time',
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `posts_data`;

CREATE TABLE `posts_data` (
    `id` int unsigned NOT NULL,
    `topic_id` int unsigned NOT NULL DEFAULT 0,
    `point_id` int unsigned NOT NULL DEFAULT 0,
    `comments_cnt` int unsigned NOT NULL DEFAULT 0,
    `to_times` int unsigned NOT NULL DEFAULT 0,
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
    `reply_account_id` int unsigned NOT NULL DEFAULT 0,
    `reply_comment_id` int unsigned NOT NULL DEFAULT 0,
    `content` varchar(500) NOT NULL,
    `ip` int unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `post_id` int unsigned NOT NULL,
    `account_id` int unsigned NOT NULL,
    `reason` tinyint unsigned NOT NULL DEFAULT 0,
    `status` tinyint unsigned NOT NULL DEFAULT 0,
    `result` varchar( 255 ),
    `desc` varchar( 255 ),
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `comment_complaint`;

CREATE TABLE `comment_complaint` ( 
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `comment_id` int unsigned NOT NULL,
    `reason` tinyint unsigned NOT NULL DEFAULT 0,
    `status` tinyint unsigned NOT NULL DEFAULT 0,
    `result` tinyint unsigned NOT NULL DEFAULT 0,
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `topic_events`;

CREATE TABLE `topic_events` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `tip` varchar(255) NOT NULL DEFAULT '',
    `author` varchar( 8 ) NOT NULL DEFAULT '',
    `time` timestamp,
    `img` char(40) NOT NULL DEFAULT '',
    `origin` varchar( 10 ) NOT NULL DEFAULT '',
    `origin_url` varchar( 128 ) NOT NULL DEFAULT '',
    `origin_logo` varchar( 128 ) NOT NULL DEFAULT '',
    `topic_id` varchar( 255 ) NOT NULL DEFAULT '',
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `from` int unsigned NOT NULL DEFAULT 0 COMMENT 'user id who sent this message, set 0 if system account',
    `to` int unsigned NOT NULL DEFAULT 0,
    `title` varchar( 255 ) NOT NULL DEFAULT '',
    `context` varchar(1000) NOT NULL DEFAULT '',
    `read` tinyint unsigned NOT NULL DEFAULT 0,
    `del` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '0 means never been deleted, 1 means deleted by receiver, 2 means deleted by sender',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '0 = system message, 1 = got post, 2 = got reply, 3 = got comment',
    `ctime` timestamp NOT NULL DEFAULT NOW(),
    PRIMARY KEY( `id` )
);
