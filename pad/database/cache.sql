/*

  pad Cache Database - the tables of the 'db' page cache backend (pad/cache/types/db.php)

  Under the names that backend reads and the account its defaults in pad/config/cache.php
  connect with: database `cache`, user cache/cache, tables `etag`, `url` and `data`. Both
  keys are padMD5 values, 22 characters. This file created cache_etag, cache_url and
  cache_data in the `pad` database, which the backend never looks in.

*/

DROP DATABASE IF EXISTS `cache`;
DROP USER     IF EXISTS 'cache'@'localhost';

CREATE DATABASE `cache` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER 'cache'@'localhost' IDENTIFIED BY 'cache';

USE `cache`;

CREATE TABLE `etag` (
  `etag` char(22) NOT NULL,
  `age`  int NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `url` (
  `url`  char(22) NOT NULL,
  `age`  int NOT NULL,
  `etag` char(22) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `data` (
  `etag` char(22) NOT NULL,
  `data` longblob NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `etag` ADD PRIMARY KEY (`etag`);
ALTER TABLE `url`  ADD PRIMARY KEY (`url`);
ALTER TABLE `data` ADD PRIMARY KEY (`etag`);

GRANT ALL PRIVILEGES ON `cache`.* TO 'cache'@'localhost';
FLUSH PRIVILEGES;
