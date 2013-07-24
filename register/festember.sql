-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2013 at 01:47 AM
-- Server version: 5.5.31
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `festember`
--

-- --------------------------------------------------------

--
-- Table structure for table `festember_groups`
--

CREATE TABLE IF NOT EXISTS `festember_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identification number of the group',
  `group_name` varchar(100) NOT NULL COMMENT 'Group name',
  `group_description` varchar(200) NOT NULL COMMENT 'Group description',
  `group_priority` int(11) NOT NULL DEFAULT '0' COMMENT 'Used for permissions',
  `form_id` int(11) NOT NULL DEFAULT '0' COMMENT '0 if not associated with a form',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `groupName` (`group_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `festember_groups`
--

INSERT INTO `festember_groups` (`group_id`, `group_name`, `group_description`, `group_priority`, `form_id`) VALUES
(2, 'admin', 'The Administrators', 100, 0);

-- --------------------------------------------------------

--
-- Table structure for table `festember_uploads`
--

CREATE TABLE IF NOT EXISTS `festember_uploads` (
  `page_modulecomponentid` int(11) NOT NULL,
  `page_module` varchar(128) NOT NULL,
  `upload_fileid` int(11) NOT NULL,
  `upload_filename` varchar(200) NOT NULL,
  `upload_filetype` varchar(300) NOT NULL,
  `upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` varchar(100) NOT NULL COMMENT 'The user who uploaded the file',
  PRIMARY KEY (`upload_fileid`),
  UNIQUE KEY `page_modulecomponentid` (`page_modulecomponentid`,`page_module`,`upload_filename`),
  KEY `page_module` (`page_module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `festember_uploads`
--

INSERT INTO `festember_uploads` (`page_modulecomponentid`, `page_module`, `upload_fileid`, `upload_filename`, `upload_filetype`, `upload_time`, `user_id`) VALUES
(0, 'form', 1, '7e5d2e5050ca335685982e38c4e6a2e03d97fad7.zip', 'application/zip', '2013-06-24 19:31:18', '1');

-- --------------------------------------------------------

--
-- Table structure for table `form_desc`
--

CREATE TABLE IF NOT EXISTS `form_desc` (
  `page_modulecomponentid` int(11) NOT NULL,
  `form_heading` varchar(1000) NOT NULL,
  `form_loginrequired` tinyint(1) NOT NULL DEFAULT '1',
  `form_headertext` text,
  `form_footertext` text,
  `form_expirydatetime` datetime DEFAULT NULL,
  `form_sendconfirmation` tinyint(1) NOT NULL DEFAULT '0',
  `form_usecaptcha` tinyint(1) NOT NULL DEFAULT '0',
  `form_allowuseredit` tinyint(1) NOT NULL DEFAULT '1',
  `form_allowuserunregister` tinyint(1) NOT NULL DEFAULT '0',
  `form_showuseremail` tinyint(1) NOT NULL DEFAULT '1',
  `form_showuserfullname` tinyint(1) NOT NULL DEFAULT '0',
  `form_showuserprofiledata` tinyint(1) NOT NULL DEFAULT '0',
  `form_showregistrationdate` tinyint(1) NOT NULL DEFAULT '1',
  `form_showlastupdatedate` tinyint(1) NOT NULL DEFAULT '0',
  `form_registrantslimit` int(11) NOT NULL DEFAULT '-1',
  `form_closelimit` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`page_modulecomponentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_desc`
--

INSERT INTO `form_desc` (`page_modulecomponentid`, `form_heading`, `form_loginrequired`, `form_headertext`, `form_footertext`, `form_expirydatetime`, `form_sendconfirmation`, `form_usecaptcha`, `form_allowuseredit`, `form_allowuserunregister`, `form_showuseremail`, `form_showuserfullname`, `form_showuserprofiledata`, `form_showregistrationdate`, `form_showlastupdatedate`, `form_registrantslimit`, `form_closelimit`) VALUES
(0, '', 1, 'Submit Club List', 'Submit Club List', '0000-00-00 00:00:00', 0, 0, 1, 0, 1, 0, 0, 1, 0, -1, -1);

-- --------------------------------------------------------

--
-- Table structure for table `form_elementdata`
--

CREATE TABLE IF NOT EXISTS `form_elementdata` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `page_modulecomponentid` int(11) NOT NULL DEFAULT '0',
  `form_elementid` int(11) NOT NULL DEFAULT '0',
  `form_elementdata` text NOT NULL,
  PRIMARY KEY (`user_id`,`page_modulecomponentid`,`form_elementid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_elementdata`
--

INSERT INTO `form_elementdata` (`user_id`, `page_modulecomponentid`, `form_elementid`, `form_elementdata`) VALUES
(1, 0, 0, 'asd'),
(1, 0, 1, '7e5d2e5050ca335685982e38c4e6a2e03d97fad7.zip');

-- --------------------------------------------------------

--
-- Table structure for table `form_elementdesc`
--

CREATE TABLE IF NOT EXISTS `form_elementdesc` (
  `page_modulecomponentid` int(11) NOT NULL DEFAULT '0',
  `form_elementid` int(11) NOT NULL DEFAULT '0',
  `form_elementname` varchar(1000) NOT NULL,
  `form_elementdisplaytext` varchar(5000) NOT NULL COMMENT 'Description of data held',
  `form_elementtype` enum('text','textarea','radio','checkbox','select','password','file','date','datetime') NOT NULL DEFAULT 'text',
  `form_elementsize` int(11) DEFAULT NULL,
  `form_elementtypeoptions` text,
  `form_elementdefaultvalue` varchar(4000) DEFAULT NULL,
  `form_elementmorethan` varchar(4000) DEFAULT NULL,
  `form_elementlessthan` varchar(4000) DEFAULT NULL,
  `form_elementcheckint` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Check if it is int if 1',
  `form_elementtooltiptext` text NOT NULL,
  `form_elementisrequired` tinyint(1) NOT NULL DEFAULT '0',
  `form_elementrank` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_modulecomponentid`,`form_elementid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_elementdesc`
--

INSERT INTO `form_elementdesc` (`page_modulecomponentid`, `form_elementid`, `form_elementname`, `form_elementdisplaytext`, `form_elementtype`, `form_elementsize`, `form_elementtypeoptions`, `form_elementdefaultvalue`, `form_elementmorethan`, `form_elementlessthan`, `form_elementcheckint`, `form_elementtooltiptext`, `form_elementisrequired`, `form_elementrank`) VALUES
(0, 0, 'clubname', 'Enter Club Name1', 'text', 100, '', '', '', '', 0, '', 1, 0),
(0, 1, 'submit', 'Upload Club List here', 'file', 0, 'xls|Xlxs|zip|rar|tar|tar.gz|tar.bz', '', '', '', 0, '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `form_regdata`
--

CREATE TABLE IF NOT EXISTS `form_regdata` (
  `user_id` int(11) NOT NULL,
  `page_modulecomponentid` int(11) NOT NULL,
  `form_firstupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `form_lastupdated` timestamp NULL DEFAULT NULL,
  `form_verified` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`page_modulecomponentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_regdata`
--

INSERT INTO `form_regdata` (`user_id`, `page_modulecomponentid`, `form_firstupdated`, `form_lastupdated`, `form_verified`) VALUES
(9, 0, '2012-10-13 10:05:37', '2012-10-13 10:05:37', 1),
(10, 0, '2012-10-13 11:02:37', '2012-10-13 11:02:37', 1),
(11, 0, '2012-10-13 11:11:25', '2012-10-13 11:11:25', 1),
(12, 0, '2012-11-03 16:18:33', '2012-11-03 16:18:33', 1),
(13, 0, '2012-11-06 13:41:29', '2012-11-06 13:41:29', 1),
(1, 0, '2013-06-24 19:25:04', '2013-06-24 19:31:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pragyanV3_users`
--

CREATE TABLE IF NOT EXISTS `pragyanV3_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'user identification number',
  `user_name` varchar(100) NOT NULL COMMENT 'User''s good name',
  `user_email` varchar(100) NOT NULL,
  `user_fullname` varchar(100) NOT NULL COMMENT 'User''s full name',
  `user_password` varchar(32) NOT NULL,
  `user_regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_lastlogin` datetime NOT NULL,
  `user_activated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Used for email verification',
  `user_loginmethod` enum('openid','db','ldap','imap','ads') NOT NULL DEFAULT 'db' COMMENT 'Login Method',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `pragyanV3_users`
--

INSERT INTO `pragyanV3_users` (`user_id`, `user_name`, `user_email`, `user_fullname`, `user_password`, `user_regdate`, `user_lastlogin`, `user_activated`, `user_loginmethod`) VALUES
(1, 'admin', 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', '2013-06-12 08:19:31', '2013-06-23 23:04:36', 1, 'db');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
