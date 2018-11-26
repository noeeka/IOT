/*
Navicat MySQL Data Transfer

Source Server         : 192.168.1.254
Source Server Version : 50560
Source Host           : 127.0.0.1:3306
Source Database       : kangkang

Target Server Type    : MYSQL
Target Server Version : 50560
File Encoding         : 65001

Date: 2018-11-26 18:16:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `passwd` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for appoint
-- ----------------------------
DROP TABLE IF EXISTS `appoint`;
CREATE TABLE `appoint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_id` int(11) NOT NULL,
  `time` varchar(100) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(4) NOT NULL,
  `per_id` int(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for base_case
-- ----------------------------
DROP TABLE IF EXISTS `base_case`;
CREATE TABLE `base_case` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '基础病例自增ID',
  `patient_info_id` int(32) DEFAULT NULL COMMENT '患者ID 关联patient_info表',
  `times` int(11) DEFAULT NULL COMMENT '0-一次扫描 1-多次扫描',
  `project` varchar(255) DEFAULT NULL COMMENT '检查项目',
  `medical_history_id` int(32) DEFAULT NULL COMMENT '既往病史ID',
  `medical_current_id` int(32) DEFAULT NULL COMMENT '现病史ID',
  `personal_history` int(2) DEFAULT '0' COMMENT '个人史 0：无 1：抽烟 2：喝酒 3：其他',
  `obsterical_history` varchar(50) DEFAULT NULL COMMENT '婚育史 spinsterhood：未婚  married：已婚',
  `family_history` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT '家族史 0：无 文字性描述为家族史的详细信息',
  PRIMARY KEY (`id`),
  KEY `medical_history_id` (`medical_history_id`),
  KEY `medical_current_id` (`medical_current_id`),
  KEY `base_case_patient_id` (`patient_info_id`),
  CONSTRAINT `base_case_patient_id` FOREIGN KEY (`patient_info_id`) REFERENCES `patient_info` (`id`),
  CONSTRAINT `medical_current_id` FOREIGN KEY (`medical_current_id`) REFERENCES `medical_current` (`id`),
  CONSTRAINT `medical_history_id` FOREIGN KEY (`medical_history_id`) REFERENCES `medical_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='患者基础病例表 包括既往病史关联ID，现病史关联ID，个人史，婚育史和家族史';

-- ----------------------------
-- Table structure for batch
-- ----------------------------
DROP TABLE IF EXISTS `batch`;
CREATE TABLE `batch` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `patient_info_id` int(32) DEFAULT NULL COMMENT '患者ID',
  `datetime` int(32) DEFAULT NULL COMMENT '检查时间',
  `status` int(32) DEFAULT NULL COMMENT '是否医生确认 0：未确认 1：已确认',
  PRIMARY KEY (`id`),
  KEY `batch_report_id` (`patient_info_id`) USING BTREE,
  CONSTRAINT `batch_report_id` FOREIGN KEY (`patient_info_id`) REFERENCES `patient_info` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COMMENT='检查批次 关联影像记录表和诊断报告表';

-- ----------------------------
-- Table structure for codes
-- ----------------------------
DROP TABLE IF EXISTS `codes`;
CREATE TABLE `codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(50) NOT NULL,
  `p_id` int(11) NOT NULL,
  `zan` int(11) NOT NULL,
  `cang` int(11) NOT NULL,
  `p_chat` varchar(255) CHARACTER SET utf8 NOT NULL,
  `c_chat` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `xp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for dateflow
-- ----------------------------
DROP TABLE IF EXISTS `dateflow`;
CREATE TABLE `dateflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dates` varchar(100) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for doctor
-- ----------------------------
DROP TABLE IF EXISTS `doctor`;
CREATE TABLE `doctor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tel` varchar(30) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `sex` varchar(20) CHARACTER SET utf8 NOT NULL,
  `info` text CHARACTER SET utf8 NOT NULL,
  `job` varchar(50) CHARACTER SET utf8 NOT NULL,
  `addr` varchar(200) CHARACTER SET utf8 NOT NULL,
  `welcome` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `regtime` varchar(50) NOT NULL,
  `lastip` varchar(30) NOT NULL,
  `lastime` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `weixin` varchar(50) CHARACTER SET utf8 NOT NULL,
  `nickname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `p_id` int(11) NOT NULL,
  `borthday` varchar(50) CHARACTER SET utf8 NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for famliy
-- ----------------------------
DROP TABLE IF EXISTS `famliy`;
CREATE TABLE `famliy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_code` int(11) NOT NULL,
  `sex` varchar(10) CHARACTER SET utf8 NOT NULL,
  `tel` varchar(15) NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `borthday` varchar(50) CHARACTER SET utf8 NOT NULL,
  `addr` varchar(200) CHARACTER SET utf8 NOT NULL,
  `nickname` varchar(30) CHARACTER SET utf8 NOT NULL,
  `weixin` varchar(30) NOT NULL,
  `relationship` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for hospital_doctor
-- ----------------------------
DROP TABLE IF EXISTS `hospital_doctor`;
CREATE TABLE `hospital_doctor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `department_t` varchar(20) CHARACTER SET utf8 NOT NULL,
  `department` varchar(30) CHARACTER SET utf8 NOT NULL,
  `job_title` varchar(30) CHARACTER SET utf8 NOT NULL,
  `good` varchar(30) CHARACTER SET utf8 NOT NULL,
  `Mon_m` tinyint(2) NOT NULL,
  `Mon_a` tinyint(2) NOT NULL,
  `Tue_m` tinyint(2) NOT NULL,
  `Tue_a` tinyint(2) NOT NULL,
  `Wed_m` tinyint(2) NOT NULL,
  `Wed_a` tinyint(2) NOT NULL,
  `Thu_m` tinyint(2) NOT NULL,
  `Thu_a` tinyint(2) NOT NULL,
  `Fri_m` tinyint(2) NOT NULL,
  `Fri_a` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for image_info
-- ----------------------------
DROP TABLE IF EXISTS `image_info`;
CREATE TABLE `image_info` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `batch_id` int(32) DEFAULT NULL COMMENT '检查批次ID',
  `patient_info_id` int(32) DEFAULT '0' COMMENT '患者ID，关联patient_info表',
  `taketime` int(32) DEFAULT '0' COMMENT '拍摄时间',
  `filepath` varchar(255) DEFAULT '0' COMMENT '文件路径',
  `type` int(32) DEFAULT '1' COMMENT '影像类型 1：舌诊 2：面部 3：红外上半身 4：红外检查所有数据',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_info_id`),
  KEY `image_info_batch_id` (`batch_id`),
  CONSTRAINT `image_info_batch_id` FOREIGN KEY (`batch_id`) REFERENCES `batch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COMMENT='影像文件记录表 关联patient_info';

-- ----------------------------
-- Table structure for knowledge_base_info
-- ----------------------------
DROP TABLE IF EXISTS `knowledge_base_info`;
CREATE TABLE `knowledge_base_info` (
  `TFID` int(11) NOT NULL AUTO_INCREMENT,
  `Answer` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `Question` text CHARACTER SET utf8 NOT NULL,
  `DeleteFlag` int(11) NOT NULL,
  `CreateBy` int(11) NOT NULL,
  `CreateDt` int(11) NOT NULL,
  `UpdateBy` int(11) NOT NULL,
  `UpdateDt` int(11) NOT NULL,
  PRIMARY KEY (`TFID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for mac
-- ----------------------------
DROP TABLE IF EXISTS `mac`;
CREATE TABLE `mac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `mac` varchar(30) CHARACTER SET utf8 NOT NULL,
  `fit` varchar(30) CHARACTER SET utf8 NOT NULL,
  `guang` varchar(30) CHARACTER SET utf8 NOT NULL,
  `address` varchar(200) CHARACTER SET utf8 NOT NULL,
  `env` varchar(50) NOT NULL,
  `sign` int(11) NOT NULL,
  `open` tinyint(4) NOT NULL,
  `version` varchar(20) NOT NULL,
  `redout` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for map
-- ----------------------------
DROP TABLE IF EXISTS `map`;
CREATE TABLE `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x` double NOT NULL,
  `y` float NOT NULL,
  `mac_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for medical_current
-- ----------------------------
DROP TABLE IF EXISTS `medical_current`;
CREATE TABLE `medical_current` (
  `id` int(32) NOT NULL COMMENT '自增ID',
  `case` varchar(255) DEFAULT '' COMMENT '疾病描述',
  `hospital` varchar(255) DEFAULT NULL COMMENT '治疗地点',
  `datetime` int(32) DEFAULT NULL COMMENT '就诊时间',
  `content` varchar(255) DEFAULT NULL COMMENT '检查内容',
  `method` varchar(255) DEFAULT NULL COMMENT '治疗方法',
  `medicine` varchar(255) DEFAULT NULL COMMENT '用药',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='患者当前病史记录表';

-- ----------------------------
-- Table structure for medical_history
-- ----------------------------
DROP TABLE IF EXISTS `medical_history`;
CREATE TABLE `medical_history` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `medical_name` varchar(255) DEFAULT NULL COMMENT '病史名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='患者既往病史记录表';

-- ----------------------------
-- Table structure for notice
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `url` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for patient_info
-- ----------------------------
DROP TABLE IF EXISTS `patient_info`;
CREATE TABLE `patient_info` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(255) DEFAULT NULL COMMENT '患者姓名',
  `sex` int(2) DEFAULT '0' COMMENT '患者性别 0:男 1:女',
  `customer_no` varchar(255) DEFAULT '0' COMMENT '客户编号（去重用）',
  `age` int(10) DEFAULT '0' COMMENT '患者年龄',
  `birthday` int(32) DEFAULT NULL COMMENT '患者生日',
  `identify` varchar(255) DEFAULT '0' COMMENT '患者身份证号',
  `callphone` varchar(255) DEFAULT '0' COMMENT '手机号',
  `address` varchar(255) DEFAULT '0' COMMENT '现住址',
  `career` varchar(255) DEFAULT NULL COMMENT '患者职业',
  `nationality` varchar(255) DEFAULT NULL COMMENT '患者民族',
  `birthplace` varchar(255) DEFAULT NULL COMMENT '患者出生地',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COMMENT='患者基础信息表';

-- ----------------------------
-- Table structure for person
-- ----------------------------
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `tel` varchar(15) CHARACTER SET utf8 NOT NULL,
  `sex` varchar(10) CHARACTER SET utf8 NOT NULL,
  `types` varchar(10) CHARACTER SET utf8 NOT NULL,
  `borthday` varchar(100) CHARACTER SET utf8 NOT NULL,
  `addr` varchar(300) CHARACTER SET utf8 NOT NULL,
  `p_id` int(11) NOT NULL,
  `welcome` varchar(255) CHARACTER SET utf8 NOT NULL,
  `job` varchar(100) CHARACTER SET utf8 NOT NULL,
  `xp` int(11) NOT NULL,
  `regtime` int(11) NOT NULL,
  `lastip` varchar(30) NOT NULL,
  `lastime` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `weixin` varchar(50) NOT NULL,
  `nickname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `role` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for picture
-- ----------------------------
DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(100) NOT NULL,
  `time` int(11) NOT NULL,
  `addr` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sign` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for redout
-- ----------------------------
DROP TABLE IF EXISTS `redout`;
CREATE TABLE `redout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `date` int(11) NOT NULL,
  `img1` varchar(100) NOT NULL,
  `img2` varchar(100) NOT NULL,
  `img3` varchar(100) NOT NULL,
  `img4` varchar(100) NOT NULL,
  `img5` varchar(100) NOT NULL,
  `img6` varchar(100) NOT NULL,
  `img7` varchar(100) NOT NULL,
  `img8` varchar(100) NOT NULL,
  `suggest` text CHARACTER SET utf8 NOT NULL,
  `m_id` varchar(50) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `system` varchar(100) CHARACTER SET utf8 NOT NULL,
  `part` varchar(100) CHARACTER SET utf8 NOT NULL,
  `hot` varchar(100) CHARACTER SET utf8 NOT NULL,
  `tip` varchar(100) CHARACTER SET utf8 NOT NULL,
  `food` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sport` varchar(255) CHARACTER SET utf8 NOT NULL,
  `medicine` varchar(255) CHARACTER SET utf8 NOT NULL,
  `living` varchar(255) CHARACTER SET utf8 NOT NULL,
  `medicine_food` varchar(255) CHARACTER SET utf8 NOT NULL,
  `collateral` varchar(255) CHARACTER SET utf8 NOT NULL,
  `checks` varchar(255) CHARACTER SET utf8 NOT NULL,
  `code` varchar(20) NOT NULL,
  `healthinfo` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for report
-- ----------------------------
DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `id` int(32) NOT NULL AUTO_INCREMENT COMMENT '分析报告ID',
  `patient_info_id` int(32) DEFAULT NULL COMMENT '患者ID 关联patient_info表',
  `content` text COMMENT '报告主题（结构体）',
  `filepath` text COMMENT '报告文件路径',
  `batch_id` int(32) DEFAULT NULL COMMENT '检查批次报告关联ID 关联batch表',
  PRIMARY KEY (`id`),
  KEY `report_batch_id` (`batch_id`),
  CONSTRAINT `report_batch_id` FOREIGN KEY (`batch_id`) REFERENCES `batch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COMMENT='分析报告记录表';

-- ----------------------------
-- Table structure for shui
-- ----------------------------
DROP TABLE IF EXISTS `shui`;
CREATE TABLE `shui` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `totalSleep` int(11) NOT NULL,
  `avgBreath` float NOT NULL,
  `avgHeart` float NOT NULL,
  `reportDate` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sleepScore` int(11) NOT NULL,
  `sleepTime` varchar(100) CHARACTER SET utf8 NOT NULL,
  `wakeUpTime` varchar(100) CHARACTER SET utf8 NOT NULL,
  `deepSleep` int(11) NOT NULL,
  `lightSleep` int(11) NOT NULL,
  `beginSleep` int(11) NOT NULL,
  `wakeUpSleep` int(11) NOT NULL,
  `heartVal` float NOT NULL,
  `breathVal` float NOT NULL,
  `sleepType` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for timeflow
-- ----------------------------
DROP TABLE IF EXISTS `timeflow`;
CREATE TABLE `timeflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tel` varchar(15) NOT NULL,
  `passwd` varchar(35) NOT NULL,
  `lastip` varchar(100) NOT NULL,
  `lastime` int(11) NOT NULL,
  `regtime` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
