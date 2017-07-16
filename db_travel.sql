-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 年 06 月 12 日 16:22
-- 服务器版本: 5.5.40
-- PHP 版本: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `db_travel`
--

-- --------------------------------------------------------

--
-- 表的结构 `tra_config`
--

CREATE TABLE IF NOT EXISTS `tra_config` (
  `con_id` int(11) NOT NULL AUTO_INCREMENT,
  `em_host` varchar(255) NOT NULL,
  `em_port` varchar(20) NOT NULL,
  `em_user` varchar(255) NOT NULL,
  `em_pwd` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  PRIMARY KEY (`con_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tra_config`
--

INSERT INTO `tra_config` (`con_id`, `em_host`, `em_port`, `em_user`, `em_pwd`, `from_name`) VALUES
(1, '', '25', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `tra_email`
--

CREATE TABLE IF NOT EXISTS `tra_email` (
  `em_id` int(11) NOT NULL AUTO_INCREMENT,
  `key_id` int(11) NOT NULL,
  `key_table` varchar(50) NOT NULL,
  `em_to` varchar(255) NOT NULL,
  `em_cc` varchar(255) NOT NULL,
  `em_bcc` varchar(255) NOT NULL,
  `em_from` varchar(255) NOT NULL,
  `em_subject` varchar(255) NOT NULL,
  `em_body` text NOT NULL,
  `em_attach` varchar(255) NOT NULL,
  `em_time` datetime NOT NULL,
  `em_status` tinyint(1) NOT NULL DEFAULT '1',
  `em_result` varchar(255) NOT NULL,
  `use_id` int(11) NOT NULL,
  PRIMARY KEY (`em_id`),
  KEY `key_table` (`key_table`),
  KEY `key_id` (`key_id`),
  KEY `use_id` (`use_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;


--
-- 表的结构 `tra_record`
--

CREATE TABLE IF NOT EXISTS `tra_record` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT,
  `re_name` varchar(255) NOT NULL,
  `re_contact` varchar(255) NOT NULL,
  `re_tel` varchar(255) NOT NULL,
  `re_fax` varchar(255) NOT NULL,
  `re_email` varchar(255) NOT NULL,
  `re_addr` varchar(255) NOT NULL,
  `re_postcode` varchar(10) NOT NULL,
  `re_date` date NOT NULL,
  `re_remark` text NOT NULL,
  `re_status` tinyint(1) NOT NULL DEFAULT '1',
  `re_time` datetime NOT NULL,
  PRIMARY KEY (`re_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `tra_record`
--

INSERT INTO `tra_record` (`re_id`, `re_name`, `re_contact`, `re_tel`, `re_fax`, `re_email`, `re_addr`, `re_postcode`, `re_date`, `re_remark`, `re_status`, `re_time`) VALUES
(1, 'test1', 'test2', 'test3', 'test4', '164413344@qq.com', 'test6', 'test7', '2017-06-13', 'testtest', 1, '2017-06-12 15:46:36'),
(2, 'ceshi', 'ceshi2', 'ceshi3', 'ceshi4', 'huiwen.zheng@8hy.hk', 'ceshi6', 'ceshi7', '2017-06-13', 'ceshiceshi', 1, '2017-06-12 16:13:59');

-- --------------------------------------------------------

--
-- 表的结构 `tra_user`
--

CREATE TABLE IF NOT EXISTS `tra_user` (
  `use_id` int(11) NOT NULL AUTO_INCREMENT,
  `use_no` varchar(20) NOT NULL,
  `use_pwd` char(32) NOT NULL,
  `use_name` varchar(20) NOT NULL,
  `use_status` tinyint(1) NOT NULL DEFAULT '1',
  `use_time` datetime NOT NULL,
  PRIMARY KEY (`use_id`),
  KEY `use_account` (`use_no`,`use_pwd`,`use_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tra_user`
--

INSERT INTO `tra_user` (`use_id`, `use_no`, `use_pwd`, `use_name`, `use_status`, `use_time`) VALUES
(1, 'admin', '98ab30ece197a0f22abf3a239a04ecf9', 'admin', 1, '2017-06-12 11:29:21');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
