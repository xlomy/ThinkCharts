-- phpMyAdmin SQL Dump
-- version 3.4.8
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 07 月 13 日 10:04
-- 服务器版本: 5.1.60
-- PHP 版本: 5.2.17p1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
-- ----------------------------
-- Table structure for `think_domains`
-- ----------------------------
DROP TABLE IF EXISTS `shuipfcms_domains`;
CREATE TABLE `shuipfcms_domains` (
  `id` int(4) unsigned NOT NULL auto_increment,
  `module` varchar(20) default NULL COMMENT '模块',
  `domain` varchar(255) default NULL COMMENT '域名',
  `status` int(1) default '0' COMMENT '状态',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='域名绑定' AUTO_INCREMENT=1 ;