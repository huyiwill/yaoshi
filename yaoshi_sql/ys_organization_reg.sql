/*
Navicat MySQL Data Transfer

Source Server         : my_virtual
Source Server Version : 50556
Source Host           : 192.168.254.128:3306
Source Database       : yaoshi

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2017-12-11 17:25:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ys_organization_reg`
-- ----------------------------
DROP TABLE IF EXISTS `ys_organization_reg`;
CREATE TABLE `ys_organization_reg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `operation_user_name` varchar(55) NOT NULL DEFAULT '' COMMENT '操作者的姓名',
  `phone` char(11) NOT NULL DEFAULT '0' COMMENT '注册手机号',
  `name` varchar(55) NOT NULL DEFAULT '' COMMENT '注册姓名',
  `certificate_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '学历类型 0:专科  1：本科  2硕士  3博士',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别   1女   2男',
  `province_id` varchar(55) NOT NULL DEFAULT '' COMMENT '省',
  `city_id` varchar(55) NOT NULL DEFAULT '' COMMENT '市区',
  `address` varchar(155) NOT NULL DEFAULT '' COMMENT '详细地址',
  `venue_name` varchar(75) NOT NULL DEFAULT '' COMMENT '单位',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '信息插入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='报名管理  团体注册';

-- ----------------------------
-- Records of ys_organization_reg
-- ----------------------------
