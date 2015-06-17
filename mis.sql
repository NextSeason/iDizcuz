DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `email` char(80) NOT NULL,
    `passwd` char(40) NOT NULL,
    `salt` char(32) NOT NULL,
    `uname` char(30) NOT NULL,
    `level` tinyint unsigned NOT NULL DEFAULT 0 COMMENT 'admin level',
    `reg_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address for registration',
    `login_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT 'ip address of last login client',
    `ctime` timestamp NOT NULL DEFAULT NOW() COMMENT 'create time',
    `mtime` timestamp NOT NULL DEFAULT NOW() COMMENT 'last login time',
    PRIMARY KEY (`id`),
    UNIQUE KEY `account_email_unique` (`email`)
);
