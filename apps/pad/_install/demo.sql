/*  PAD Demo Database */

USE `demo`;

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

CREATE TABLE `table1` (`key` integer(3),`text` varchar(32) );
CREATE TABLE `table2` (`key` integer(3),`text` varchar(32) );
CREATE TABLE `table3` (`key` integer(3),`text` varchar(32) );
CREATE TABLE `table4` (`key` integer(3),`text` varchar(32) );

insert into `table1` values
  (11,'Only in table 1'),
  (12,'Only in table 1'),
  (21,'In both tables'),
  (22,'In both tables');
  
insert into `table2` values
  (21,'In both tables'),
  (22,'In both tables'),
  (31,'Only in table 2'),
  (32,'Only in table 2');

insert into `table3` values
  (22,'Multi join 3');

insert into `table4` values
  (22,'Multi join 4');

GRANT ALL PRIVILEGES ON `demo`.* TO 'demo'@'localhost';
FLUSH PRIVILEGES;
  