-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 年 10 月 06 日 18:34
-- 服务器版本: 5.5.40
-- PHP 版本: 5.2.17

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
(1, 'c2.icoremail.net', '25', 'zbx@huyi.cn', 'lcx^zbx2009', 'test');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `tra_email`
--

INSERT INTO `tra_email` (`em_id`, `key_id`, `key_table`, `em_to`, `em_cc`, `em_bcc`, `em_from`, `em_subject`, `em_body`, `em_attach`, `em_time`, `em_status`, `em_result`, `use_id`) VALUES
(8, 2, 'tra_record', '121867334@qq.com', '', '', 'zbx@huyi.cn', 'tetttt', '<p>		asfdsafds\r\nasdfdsaf		</p>', '', '2017-07-16 18:56:52', 0, 'Language string failed to load: connect_host', 1),
(9, 2, 'tra_record', '121867334@qq.com', '', '', 'zbx@huyi.cn', 'test', '<p>		asfdsafds\r\nasdfdsaf		</p>', '', '2017-07-16 18:58:47', 1, '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `tra_record`
--

CREATE TABLE IF NOT EXISTS `tra_record` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客人姓名',
  `re_confNumber` varchar(255) NOT NULL COMMENT '订单号',
  `re_customer` varchar(255) NOT NULL COMMENT '客人姓名',
  `re_chnTel` varchar(255) NOT NULL COMMENT '中国电话号码',
  `re_mlyTel` varchar(255) NOT NULL COMMENT '马来西亚电话号码',
  `re_email` varchar(255) NOT NULL COMMENT '邮箱',
  `re_QQ` varchar(255) NOT NULL COMMENT 'QQ或者微信',
  `re_roadLink` varchar(200) NOT NULL,
  `re_arrivalPeople` varchar(10) NOT NULL COMMENT '到达人数',
  `re_arrivalPlace` varchar(255) NOT NULL COMMENT '到达地方',
  `re_arrivalFlight` varchar(255) NOT NULL COMMENT '到达航班',
  `re_arrivalTime` datetime NOT NULL COMMENT '到达时间',
  `re_airportService` varchar(255) NOT NULL COMMENT '机场接机服务',
  `re_spnHotel` varchar(255) NOT NULL COMMENT '仙本那酒店',
  `re_toIslandTime` datetime NOT NULL COMMENT '上岛时间',
  `re_leaveIslandTime` datetime NOT NULL COMMENT '离岛时间',
  `re_departureTime` datetime NOT NULL COMMENT '离开仙本那时间',
  `re_toAirpostService` varchar(255) NOT NULL COMMENT '返程送机服务',
  `re_departureFlight` varchar(255) NOT NULL COMMENT '返程航班号',
  `re_info` text NOT NULL COMMENT '序列化的自定义字段',
  `re_remark` text NOT NULL COMMENT '备注说明',
  `re_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0无效 1有效默认',
  `re_time` datetime NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`re_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `tra_record`
--

INSERT INTO `tra_record` (`re_id`, `re_confNumber`, `re_customer`, `re_chnTel`, `re_mlyTel`, `re_email`, `re_QQ`, `re_roadLink`, `re_arrivalPeople`, `re_arrivalPlace`, `re_arrivalFlight`, `re_arrivalTime`, `re_airportService`, `re_spnHotel`, `re_toIslandTime`, `re_leaveIslandTime`, `re_departureTime`, `re_toAirpostService`, `re_departureFlight`, `re_info`, `re_remark`, `re_status`, `re_time`) VALUES
(1, '1030', 'Zhang San', '', ' ', '121867334@qq.com', '旺旺：23y 火星妹', '马布岛路线', '3大', '斗湖', 'AK6266', '2017-06-26 16:45:00', 'Yes', 'Mabul Inn', '2017-04-27 08:00:00', '2017-04-27 16:00:00', '2017-04-28 18:00:00', 'Yes', 'AK6265', '', '', 1, '2017-06-12 15:46:36'),
(2, '1030', 'Zhang San', '', '', '121867334@qq.com', '', '海洋公园路线', '', '', '', '0000-00-00 00:00:00', '', '', '2017-04-28 08:30:00', '2017-04-28 16:00:00', '0000-00-00 00:00:00', '', '', '', '', 1, '2017-06-12 16:13:59'),
(3, 'abksldf', '1234i8989', '', '', '', '', '马布岛路线', '', '', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', 0, '0000-00-00 00:00:00'),
(4, '1', '张保星', '18028089921', '+2893247923', 'zbx@huyi.cn', '121867334', '马达京路线', '3', '仙本那', 'GA18923', '2017-08-11 00:00:00', '有', '仙本那国际大酒店', '2017-08-12 00:00:00', '2017-08-13 00:00:00', '2017-08-14 00:00:00', '有没有', 'AA128933', '', 'test', 1, '2017-08-14 17:16:46'),
(5, '9999', '香港测试', '92389892', '', '28934@12.com', '', '海洋公园路线', '1', '22', '2223', '2017-10-06 11:53:53', '33', '333', '2017-10-06 11:54:01', '2017-10-06 11:54:14', '0000-00-00 00:00:00', '', '', '', 'test', 1, '2017-10-06 11:56:12');

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
