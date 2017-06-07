SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0',
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `patient_name` varchar(150) DEFAULT NULL,
  `country` varchar(150) DEFAULT NULL,
  `video_code` varchar(150) DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for comment_category
-- ----------------------------
DROP TABLE IF EXISTS `comment_category`;
CREATE TABLE `comment_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for comment_translate
-- ----------------------------
DROP TABLE IF EXISTS `comment_translate`;
CREATE TABLE `comment_translate` (
  `translate_id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL DEFAULT '0',
  `comment_id` int(11) NOT NULL DEFAULT '0',
  `country` varchar(150) DEFAULT NULL,
  `video_code` varchar(150) DEFAULT NULL,
  `comment` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`translate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for language
-- ----------------------------
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `lang_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` bit(1) NOT NULL DEFAULT b'0',
  `language` varchar(100) DEFAULT NULL,
  `short_code` varchar(2) DEFAULT NULL,
  `long_code` varchar(5) DEFAULT NULL,
  `friendly_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
