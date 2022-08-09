/*  PAD Demo Database */

DROP DATABASE IF EXISTS `demo`;
DROP USER     IF EXISTS 'demo'@'localhost';

CREATE DATABASE `demo`;
USE `demo`;

CREATE USER 'demo'@'localhost' IDENTIFIED BY 'demo';
GRANT ALL PRIVILEGES ON `demo`.* TO 'demo'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE `staff` (
  `name`   varchar(32),
  `phone`  varchar(32),
  `salary` decimal(8,2),
  `bonus`  decimal(8,2) 
);

insert into `staff` values
  ('bob',   '555-3425', 1000, 400),
  ('jim',   '555-4364', 2000, 300),
  ('joe',   '555-3422', 3000, 200),
  ('jerry', '555-4973', 4000, 100);

  