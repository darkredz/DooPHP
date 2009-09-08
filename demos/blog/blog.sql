-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 08, 2009 at 06:39 PM
-- Server version: 5.1.33
-- PHP Version: 5.2.9-2
-- 
-- Database: `blog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `comment`
-- 

CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned DEFAULT NULL,
  `author` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `content` varchar(145) NOT NULL,
  `url` varchar(128) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT 'Need to moderate comments\n0=unmoderated comment\n1=comment is OK',
  PRIMARY KEY (`id`),
  KEY `fk_comment_post` (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- 
-- Dumping data for table `comment`
-- 

INSERT INTO `comment` (`id`, `post_id`, `author`, `email`, `content`, `url`, `createtime`, `status`) VALUES 
(14, 10, 'Name with NoWebsite', 'abc@abc.com.my', 'DooPHP is cool~', NULL, '2009-09-09 02:27:28', 1),
(13, 10, 'Leng Again', 'abc@abc.com', 'This is another test :D ', 'http://dkrd.net', '2009-09-09 02:18:37', 1),
(12, 10, 'Leng', 'abc@abc.com', 'This is a cool intro', 'http://doophp.com', '2009-09-09 02:14:25', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `post`
-- 

CREATE TABLE `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(145) NOT NULL,
  `content` text NOT NULL,
  `createtime` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0=draft\n1=published',
  `totalcomment` smallint(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- 
-- Dumping data for table `post`
-- 

INSERT INTO `post` (`id`, `title`, `content`, `createtime`, `status`, `totalcomment`) VALUES 
(1, 'This is first post', 'hahahah', '2009-07-03 04:27:41', 1, 0),
(2, 'Another one', 'googogog', '2009-08-26 04:27:41', 1, 0),
(3, 'Another thing to say', 'Whoa another post here , you are superb!', '2009-08-16 05:15:11', 1, 0),
(4, 'Good Readings', 'DooPHP is super fun to work with!!~~', '2009-09-03 05:15:11', 1, 0),
(5, 'What is ORM?', '<p><b>Object-relational mapping</b> (<b>ORM</b>, <b>O/RM</b>, and <b>O/R mapping</b>) in computer software is a <a title="Computer programming" href="/wiki/Computer_programming">programming</a> technique for converting data between incompatible <a title="Type system" href="/wiki/Type_system">type systems</a> in <a title="Relational database" href="/wiki/Relational_database">relational databases</a> and <a class="mw-redirect" title="Object-oriented" href="/wiki/Object-oriented">object-oriented</a> programming languages. This creates, in effect, a "virtual <a title="Object database" href="/wiki/Object_database">object database</a>" that can be used from within the programming language. There are both free and commercial packages available that perform object-relational mapping, although some programmers opt to create their own ORM tools.</p>\r\n\r\n<p><a title="Data management" href="/wiki/Data_management">Data management</a> tasks in object-oriented (OO) programming are typically implemented by manipulating <a title="Object (computer science)" href="/wiki/Object_(computer_science)">objects</a> that are almost always non-scalar values. For example, consider an address book entry that represents a single person along with zero or more phone numbers and zero or more addresses. This could be modeled in an object-oriented implementation by a "person <a class="mw-redirect" title="Object (computing)" href="/wiki/Object_(computing)">object</a>" with "slots" to hold the data that comprise the entry: the person''s name, a list of phone numbers, and a list of addresses. The list of phone numbers would itself contain "phone number objects" and so on. The address book entry is treated as a single value by the programming language (it can be referenced by a single variable, for instance). Various methods can be associated with the object, such as a method to return the preferred phone number, the home address, and so on.</p>\r\n\r\n<p>However, many popular database products such as structured query language database management systems (<a title="SQL" href="/wiki/SQL">SQL</a> <a class="mw-redirect" title="DBMS" href="/wiki/DBMS">DBMS</a>) can only store and manipulate <a title="Scalar (computing)" href="/wiki/Scalar_(computing)">scalar</a> values such as integers and strings organized within <a title="Database normalization" href="/wiki/Database_normalization">normalized</a> <a title="Table (database)" href="/wiki/Table_(database)">tables</a>. The programmer must either convert the object values into groups of simpler values for storage in the database (and convert them back upon retrieval), or only use simple scalar values within the program. Object-relational mapping is used to implement the first approach.</p>\r\n\r\n<p>The heart of the problem is translating those objects to forms that can be stored in the database for easy retrieval, while preserving the properties of the objects and their relationships; these objects are then said to be <a title="Persistence (computer science)" href="/wiki/Persistence_(computer_science)">persistent</a>.</p>', '2009-09-03 05:15:53', 1, 0),
(6, 'Good PHP Code', 'DooPHP is super Cool to work with!!~~', '2009-09-03 05:15:53', 1, 0),
(7, 'Sipadan Island', '<p><b>Sipadan</b> is the only <i><a title="Island" href="/wiki/Island">oceanic island</a></i> in <a title="Malaysia" href="/wiki/Malaysia">Malaysia</a>, rising 600 metres (2,000 ft) from the seabed. It is located in the <a title="Celebes Sea" href="/wiki/Celebes_Sea">Celebes Sea</a> east of the major town of <a title="Tawau" href="/wiki/Tawau">Tawau</a> and off the coast of <a title="East Malaysia" href="/wiki/East_Malaysia">East Malaysia</a> on the Island of <a title="Borneo" href="/wiki/Borneo">Borneo</a>. It was formed by living <a title="Coral" href="/wiki/Coral">corals</a> growing on top of an extinct <i><a title="Volcano" href="/wiki/Volcano">volcanic cone</a></i> that took thousands of years to develop. Sipadan is located at the heart of the <a title="Indo-Pacific" href="/wiki/Indo-Pacific">Indo-Pacific basin</a>, the centre of one of the richest marine habitats in the world. More than 3,000 species of fish and hundreds of coral species have been classified in this ecosystem.</p>\r\n\r\n<p>Normally rare diving scenes are frequently seen in the waters around Sipadan: schools of <a title="Green turtle" href="/wiki/Green_turtle">green</a> and <a title="Hawksbill turtle" href="/wiki/Hawksbill_turtle">hawksbill turtles</a> nesting and mating, schools of <a title="Barracuda" href="/wiki/Barracuda">barracuda</a> and <a title="Carangidae" href="/wiki/Carangidae">big-eye trevally</a> in tornado-like formations, pelagic species such as <a class="mw-redirect" title="Manta rays" href="/wiki/Manta_rays">manta rays</a>, <a title="Eagle ray" href="/wiki/Eagle_ray">eagle rays</a>, <a title="Hammerhead shark" href="/wiki/Hammerhead_shark">scalloped hammerhead sharks</a> and <a class="mw-redirect" title="Whale sharks" href="/wiki/Whale_sharks">whale sharks</a>.</p>\r\n\r\n<p>A mysterious turtle tomb lies underneath the column of the island, formed by an underwater <a title="Limestone" href="/wiki/Limestone">limestone</a> <a title="Cave" href="/wiki/Cave">cave</a> with a labyrinth of tunnels and chambers that contain many skeletal remains of turtles that have become lost and drown before finding the surface. <a rel="nofollow" title="http://outdoors.webshots.com/album/558956047fLkUvv" class="external autonumber" href="http://outdoors.webshots.com/album/558956047fLkUvv">[1]</a></p>\r\n\r\n<p>In year 2004, the Government of Malaysia ordered all on-site dive and resort operators of Sipadan to move their structures out of the island by <span title="2004-12-31" class="mw-formatted-date"><a title="December 31" href="/wiki/December_31">31 December</a> <a title="2004" href="/wiki/2004">2004</a></span>. This move is mainly to conserve a balanced ecosystem for Sipadan and its surrounding.<sup style="white-space: nowrap;" title="This claim needs references to reliable sources from September 2008" class="noprint Template-Fact">[<i><a title="Wikipedia:Citation needed" href="/wiki/Wikipedia:Citation_needed">citation needed</a></i>]</sup>.</p>\r\n\r\n<p>Diving will continue to be allowed in Sipadan for divers who are ferried in and out by dive and resort operators from the mainland and surrounding islands. However, tourists and keen divers should be warned that the number of permits available for Sipadan each day is limited to 120 spread between 12 resorts. A visit to Sipadan is not only not guaranteed for guests at the resort, regardless of the length of stay, but it is highly unlikely for those who stay less than a week or who want to snorkel rather than dive. Please keep this in mind to avoid disappointment.</p>\r\n\r\n<p>If you are lucky enough to get to dive at Sipadan, you''ll experience world class diving, and maybe the most known diving spot is the Barracuda Point, where during the morning dive you''ll often encounter a very large school of <a title="Barracuda" href="/wiki/Barracuda">Barracuda</a> or Big Eye Trevallies. This is only one of many rare experiences you''ll have diving the reef off Sipadan island. There will be a lot of <a class="mw-redirect" title="Green Turtle" href="/wiki/Green_Turtle">Green Turtle</a>, Hawkbill Turtle and <a title="Whitetip reef shark" href="/wiki/Whitetip_reef_shark">Whitetip reef shark</a> and even the rare encounter of <a class="mw-redirect" title="Hammerhead sharks" href="/wiki/Hammerhead_sharks">Hammerhead sharks</a>.</p>', '2009-09-03 05:16:38', 1, 0),
(8, 'This is draft', 'Draft wont be show', '2009-09-03 05:16:38', 0, 0),
(9, 'Redang Island', '<img height="188" width="250" class="thumbimage" src="http://upload.wikimedia.org/wikipedia/commons/thumb/2/27/Pasir_Panjang.JPG/250px-Pasir_Panjang.JPG" alt="" style="float:left;padding-right:15px"/><p><b>Redang Island</b>, locally known as <b>Pulau Redang</b> or just "Redang" is one of the largest islands off the east coast of <a title="Malaysia" href="/wiki/Malaysia">Malaysia</a>. It is a popular holiday island for Malaysians, most of whom come on package deals to one of the resorts. Redang is one of nine islands, which form a marine park, and which offer snorkeling and diving opportunities.</p>\r\n<p>The island is also an important conservation site for <a href="/wiki/Sea_turtle" title="Sea turtle">sea turtles</a>. Previously, the indiscriminate economic exploitation of turtle eggs had caused fewer turtles returning to nest on the island. This has led the Terengganu state government to set up the <b>Koperasi Setiajaya Pulau Redang</b> in 1989, a cooperative aiming to develop and manage socio-economic programmes that could improve the livelihood of Pulau Redang locals without endangering its natural resources.</p>\r\n\r\n<p>The Pulau Redang <a href="/wiki/Archipelago" title="Archipelago">archipelago</a> comprises Pulau Redang, Pulau Lima, Pulau Paku Besar, Pulau Paku Kecil, Pulau Kerengga Kecil, Pulau Kerengga Besar, Pulau Ekor Tebu, Pulau Ling and Pulau Pinang. Pulau Redang is the biggest of all the islands in the <a href="/wiki/Marine_Park" title="Marine Park">Marine Park</a>, measuring about 7 km long and 6 km wide. Its highest peak is Bukit Besar at 359 metres above sea level. The boundary of the Pulau Redang Marine Park is established by a line linking all points 2 nautical miles (3.7 km) from the shores of Pulau Redang, Pulau Lima, Pulau Ekor Tebu and Pulau Pinang. The other nearby islands of <a href="/wiki/Perhentian_Islands" title="Perhentian Islands">Pulau Perhentian Besar, Pulau Perhentian Kecil</a>, <a href="/wiki/Lang_Tengah_Island" title="Lang Tengah Island">Pulau Lang Tengah</a>, <a href="/wiki/Kapas_Island" title="Kapas Island">Pulau Kapas</a> and Pulau Susu Dara are also gazetted and protected as Marine Parks. Today, only the bigger islands like Redang, Lang Tengah, Perhentian and Kapas have resort facilities for visitors. The management of Marine Parks primarily involves protection of the sensitive marine and terrestrial ecosystems by controlling the impact from human activities. These include waste & pollution management and conservation of <a href="/wiki/Coral_reef" title="Coral reef">coral reefs</a> and terrestrial habitats.</p>\r\n<p>The 2000 film, <a href="/wiki/Summer_Holiday_(2000_film)" title="Summer Holiday (2000 film)">Summer Holiday</a> was filmed on the Laguna Redang resort, and a replica of the tea house now serves as the resort''s gift shop.</p>', '2009-09-03 05:17:54', 1, 0),
(10, 'MVC is Cool', '<p><b>Model–View–Controller</b> (MVC) is an <a title="Architectural pattern (computer science)" href="/wiki/Architectural_pattern_(computer_science)">architectural pattern</a> used in <a title="Software engineering" href="/wiki/Software_engineering">software engineering</a>. The pattern isolates <a title="Business logic" href="/wiki/Business_logic">business logic</a> from input and <a title="Presentation" href="/wiki/Presentation">presentation</a>, permitting <a title="Separation of concerns" href="/wiki/Separation_of_concerns">independent development, testing and maintenance of each</a>.</p>\r\n\r\n<p>An MVC application is a collection of model/view/controller triplets (a central dispatcher is often used to delegate controller actions to a view-specific controller). Each model is associated with one or more views (projections) suitable for presentation (not necessarily visual presentation). When a model changes its state, it notifies its associated views so they can refresh. The controller is responsible for initiating change requests and providing any necessary data inputs to the model.</p>\r\n\r\n<p>It is not necessary to have a graphical user interface to implement MVC. Rendering views graphically is one application of MVC, but it is not part of the pattern definition. A <a title="Business-to-business" href="/wiki/Business-to-business">business-to-business</a> interface can leverage an MVC architecture equally well.</p>', '2009-09-05 05:17:54', 1, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `post_tag`
-- 

CREATE TABLE `post_tag` (
  `tag_id` int(11) unsigned NOT NULL,
  `post_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`,`post_id`),
  KEY `fk_tag_has_post_tag` (`tag_id`),
  KEY `fk_tag_has_post_post` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `post_tag`
-- 

INSERT INTO `post_tag` (`tag_id`, `post_id`) VALUES 
(1, 2),
(1, 5),
(1, 10),
(2, 6),
(2, 10),
(3, 4),
(3, 8),
(4, 4),
(4, 8),
(5, 6),
(5, 8),
(5, 10),
(6, 4),
(6, 16),
(8, 7),
(8, 9),
(8, 17),
(9, 4),
(9, 7),
(9, 9),
(9, 16),
(9, 17);

-- --------------------------------------------------------

-- 
-- Table structure for table `tag`
-- 

CREATE TABLE `tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(145) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `tag`
-- 

INSERT INTO `tag` (`id`, `name`) VALUES 
(1, 'tutorial'),
(2, 'mvc'),
(3, 'php'),
(4, 'framework'),
(5, 'software'),
(6, 'web 2.0'),
(7, 'doophp'),
(8, 'tourism'),
(9, 'Malaysia');
