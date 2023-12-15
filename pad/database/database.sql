/*

  pad Database for tracking, caching, etc.

*/

DROP DATABASE IF EXISTS `pad`;
DROP USER     IF EXISTS 'pad'@'localhost';

CREATE DATABASE `pad` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER 'pad'@'localhost' IDENTIFIED BY 'pad';
