-- create user
CREATE USER 'zombie'@'localhost' IDENTIFIED BY '***';GRANT USAGE ON *.* TO 'zombie'@'localhost' IDENTIFIED BY '***' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON `zombie\_game`.* TO 'zombie'@'localhost';

-- create database --
CREATE DATABASE IF NOT EXISTS `zombie_game` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `zombie_game`;

-- create table inventories --
CREATE TABLE IF NOT EXISTS `inventories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of the inventory',
  `slots` int(11) NOT NULL DEFAULT '5' COMMENT 'number of slots in inventory',
  `knife` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'true if players has a knife',
  `pistol` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'true if players has a pistol',
  `ammo` int(11) NOT NULL DEFAULT '0' COMMENT 'number of ammunition',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='inventories of the players' AUTO_INCREMENT=1 ;

-- create table players --
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id of the player',
  `name` varchar(255) NOT NULL COMMENT 'name of the player',
  `hp` int(11) NOT NULL DEFAULT '100' COMMENT 'health points of the player',
  `startLat` double NOT NULL COMMENT 'starting latitude of the player',
  `startLong` double NOT NULL COMMENT 'starting longitude of the player',
  `currentLat` double NOT NULL COMMENT 'current latitude of the player',
  `currentLong` double NOT NULL COMMENT 'current longitude of the player',
  `inventory` int(11) NOT NULL COMMENT 'id of the players'' inventory',
  PRIMARY KEY (`id`),
  KEY `inventory` (`inventory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table holds the players' AUTO_INCREMENT=1 ;

-- create table zombies --
CREATE TABLE IF NOT EXISTS `zombies` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id of the zombie',
  `hp` int(11) NOT NULL COMMENT 'health points of the zombie',
  `ammo` int(11) NOT NULL COMMENT 'number of ammo the zombie holds',
  `currentLat` double NOT NULL COMMENT 'current latitude of the zombie',
  `currentLong` double NOT NULL COMMENT 'current longitude of the zombie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='table holds the zombies' AUTO_INCREMENT=1 ;

-- add foreign key for players' inventory --
ALTER TABLE `players`
ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`inventory`) REFERENCES `inventories` (`id`);
