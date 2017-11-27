/*
 Navicat Premium Data Transfer

 Source Server         : centeOS_6.5
 Source Server Type    : MySQL
 Source Server Version : 50548
 Source Host           : 10.211.55.5
 Source Database       : yaoshi

 Target Server Type    : MySQL
 Target Server Version : 50548
 File Encoding         : utf-8

 Date: 11/27/2017 23:57:29 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `ys_meet_register`
-- ----------------------------
DROP TABLE IF EXISTS `ys_meet_register`;
CREATE TABLE `ys_meet_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_user_id` int(11) DEFAULT '0' COMMENT '注册用户id',
  `reg_operation_user_id` int(11) DEFAULT '0' COMMENT '注册操作者的用户ID',
  `reg_user_name` varchar(255) DEFAULT '' COMMENT '注册用户姓名',
  `reg_user_phone` char(11) DEFAULT '' COMMENT '注册用户的手机号码',
  `reg_user_bachelor` varchar(75) DEFAULT '' COMMENT '学历',
  `reg_user_sex` tinyint(1) DEFAULT '0' COMMENT '注册用户性别',
  `reg_user_country` varchar(75) DEFAULT '' COMMENT '注册用户国家',
  `reg_user_city` varchar(75) DEFAULT '' COMMENT '注册用户的城市',
  `reg_user_unit` varchar(155) DEFAULT '' COMMENT '注册用户的单位',
  `reg_certify_data` varchar(255) DEFAULT '' COMMENT '认证资料',
  `reg_pay_status` tinyint(1) DEFAULT '0' COMMENT '1已缴纳   0没有缴纳',
  `reg_vip` tinyint(1) DEFAULT '0' COMMENT '是否VIP  0不是  1是',
  `is_issue_invoice` tinyint(1) DEFAULT '0' COMMENT '0没有开发票   1开了发票',
  `reg_time` int(11) DEFAULT '0' COMMENT '注册时间',
  PRIMARY KEY (`id`),
  KEY `reg_name` (`reg_user_name`) USING BTREE,
  KEY `reg_pay` (`reg_pay_status`) USING BTREE,
  KEY `reg_vip` (`reg_vip`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
