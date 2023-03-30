/*

  pad Cache Database

*/

DROP DATABASE IF EXISTS `cache`;
DROP USER     IF EXISTS 'cache'@'localhost';

CREATE DATABASE `cache` CHARACTER SET latin1 COLLATE latin1_bin;

USE `cache`;

CREATE TABLE `etag` (
  `etag` char(22) NOT NULL ,
  `age`  int NOT NULL
) ENGINE=aria;

CREATE TABLE `url` (
  `url`  char(22) NOT NULL,
  `age`  int NOT NULL ,
  `etag` char(22) NOT NULL
) ENGINE=aria;

CREATE TABLE `data` (
  `etag` char(22) NOT NULL,
  `data` longblob NOT NULL
) ENGINE=aria;

ALTER TABLE `etag` ADD PRIMARY KEY (`etag`);
ALTER TABLE `url`  ADD PRIMARY KEY (`url`);
ALTER TABLE `data` ADD PRIMARY KEY (`etag`);

CREATE USER 'cache'@'localhost' IDENTIFIED BY 'cache';

GRANT ALL PRIVILEGES ON `cache`.* TO 'cache'@'localhost';
FLUSH PRIVILEGES;