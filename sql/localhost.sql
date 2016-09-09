-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: May 16, 2011, 02:40 PM
-- 伺服器版本: 5.1.41
-- PHP 版本: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `shopcart`
--
CREATE DATABASE `shopcart` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `shopcart`;

-- --------------------------------------------------------

--
-- 資料表格式： `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序號',
  `datetime` datetime NOT NULL COMMENT '日期時間',
  `name` varchar(30) NOT NULL COMMENT '名稱',
  `email` varchar(30) NOT NULL,
  `phone` varchar(15) NOT NULL COMMENT '電話',
  `address` varchar(100) NOT NULL COMMENT '住址',
  `data` text NOT NULL COMMENT '訂單資料',
  `total` int(11) NOT NULL DEFAULT '0',
  `sn` varchar(32) NOT NULL,
  `_checkout` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='訂單資料表';

--
-- 資料表格式： `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序號',
  `category` int(11) NOT NULL COMMENT '分類',
  `title` varchar(30) NOT NULL COMMENT '標題',
  `content` text NOT NULL COMMENT '內容',
  `pic` varchar(50) DEFAULT NULL COMMENT '圖片',
  `cost` int(11) NOT NULL COMMENT '成本',
  `price` int(11) NOT NULL COMMENT '售價',
  `store` int(11) NOT NULL DEFAULT '0' COMMENT '庫存',
  `sale` int(11) NOT NULL DEFAULT '0' COMMENT '銷詹量',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '點擊次數',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='產品資料表';

-- --------------------------------------------------------

--
-- 資料表格式： `product_category`
--

CREATE TABLE IF NOT EXISTS `product_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序號',
  `title` varchar(30) NOT NULL COMMENT '名稱',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='產品分類資料表' AUTO_INCREMENT=1 ;

--
-- 列出以下資料庫的數據： `product_category`
--

INSERT INTO `product_category` (`id`, `title`) VALUES
(0, '未分類');


CREATE USER 'shopcart'@'localhost' IDENTIFIED BY  '***';

GRANT USAGE ON * . * TO  'shopcart'@'localhost' IDENTIFIED BY  '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON  `shopcart` . * TO  'shopcart'@'localhost';
