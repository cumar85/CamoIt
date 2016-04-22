-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 01 2016 г., 17:05
-- Версия сервера: 5.5.41-log
-- Версия PHP: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `camo_it`
--
CREATE DATABASE IF NOT EXISTS `camo_it` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `camo_it`;


--
-- Структура таблицы `topics`
--

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `views` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `addRandTopics`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addRandTopics`(IN counter INTEGER(11))
BEGIN
    DECLARE id INT;
    DECLARE title VARCHAR(255);
    DECLARE views INT;
    DECLARE timestamp INT;
    SET id = 1;
    WHILE id <= counter DO
      SET title = CONCAT_WS(' ', 'Тестовая тема № '  , id);
      SET views = FLOOR(RAND() * 10000);
      INSERT INTO topics VALUES (id, title, views);
      SET id = id + 1;
    END WHILE;
END$$
DELIMITER ;

/* CALL addRandTopics(150000) */
/* CALL addRandMessages(600000, 100) */



-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(255) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



DELIMITER $$
DROP PROCEDURE IF EXISTS `addRandMessages`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addRandMessages`(IN counter INTEGER(11), IN maxTopicId INTEGER(11))
BEGIN
    DECLARE id INT;
    DECLARE msg VARCHAR(255);
    DECLARE topic_id INT;
    DECLARE timestamp INT;
    SET id = 1;
    WHILE id <= counter DO
      SET msg = CONCAT_WS(' ', 'Тестовое сообщение № '  , id);
      SET topic_id = FLOOR(RAND() * maxTopicId) + 1;
      SET timestamp = UNIX_TIMESTAMP(NOW()) - (FLOOR(RAND() * 100000000));
      INSERT INTO messages VALUES (id, msg, topic_id, timestamp);
      SET id = id + 1;
    END WHILE;
END$$
DELIMITER ;

/* CALL addRandTopics(150000) */
/* CALL addRandMessages(600000, 100) */