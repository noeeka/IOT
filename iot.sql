/*
Navicat MariaDB Data Transfer

Source Server         : Localhost
Source Server Version : 100137
Source Host           : 127.0.0.1:3306
Source Database       : iot

Target Server Type    : MariaDB
Target Server Version : 100137
File Encoding         : 65001

Date: 2018-12-02 11:35:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for base_department_info
-- ----------------------------
DROP TABLE IF EXISTS `base_department_info`;
CREATE TABLE `base_department_info` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '部门自增id',
  `name` varchar(255) DEFAULT NULL COMMENT '部门名称',
  `parent_id` int(32) DEFAULT NULL COMMENT '上级部门',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of base_department_info
-- ----------------------------

-- ----------------------------
-- Table structure for base_permission_info
-- ----------------------------
DROP TABLE IF EXISTS `base_permission_info`;
CREATE TABLE `base_permission_info` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `base_role_info_id` int(32) DEFAULT NULL COMMENT '角色id',
  `permission` text COMMENT '权限列表',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `roleID` (`base_role_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of base_permission_info
-- ----------------------------

-- ----------------------------
-- Table structure for base_role_info
-- ----------------------------
DROP TABLE IF EXISTS `base_role_info`;
CREATE TABLE `base_role_info` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `name` varchar(30) DEFAULT NULL COMMENT '角色名称',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of base_role_info
-- ----------------------------

-- ----------------------------
-- Table structure for base_user_info
-- ----------------------------
DROP TABLE IF EXISTS `base_user_info`;
CREATE TABLE `base_user_info` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `base_role_info_id` int(32) DEFAULT NULL COMMENT '角色id',
  `base_department_info_id` int(32) DEFAULT NULL COMMENT '部门id',
  `name` varchar(50) NOT NULL COMMENT '账号',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `privilege` tinyint(2) NOT NULL COMMENT '权限等级 1：超级管理员,2：管理员',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `roleID` (`base_role_info_id`),
  KEY `base_department_info_id` (`base_department_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of base_user_info
-- ----------------------------
INSERT INTO `base_user_info` VALUES ('1', '1', '1', '11', '11', '1', null);

-- ----------------------------
-- Table structure for operate_record
-- ----------------------------
DROP TABLE IF EXISTS `operate_record`;
CREATE TABLE `operate_record` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `base_user_info_id` int(32) DEFAULT NULL COMMENT '操作者id',
  `content` text NOT NULL COMMENT '操作内容',
  `datetime` int(32) DEFAULT NULL COMMENT '操作时间',
  `ip` varchar(50) DEFAULT NULL COMMENT '登录IP',
  PRIMARY KEY (`id`),
  KEY `user_id` (`base_user_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='登录登出记录表';

-- ----------------------------
-- Records of operate_record
-- ----------------------------
INSERT INTO `operate_record` VALUES ('1', '1', '1', '1', null);

-- ----------------------------
-- Table structure for ticket
-- ----------------------------
DROP TABLE IF EXISTS `ticket`;
CREATE TABLE `ticket` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT COMMENT '工单自增id',
  `name` varchar(255) DEFAULT NULL COMMENT '工单名称',
  `ticket_type` varchar(255) DEFAULT NULL COMMENT '工单类型',
  `facilities_type` varchar(255) DEFAULT NULL COMMENT '设备类型',
  `base_user_info_id` int(32) DEFAULT NULL COMMENT '发布人id',
  `publisher` varchar(255) DEFAULT NULL COMMENT '发布人名字',
  `operator` varchar(255) DEFAULT NULL COMMENT '操作人或部门',
  `start_time` int(32) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(32) DEFAULT NULL COMMENT '结束时间',
  `timeout_reminder` int(1) DEFAULT NULL COMMENT '超时提醒',
  `remark` text COMMENT '备注',
  `status` varchar(50) DEFAULT NULL COMMENT '状态',
  `create_time` int(32) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `base_user_info_id` (`base_user_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ticket
-- ----------------------------
