/*  PAD Demo Database */

DROP DATABASE IF EXISTS `demo`;
DROP USER     IF EXISTS 'demo'@'localhost';

CREATE DATABASE `demo`;
USE `demo`;

CREATE USER 'demo'@'localhost' IDENTIFIED BY 'demo';
GRANT ALL PRIVILEGES ON `demo`.* TO 'demo'@'localhost';
FLUSH PRIVILEGES;

  