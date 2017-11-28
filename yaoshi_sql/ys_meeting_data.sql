/*
Navicat MySQL Data Transfer

Source Server         : my_virtual
Source Server Version : 50556
Source Host           : 192.168.254.128:3306
Source Database       : yaoshi

Target Server Type    : MYSQL
Target Server Version : 50556
File Encoding         : 65001

Date: 2017-11-28 11:33:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ys_meeting_data`
-- ----------------------------
DROP TABLE IF EXISTS `ys_meeting_data`;
CREATE TABLE `ys_meeting_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meet_data_id` int(11) NOT NULL DEFAULT '0' COMMENT '会议资料ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `meet_data_name` varchar(255) NOT NULL DEFAULT '' COMMENT '会议资料名称',
  `meet_data_url` varchar(300) NOT NULL DEFAULT '' COMMENT '会议资料地址',
  `meet_data_url1` varchar(300) NOT NULL DEFAULT '',
  `meet_data_url2` varchar(300) NOT NULL DEFAULT '',
  `meet_data_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会议资料状态( 0:未上传  1:已上传  )',
  `meet_data_operation_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会议资料操作状态( 0: 正常  1:禁用  2: 可删除 )',
  `meet_data_insert_time` int(11) NOT NULL DEFAULT '0' COMMENT '会议资料插入时间',
  `meet_data_update_time` int(11) NOT NULL DEFAULT '0' COMMENT '会议资料 状态更新时间( 禁止时间 )',
  PRIMARY KEY (`id`),
  KEY `meet_data_name` (`meet_data_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会议资料表（用户端）';

-- ----------------------------
-- Records of ys_meeting_data
-- ----------------------------
INSERT INTO `ys_meeting_data` VALUES ('1', '0', '0', 'asdfas', '', '', '', '0', '0', '0', '0');
