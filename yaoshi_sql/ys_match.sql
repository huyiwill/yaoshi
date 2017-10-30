/*
Navicat MySQL Data Transfer

Source Server         : my_virtual
Source Server Version : 50556
Source Host           : 192.168.254.128:3306
Source Database       : yaoshi

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2017-10-30 16:10:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ys_match`
-- ----------------------------
DROP TABLE IF EXISTS `ys_match`;
CREATE TABLE `ys_match` (
  `match_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_name` varchar(255) NOT NULL DEFAULT '' COMMENT '比赛名称',
  `match_banner` varchar(255) NOT NULL DEFAULT '',
  `match_logo` varchar(255) NOT NULL DEFAULT '',
  `match_start_time` int(11) NOT NULL DEFAULT '0' COMMENT '比赛开始时间',
  `match_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '比赛借宿时间',
  `match_signup_start_time` int(11) NOT NULL DEFAULT '0' COMMENT '报名开始时间',
  `match_signup_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '比赛报名结束时间',
  `match_zone` varchar(255) NOT NULL DEFAULT '' COMMENT '参赛地区',
  `match_people` varchar(255) NOT NULL DEFAULT '' COMMENT '参赛人员',
  `match_review_signup` tinyint(1) NOT NULL DEFAULT '0' COMMENT '报名审核',
  `match_real_auth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '实名认证',
  `match_bottom_content` text NOT NULL COMMENT '底部内容',
  `match_introduce` tinyint(1) NOT NULL DEFAULT '0' COMMENT '比赛介绍',
  `display_location` tinyint(1) NOT NULL DEFAULT '0' COMMENT '页面展示位置',
  `match_content` text NOT NULL COMMENT '大赛简介内容',
  PRIMARY KEY (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ys_match
-- ----------------------------
