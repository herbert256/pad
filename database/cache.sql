/*

  pad Cache Database

*/

USE `pad`;

CREATE TABLE `cache_etag` (
  `etag` char(22) NOT NULL ,
  `age`  int NOT NULL
) ENGINE=aria;

CREATE TABLE `cache_url` (
  `url`  char(22) NOT NULL,
  `age`  int NOT NULL ,
  `etag` char(22) NOT NULL
) ENGINE=aria;

CREATE TABLE `cache_data` (
  `etag` char(22) NOT NULL,
  `data` longblob NOT NULL
) ENGINE=aria;

ALTER TABLE `cache_etag` ADD PRIMARY KEY (`etag`);
ALTER TABLE `cache_url`  ADD PRIMARY KEY (`url`);
ALTER TABLE `cache_data` ADD PRIMARY KEY (`etag`);

GRANT ALL PRIVILEGES ON `pad`.* TO 'pad'@'localhost';
FLUSH PRIVILEGES;
