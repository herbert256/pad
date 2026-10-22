/*
  The padSelect database: a small music catalogue - labels, artists, genres, albums, their
  tracks, singles, playlists and reviews - that the padSelect application walks with the
  Select subsystem. Every name in it is made up.

  pad/install/db.sh loads it as the database administrator; the tables alone can be loaded
  again as the padSelect user:  sed -n '/^-- tables/,$p' padSelect.sql | mysql -upadSelect -p padSelect
*/

DROP DATABASE IF EXISTS `padSelect`;
DROP USER     IF EXISTS 'padSelect'@'localhost';

CREATE DATABASE `padSelect` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'padSelect'@'localhost' IDENTIFIED BY 'padSelect';
GRANT ALL PRIVILEGES ON `padSelect`.* TO 'padSelect'@'localhost';

USE `padSelect`;

-- tables

DROP TABLE IF EXISTS `reviews`, `playlist_tracks`, `playlists`, `singles`, `tracks`, `albums`, `artists`, `genres`, `labels`;

CREATE TABLE `labels` (
  `label_id` int NOT NULL,
  `name`     varchar(40) NOT NULL,
  `country`  varchar(30) NOT NULL,
  `founded`  smallint NOT NULL,
  PRIMARY KEY (`label_id`)
);

CREATE TABLE `genres` (
  `genre_code`  varchar(12) NOT NULL,
  `genre`       varchar(30) NOT NULL,
  `parent_code` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`genre_code`)
);

CREATE TABLE `artists` (
  `artist_id` int NOT NULL,
  `name`      varchar(40) NOT NULL,
  `country`   varchar(30) NOT NULL,
  `formed`    smallint NOT NULL,
  `signed_to` int DEFAULT NULL,
  PRIMARY KEY (`artist_id`)
);

CREATE TABLE `albums` (
  `album_id`   int NOT NULL,
  `artist_id`  int NOT NULL,
  `title`      varchar(40) NOT NULL,
  `released`   smallint NOT NULL,
  `genre_code` varchar(12) NOT NULL,
  `price`      decimal(5,2) NOT NULL,
  PRIMARY KEY (`album_id`)
);

CREATE TABLE `tracks` (
  `album_id` int NOT NULL,
  `track_no` tinyint NOT NULL,
  `title`    varchar(40) NOT NULL,
  `seconds`  smallint NOT NULL,
  PRIMARY KEY (`album_id`, `track_no`)
);

CREATE TABLE `singles` (
  `single_id` int NOT NULL,
  `artist_id` int NOT NULL,
  `title`     varchar(40) NOT NULL,
  `released`  smallint NOT NULL,
  PRIMARY KEY (`single_id`)
);

CREATE TABLE `playlists` (
  `playlist_id` int NOT NULL,
  `name`        varchar(30) NOT NULL,
  `curator`     varchar(30) NOT NULL,
  PRIMARY KEY (`playlist_id`)
);

CREATE TABLE `playlist_tracks` (
  `playlist_id` int NOT NULL,
  `position`    tinyint NOT NULL,
  `album_id`    int NOT NULL,
  `track_no`    tinyint NOT NULL,
  PRIMARY KEY (`playlist_id`, `position`)
);

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `album_id`  int NOT NULL,
  `reviewer`  varchar(30) NOT NULL,
  `stars`     tinyint NOT NULL,
  `written`   date NOT NULL,
  `body`      varchar(120) NOT NULL,
  PRIMARY KEY (`review_id`)
);

INSERT INTO `labels` VALUES
  (1, 'Northern Lights Records', 'Norway',      1998),
  (2, 'Copper Kettle Music',     'Ireland',     2004),
  (3, 'Blue Harbour',            'USA',         1987),
  (4, 'Tin Roof Tapes',          'Netherlands', 2011);

INSERT INTO `genres` VALUES
  ('rock',       'Rock',             NULL),
  ('indie',      'Indie rock',       'rock'),
  ('prog',       'Progressive rock', 'rock'),
  ('jazz',       'Jazz',             NULL),
  ('fusion',     'Jazz fusion',      'jazz'),
  ('folk',       'Folk',             NULL),
  ('electronic', 'Electronic',       NULL),
  ('ambient',    'Ambient',          'electronic');

INSERT INTO `artists` VALUES
  (1, 'The Velvet Owls',        'UK',          2003, 1),
  (2, 'Marta Lindqvist',        'Sweden',      2009, 1),
  (3, 'Harbour Lights Quartet', 'USA',         1985, 3),
  (4, 'Ciaran Doyle',           'Ireland',     2001, 2),
  (5, 'Static Gardens',         'Netherlands', 2012, 4),
  (6, 'Kite & Compass',         'Canada',      2015, 2),
  (7, 'Ines Moreau',            'France',      1994, 3),
  (8, 'Low Tide Radio',         'Australia',   2018, NULL);

INSERT INTO `albums` VALUES
  ( 1, 1, 'Midnight Feathers',     2005, 'indie',      12.99),
  ( 2, 1, 'Paper Moons',           2009, 'indie',      13.99),
  ( 3, 1, 'The Long Flight Home',  2016, 'prog',       15.49),
  ( 4, 2, 'Northern Quiet',        2011, 'folk',       11.99),
  ( 5, 2, 'Glass Fields',          2018, 'ambient',    14.99),
  ( 6, 3, 'Blue Hour Sessions',    1988, 'jazz',        9.99),
  ( 7, 3, 'Harbour Nights',        1994, 'fusion',     10.99),
  ( 8, 4, 'Stones of Kerry',       2003, 'folk',       12.49),
  ( 9, 5, 'Signal / Noise',        2014, 'electronic', 13.49),
  (10, 5, 'Slow Static',           2020, 'ambient',    16.99),
  (11, 6, 'True North',            2017, 'indie',      12.99),
  (12, 7, 'Rue des Songes',        1997, 'jazz',       11.49),
  (13, 7, 'Lumière',               2006, 'fusion',     13.99);

INSERT INTO `tracks` VALUES
  ( 1, 1, 'Feathers',              214), ( 1, 2, 'Lanterns',              187), ( 1, 3, 'Night Bus',             243), ( 1, 4, 'Owl Light',             301),
  ( 2, 1, 'Paper Moons',           226), ( 2, 2, 'Origami Heart',         198), ( 2, 3, 'Satellite',             254),
  ( 3, 1, 'Departure',             412), ( 3, 2, 'The Long Flight',       655), ( 3, 3, 'Home',                  389),
  ( 4, 1, 'Quiet',                 176), ( 4, 2, 'Birch',                 203), ( 4, 3, 'Ferry to Visby',        241), ( 4, 4, 'Snowmelt',              188),
  ( 5, 1, 'Glass',                 362), ( 5, 2, 'Fields',                417), ( 5, 3, 'Frost Hum',             298),
  ( 6, 1, 'Blue Hour',             322), ( 6, 2, 'Dockside',              287), ( 6, 3, 'Fog Horn Waltz',        264), ( 6, 4, 'Last Ferry',            351),
  ( 7, 1, 'Harbour Nights',        305), ( 7, 2, 'Neon Tide',             278), ( 7, 3, 'Seventh Pier',          332),
  ( 8, 1, 'Stones of Kerry',       229), ( 8, 2, 'The Low Road',          197), ( 8, 3, 'Atlantic',              266),
  ( 9, 1, 'Signal',                244), ( 9, 2, 'Noise',                 231), ( 9, 3, 'Carrier Wave',          279), ( 9, 4, 'Static Bloom',          312),
  (10, 1, 'Slow',                  455), (10, 2, 'Static',                402),
  (11, 1, 'True North',            211), (11, 2, 'Compass Rose',          233), (11, 3, 'Kites',                 189),
  (12, 1, 'Rue des Songes',        296), (12, 2, 'Café Minuit',           247), (12, 3, 'Pluie',                 263),
  (13, 1, 'Lumière',               318), (13, 2, 'Aube',                  276), (13, 3, 'Prisme',                301), (13, 4, 'Crépuscule',            342);

INSERT INTO `singles` VALUES
  (1, 1, 'Owl Song',     2004),
  (2, 2, 'First Snow',   2010),
  (3, 5, 'Pulse',        2013),
  (4, 8, 'Shoreline',    2019),
  (5, 6, 'Paper Boats',  2016);

INSERT INTO `playlists` VALUES
  (1, 'Late Night',     'Sam'),
  (2, 'Road Trip',      'Alex'),
  (3, 'Sunday Morning', 'Robin');

INSERT INTO `playlist_tracks` VALUES
  (1, 1,  6, 1), (1, 2,  1, 3), (1, 3, 12, 2), (1, 4,  5, 1),
  (2, 1, 11, 1), (2, 2,  2, 3), (2, 3,  9, 1), (2, 4,  3, 2), (2, 5,  8, 3),
  (3, 1,  4, 1), (3, 2, 13, 2), (3, 3, 10, 1);

INSERT INTO `reviews` VALUES
  ( 1,  1, 'Sam',   5, '2005-06-02', 'A debut that sounds like a band ten years in.'),
  ( 2,  1, 'Alex',  4, '2006-01-15', 'Night Bus alone is worth it.'),
  ( 3,  3, 'Robin', 5, '2016-10-30', 'Eleven minutes of The Long Flight and not one wasted.'),
  ( 4,  4, 'Sam',   4, '2011-03-08', 'Quiet, as promised, and never dull.'),
  ( 5,  6, 'Alex',  3, '1990-02-11', 'Lovely playing, a little too polite.'),
  ( 6,  6, 'Robin', 4, '1999-08-21', 'Better every year since.'),
  ( 7,  9, 'Sam',   4, '2014-05-19', 'Static Bloom is the hit nobody released.'),
  ( 8, 10, 'Robin', 5, '2020-11-03', 'Two tracks, fourteen minutes, no hurry at all.'),
  ( 9, 12, 'Alex',  5, '1998-04-27', 'Paris in the rain, in thirteen minutes.'),
  (10, 13, 'Sam',   4, '2006-09-09', 'Brighter than the debut, just as warm.'),
  (11, 11, 'Robin', 3, '2017-07-14', 'Pleasant, if a little close to its heroes.'),
  (12,  5, 'Alex',  4, '2018-12-01', 'Ambient that actually goes somewhere.');
