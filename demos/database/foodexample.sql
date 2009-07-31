-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 20, 2009 at 05:57 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0
-- 
-- Database: `foodexample`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `article`
-- 

CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(65) NOT NULL,
  `content` text NOT NULL,
  `createtime` datetime NOT NULL,
  `draft` tinyint(1) DEFAULT '0',
  `food_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_article_food` (`food_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `article`
-- 

INSERT INTO `article` (`id`, `title`, `content`, `createtime`, `draft`, `food_id`) VALUES 
(1, 'Nasi Lemak Review', 'This is a review for nasi lemak, a common for a Malaysian.', '2009-07-15 21:56:40', 0, 4),
(2, 'Nasi Lemak Review hmm..', 'This is a review for nasi lemak, a common for a Malaysian. No this is just a draft.', '2009-07-20 21:56:40', 1, 4),
(3, 'More on Nasi Lemak', 'This is something additional to Nasi Lemak.....', '2009-07-20 21:57:35', 0, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `food`
-- 

CREATE TABLE `food` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(65) NOT NULL,
  `food_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_food_food_type` (`food_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- 
-- Dumping data for table `food`
-- 

INSERT INTO `food` (`id`, `name`, `description`, `location`, `food_type_id`) VALUES 
(6, 'Wan Tan Mee', 'Wonton noodle or wantan mee is a Cantonese noodle dish which is popular in Hong Kong, Malaysia, and Singapore.', 'Malaysia', 1),
(5, 'Asam Laksa', 'Sour & spicy noodles', 'Malaysia', 1),
(4, 'Nasi Lemak', 'A kind of rice made with coconut milk', 'Malaysia', 4),
(7, 'Bak Kut Teh', 'A Chinese soup popularly served in Malaysia, Singapore originated in Klang valley. Usually served with pork but sometimes seafood & even vegetarian style', 'Malaysia', 3),
(14, 'Ban Mian', 'A type of delicious noodles which is popular in KL', 'Malaysia', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `food_has_ingredient`
-- 

CREATE TABLE `food_has_ingredient` (
  `food_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`food_id`,`ingredient_id`),
  KEY `fk_food_has_incredient_food` (`food_id`),
  KEY `fk_food_has_incredient_incredient` (`ingredient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `food_has_ingredient`
-- 

INSERT INTO `food_has_ingredient` (`food_id`, `ingredient_id`) VALUES 
(4, 9),
(4, 11),
(4, 22),
(5, 1),
(5, 3),
(5, 4),
(6, 1),
(6, 2),
(7, 1),
(7, 14),
(7, 15),
(7, 23),
(14, 22);

-- --------------------------------------------------------

-- 
-- Table structure for table `food_type`
-- 

CREATE TABLE `food_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `food_type`
-- 

INSERT INTO `food_type` (`id`, `name`) VALUES 
(1, 'Noodles'),
(2, 'Bread'),
(3, 'Meat'),
(4, 'Rice');

-- --------------------------------------------------------

-- 
-- Table structure for table `ingredient`
-- 

CREATE TABLE `ingredient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- 
-- Dumping data for table `ingredient`
-- 

INSERT INTO `ingredient` (`id`, `name`) VALUES 
(1, 'water'),
(2, 'oil'),
(3, 'shredded fish'),
(4, 'mackerel'),
(5, 'onion'),
(6, 'salt'),
(7, 'ginger'),
(8, 'fine egg noodles'),
(9, 'coconut milk'),
(10, 'pandan leaves'),
(11, 'rice'),
(12, 'sweet chilli paste'),
(13, 'cucumber'),
(14, 'bak kut teh herbs'),
(15, 'Chinese white cabbage'),
(16, 'mushrooms'),
(17, 'soy sauce'),
(22, 'ikan bilis'),
(23, 'garlic');

-- --------------------------------------------------------

-- 
-- Table structure for table `recipe`
-- 

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `food_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_recipe_food` (`food_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `recipe`
-- 

INSERT INTO `recipe` (`id`, `description`, `food_id`) VALUES 
(1, 'Bring water to a boil. Add all the herbs and simmer for 30 minutes. Add (fried) vegetarian rib bones, meatballs and the dried and button mushrooms. Cook for 10 minutes. Add wong nga pak and golden mushrooms and cook for five to six minutes.', 7);
