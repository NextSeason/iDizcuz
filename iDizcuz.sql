/**
 * Create database for grouple
 *
 */
CREATE DATABASE IF NOT EXISTS `grouple` DEFAULT CHARSET utf8 COLLATE utf8_general_ci;


/**
 * create table accounts that used to record data for accounts in grouple
 */
DROP TABLE IF EXISTS `accounts`;

CREATE TABLE IF NOT EXISTS `accounts` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `gid` int unsigned NOT NULL COMMENT 'grouple account id',
    `phone` varchar(11) NOT NULL DEFAULT 0,
    `email` char(80) NOT NULL DEFAULT 0,
    `password` char(40) DEFAULT NULL,
    `salt` char(20) DEFAULT NULL,
    `nickname` char(30),
    `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    `from` int unsigned NOT NULL COMMENT 'the website of app resigter from',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'account type reserved column',
    `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'status of account',
    `reg_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address for registration',
    `login_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address of last login client',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last login time',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_gid_unique` (`gid`),
    UNIQUE KEY `account_email_unique` (`email`),
    UNIQUE KEY `account_phone_unique` (`phone`)
);

/**
 * table name with prefix "idalloc_" means that this table is being used to store "id" and "id" status
 */

DROP TABLE IF EXISTS `idalloc_account`;

CREATE TABLE IF NOT EXISTS `idalloc_account` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `status` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'status of this id, 0 = normal, 1 = being used, 2 = lock',
    `idrank` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'rank of this id, 0 = normal, 1 = special level 1, ...',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idalloc_account_status_unique` (`status`),
    UNIQUE KEY `idalloc_account_idrank_unique` (`idrank`)
);

DROP TABLE IF EXISTS `idalloc_group`;

CREATE TABLE IF NOT EXISTS `idalloc_group` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `status` tinyint unsigned NOT NULL DEFAULT 0,
    `idrank` tinyint unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idalloc_group_status_unique` (`status`),
    UNIQUE KEY `idalloc_group_idrank_unique` (`idrank`)
);

DROP TABLE IF EXISTS `idalloc_activity`;

CREATE TABLE IF NOT EXISTS `idalloc_activity` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `status` tinyint unsigned NOT NULL DEFAULT 0,
    `idrank` tinyint unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idalloc_activity_status_unique` (`status`),
    UNIQUE KEY `idalloc_activity_idrank_unique` (`idrank`)
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
