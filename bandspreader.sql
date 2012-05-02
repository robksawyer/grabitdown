-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 02, 2012 at 12:44 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bandspreader`
--

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` VALUES(130, 11, 'kmtj5y8f37e1n96upbhlivxzc', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(129, 11, 'ezrmis3lt86uf4h20ojpvbqdx', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(128, 11, 'yb41gwtu73zc2q95kjsldmfpn', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(127, 11, '8u90hm5zpydbef6qtk1nw3j2a', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(126, 11, 'fsa5ldj4kz289ho1gxqpnerw0', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(125, 11, 'bm73p52h8zy9s6vgn4qoejk0x', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(124, 11, 'ny9polem1x4da5wqftbu830hv', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(123, 11, '842wmkja1tg0svpqohbeir5l9', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(122, 11, 'oi9vf6xbnl7sthk08m5ju143r', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');
INSERT INTO `codes` VALUES(121, 11, 'djl5caeon7ft1ygrvpw2sh83i', 1, '2012-05-02 19:09:56', '2012-05-02 19:09:56');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `path_alt` varchar(255) DEFAULT NULL,
  `caption` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `filesize` varchar(255) NOT NULL,
  `ext` varchar(10) NOT NULL,
  `group` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `user_id` char(36) NOT NULL,
  `total_codes` int(11) NOT NULL,
  `test_token` varchar(255) NOT NULL,
  `test_token_count` int(10) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` VALUES(11, '07 1nce Again.mp3', '/files/uploads/sometext/07_1nce_Again.mp3', NULL, '07 1nce Again.mp3', 'audio/mp3', 5597331, '5 MB', 'mp3', 'audio', '07_1nce_again_mp3', '4fa182c7-fbbc-445a-8962-07c8bd22bb5a', 10, '759a6d9a24ade2f9c1180702740f10fe', 0, 1, '2012-05-02 18:53:59', '2012-05-02 18:53:59', '2012-05-02 18:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL COMMENT 'uuid',
  `fullname` varchar(255) NOT NULL,
  `custom_path` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `passwd` varchar(128) DEFAULT NULL,
  `password_token` varchar(128) DEFAULT NULL,
  `tmp_passwd` varchar(128) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_authenticated` tinyint(1) NOT NULL DEFAULT '0',
  `email_token` varchar(255) DEFAULT NULL,
  `email_token_expires` datetime DEFAULT NULL,
  `tos` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `username` (`custom_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES('4fa182c7-fbbc-445a-8962-07c8bd22bb5a', 'Rob', 'sometext', 'sometext', '7c78dcbb23b52b557f57d0e48280b5aa38ca45ac', NULL, NULL, 'test@test.com', 0, 'y24a3dz6so', '2012-05-03 18:53:59', 1, 0, NULL, NULL, 0, 'user', '2012-05-02 18:53:59', '2012-05-02 18:53:59');
