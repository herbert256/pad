/*

  pad Database for tracking, caching, etc.

*/

USE `pad`;

CREATE TABLE `track_session` (
  `session` char(8) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end`   timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requests` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM;

ALTER TABLE `track_session` ADD PRIMARY KEY (`session`);

CREATE TABLE `track_request` (
  `session` char(8) NOT NULL,
  `request` char(8) NOT NULL,  
  `page` varchar(32) NOT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int(11) NOT NULL,
  `bytes` int(11) NOT NULL,
  `stop` varchar(3) NOT NULL,
  `etag` char(22) NOT NULL,
  `url` varchar(1023) NOT NULL,
  `ref` varchar(1023) NOT NULL,
  `ip` varchar(1023) NOT NULL,
  `agent` varchar(1023) NOT NULL
) ENGINE=MyISAM;

ALTER TABLE `track_request` ADD PRIMARY KEY (`session`, `request`);

CREATE TABLE `track_data` (
  `etag` char(22) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=MyISAM;

ALTER TABLE `track_data` ADD PRIMARY KEY (`etag`);

CREATE TABLE `links` (
  `link` varchar(64) NOT NULL,
  `vars` text NOT NULL
) ENGINE=MyISAM;

ALTER TABLE `links` ADD PRIMARY KEY (`link`);

GRANT ALL PRIVILEGES ON `pad`.* TO 'pad'@'localhost';
FLUSH PRIVILEGES;