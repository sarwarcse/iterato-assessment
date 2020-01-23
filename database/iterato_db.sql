/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : iterato_db

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-01-23 22:19:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `points` int(2) unsigned DEFAULT 0,
  `is_done` tinyint(1) DEFAULT 0,
  `deps` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tasks
-- ----------------------------
INSERT INTO `tasks` VALUES ('1', '0', '1', 'Task 1', '5', '0', '0', '2020-01-23 13:54:43', '2020-01-23 14:00:17');
INSERT INTO `tasks` VALUES ('2', '1', '1', 'Task 1.1', '2', '0', '1', '2020-01-23 13:55:53', '2020-01-23 13:55:53');
INSERT INTO `tasks` VALUES ('3', '1', '1', 'Task 1.2', '3', '0', '1', '2020-01-23 13:56:24', '2020-01-23 13:56:24');
INSERT INTO `tasks` VALUES ('4', '1', '1', 'Task 1.3', '5', '1', '1', '2020-01-23 14:00:17', '2020-01-23 21:09:50');
INSERT INTO `tasks` VALUES ('5', '0', '2', 'Task 2', '10', '0', '0', '2020-01-23 14:02:13', '2020-01-23 14:04:33');
INSERT INTO `tasks` VALUES ('6', '5', '2', 'Task 2.1', '7', '0', '1', '2020-01-23 14:04:08', '2020-01-23 20:04:55');
INSERT INTO `tasks` VALUES ('7', '5', '2', 'Task 2.2', '5', '0', '1', '2020-01-23 14:04:33', '2020-01-23 20:04:57');
INSERT INTO `tasks` VALUES ('8', '0', '3', 'Task 3', '10', '0', '0', '2020-01-23 14:05:55', '2020-01-23 14:09:31');
INSERT INTO `tasks` VALUES ('9', '8', '3', 'Task 3.1', '5', '0', '1', '2020-01-23 14:06:16', '2020-01-23 14:06:16');
INSERT INTO `tasks` VALUES ('10', '8', '3', 'Task 3.2', '5', '0', '1', '2020-01-23 14:06:27', '2020-01-23 14:07:23');
INSERT INTO `tasks` VALUES ('11', '10', '3', 'Task 3.2.1', '2', '0', '2', '2020-01-23 14:06:53', '2020-01-23 14:06:53');
INSERT INTO `tasks` VALUES ('12', '10', '3', 'Task 3.2.2', '2', '0', '2', '2020-01-23 14:07:23', '2020-01-23 14:07:23');
INSERT INTO `tasks` VALUES ('13', '8', '3', 'Task 3.3', '2', '0', '1', '2020-01-23 14:08:50', '2020-01-23 14:09:31');
INSERT INTO `tasks` VALUES ('14', '13', '3', 'Task 3.3.1', '2', '1', '2', '2020-01-23 14:09:31', '2020-01-23 20:43:23');
