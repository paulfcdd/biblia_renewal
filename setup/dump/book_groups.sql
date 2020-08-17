-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `book_groups`;
CREATE TABLE `book_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ИД',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Наименование группы',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Группы книг';

INSERT INTO `book_groups` (`id`, `title`) VALUES
(1,	'Пятикнижие'),
(2,	'Исторические книги'),
(3,	'Учительные книги'),
(4,	'Пророческие книги'),
(6,	'Евангелия'),
(8,	'Соборные послания'),
(9,	'Послания ап. Павла'),
(10,	'Пророческая книга');

-- 2020-04-08 08:41:27
