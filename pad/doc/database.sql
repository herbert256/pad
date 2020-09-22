/*

  PAD Database for tracking, caching, etc.

*/

DROP DATABASE IF EXISTS `pad`;
DROP USER     IF EXISTS 'pad'@'localhost';

CREATE DATABASE `pad` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `pad`;

CREATE TABLE `track_session` (
  `id` int(11) NOT NULL,
  `sessionid` varchar(32) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end`   timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requests` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `track_request` (
  `id` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `app` varchar(32) NOT NULL,
  `page` varchar(32) NOT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int(11) NOT NULL,
  `bytes` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `done` varchar(32) NOT NULL,
  `etag` varchar(32) NOT NULL,
  `url` varchar(1023) NOT NULL,
  `ref` varchar(1023) NOT NULL,
  `ip` varchar(1023) NOT NULL,
  `agent` varchar(1023) NOT NULL,
  `cookie` varchar(1023) NOT NULL,
  `log_id` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cache_etag` (
  `etag`  char(22) NOT NULL ,
  `age`  int NOT NULL
) ENGINE=aria DEFAULT CHARSET=latin1;

ALTER TABLE `cache_etag` ADD PRIMARY KEY (`etag`);

CREATE TABLE `cache_url` (
  `url` char(22) NOT NULL,
  `age`  int NOT NULL ,
  `etag`  char(22) NOT NULL
) ENGINE=aria DEFAULT CHARSET=latin1;

ALTER TABLE `cache_url` ADD PRIMARY KEY (`url`);

CREATE TABLE `cache_data` (
  `etag` char(22) NOT NULL,
  `data` longblob NOT NULL
) ENGINE=aria DEFAULT CHARSET=latin1;

ALTER TABLE `cache_data` ADD PRIMARY KEY (`etag`);

CREATE TABLE `links` (
  `link` varchar(64) NOT NULL,
  `vars` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `track_session` ADD PRIMARY KEY (`id`);
ALTER TABLE `track_request` ADD PRIMARY KEY (`id`);
ALTER TABLE `links`         ADD PRIMARY KEY (`link`);

ALTER TABLE `track_session` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `track_request` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `track_session` ADD UNIQUE KEY `SESSIONID` (`sessionid`);

CREATE USER 'pad'@'localhost' IDENTIFIED BY 'pad';

GRANT ALL PRIVILEGES ON `pad`.* TO 'pad'@'localhost';
FLUSH PRIVILEGES;