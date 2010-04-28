-- phpMyAdmin SQL Dump
-- version 3.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2010 at 12:13 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zahara`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(40) NOT NULL,
  `lastname` varchar(40) default NULL,
  `birthday` date default NULL,
  `phone_home` varchar(20) default NULL,
  `phone_work` varchar(20) default NULL,
  `movil` varchar(32) default NULL,
  `email` varchar(128) default NULL,
  `reference` varchar(40) default NULL,
  `comment` varchar(256) default NULL,
  `level_id` int(10) NOT NULL default '0',
  `last_access` datetime NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `name`, `lastname`, `birthday`, `phone_home`, `phone_work`, `movil`, `email`, `reference`, `comment`, `level_id`, `last_access`, `date`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6222715, '2010-04-28 11:32:50', '2009-12-02 15:57:46');
