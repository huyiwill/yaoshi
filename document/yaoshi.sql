CREATE DATABASE IF NOT EXISTS `yaoshi` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

# 后台管理员表
DROP TABLE IF EXISTS `ys_admin`;
CREATE TABLE IF NOT EXISTS `ys_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `account` varchar(45) NOT NULL COMMENT '账号',
  `password` char(40) NOT NULL COMMENT '密码',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理员表' AUTO_INCREMENT=1;
#  初始化超级管理员
INSERT INTO `ys_admin` (`id`, `account`, `password`, `nickname`, `status`, `create_time`) VALUES
  (1, 'admin', '1fdb7184e697ab9355a3f1438ddc6ef9', '超级管理员', 1, unix_timestamp());

#省份信息表
CREATE TABLE IF NOT EXISTS `ys_province` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '省编号',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '省名称',
  `pycode` varchar(50) NOT NULL DEFAULT '' COMMENT '拼音码',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='省份信息表' AUTO_INCREMENT=1;

# 城市信息表
CREATE TABLE IF NOT EXISTS  `ys_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '城市编号',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '城市名称',
  `pycode` varchar(50) NOT NULL DEFAULT '' COMMENT '城市拼音码',
  `pid` int(11) NOT NULL COMMENT '省编号',
  `postcode` varchar(50) NOT NULL DEFAULT '' COMMENT '邮编',
  `areacode` varchar(50) NOT NULL DEFAULT '' COMMENT '区号',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='城市信息表' AUTO_INCREMENT=1;

# 医院信息表
CREATE TABLE IF NOT EXISTS  `ys_hospital` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `province_id` int(11) NOT NULL COMMENT '省ID',
  `city_id` int(11) NOT NULL COMMENT '市ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '医院名称',
  `level` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '医院等级 1:其他,2:一甲,3:二甲,4:三甲,5:一乙,6:二乙,7:三乙',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  INDEX (`level`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='医院信息表' AUTO_INCREMENT=1;

# 单位信息表
CREATE TABLE IF NOT EXISTS  `ys_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `province_id` int(11) NOT NULL COMMENT '省ID',
  `city_id` int(11) NOT NULL COMMENT '市ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '单位名称',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:其他,2:学校,3:事业单位,4:私营企业)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='单位信息表' AUTO_INCREMENT=1;

# 科室信息表
DROP TABLE IF EXISTS `ys_department_category`;
CREATE TABLE IF NOT EXISTS `ys_department_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID,0:顶级科室',
  `name` varchar(55) NOT NULL COMMENT '科室名称',
  `code` varchar(55) NOT NULL COMMENT '科室编码',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复--父分类删除,关联删除所有子分类)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='科室信息表' AUTO_INCREMENT=1;

# 学校专业信息表
DROP TABLE IF EXISTS `ys_school_major`;
CREATE TABLE IF NOT EXISTS `ys_school_major` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID,0:顶级专业',
  `name` varchar(55) NOT NULL COMMENT '专业名称',
  `code` varchar(55) NOT NULL COMMENT '专业编码',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复--父分类删除,关联删除所有子分类)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校专业信息表' AUTO_INCREMENT=1;

# 前台用户角色表
DROP TABLE IF EXISTS `ys_role`;
CREATE TABLE IF NOT EXISTS `ys_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT '角色名称',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台角色表' AUTO_INCREMENT=1;

# 前台用户权限表

# 前台用户分组表
DROP TABLE IF EXISTS `ys_user_group`;
CREATE TABLE IF NOT EXISTS `ys_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(55) NOT NULL COMMENT '分组名称',
  `code` varchar(55) NOT NULL COMMENT '分组编码',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='前台用户分组表' AUTO_INCREMENT=1;
# ---------- 前后台分割 -----------#

# 用户总表
DROP TABLE IF EXISTS `ys_user_global`;
CREATE TABLE IF NOT EXISTS `ys_user_global` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mobile` char(11) NOT NULL COMMENT '手机号/账户',
  `password` char(40) NOT NULL COMMENT '密码',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `unionid` char(29) NOT NULL DEFAULT '' COMMENT '第三方登录绑定唯一标识',
  `birthday` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `role` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `head` varchar(255) NOT NULL DEFAULT '' COMMENT '头像图片url',
  `real_head` varchar(255) NOT NULL DEFAULT '' COMMENT '头像图片硬件地址',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '资格认证图片url',
  `real_path` varchar(255) NOT NULL DEFAULT '' COMMENT '资格认证图片硬件地址',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '本人随机生成邀请码',
  `group` int(10) unsigned NOT NULL DEFAULT '0' COMMENT  '用户所在分组',
  `perfect` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否完善资料 1:未完善,2:完善',
  `authentication` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否通过资格认证 1:待审核,2:通过,3:未通过',
  `from` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '来源 1:前台注册,2:后台添加',
  `score`  double(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '积分数值',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户基本信息' AUTO_INCREMENT=1;

# 药师资料完善
DROP TABLE IF EXISTS `ys_user_pharmacist`;
CREATE TABLE IF NOT EXISTS `ys_user_pharmacist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT 'ID',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '药师分类(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `hospital` varchar(255) NOT NULL COMMENT '所在医院',
  `title` varchar(45) NOT NULL DEFAULT '' COMMENT '职称',
  `teaching_hospital` varchar(255) NOT NULL DEFAULT '' COMMENT '实习医院,空则没实习',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='药师完善资料' AUTO_INCREMENT=1;

# 医生/护士资料完善
DROP TABLE IF EXISTS `ys_user_health`;
CREATE TABLE IF NOT EXISTS `ys_user_health` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT 'ID',
  `hospital` varchar(255) NOT NULL COMMENT '所在医院',
  `department` varchar(100) NOT NULL COMMENT '所在科室',
  `title` varchar(45) NOT NULL DEFAULT '' COMMENT '职称',
  `teaching_hospital` varchar(255) NOT NULL DEFAULT '' COMMENT '实习医院,空则没实习',
  `teaching_department` varchar(100) NOT NULL DEFAULT '' COMMENT '实习科室,空则没实习',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医生/护士完善资料' AUTO_INCREMENT=1;

# 学生资料完善
DROP TABLE IF EXISTS `ys_user_student`;
CREATE TABLE IF NOT EXISTS `ys_user_student` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT 'ID',
  `entrance_time` int(11) unsigned NOT NULL COMMENT '入学时间',
  `school` varchar(55) NOT NULL COMMENT '学校',
  `major` varchar(55) NOT NULL COMMENT '专业',
  `education` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:专科及以下,2:学士,3：硕士,4:博士)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生完善资料' AUTO_INCREMENT=1;

# 其他资料完善
DROP TABLE IF EXISTS `ys_user_other`;
CREATE TABLE IF NOT EXISTS `ys_user_other` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT 'ID',
  `city` varchar(25) NOT NULL COMMENT '所在城市',
  `company` varchar(100) NOT NULL COMMENT '所在公司',
  `post` varchar(100) NOT NULL DEFAULT '' COMMENT '职务',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='其他完善资料' AUTO_INCREMENT=1;

# 用户邀请表
DROP TABLE IF EXISTS `ys_user_code`;
CREATE TABLE IF NOT EXISTS `ys_user_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `from` int(10) unsigned NOT NULL COMMENT '来源用户ID/邀请',
  `to` int(10) unsigned NOT NULL COMMENT '给到用户ID/被邀请',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '来源邀请码',
  `score` double(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '奖励积分数值',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复--父分类删除,关联删除所有子分类)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户邀请表' AUTO_INCREMENT=1;

# 用户微信信息
DROP TABLE IF EXISTS `ys_user_wechat`;
CREATE TABLE IF NOT EXISTS `ys_user_wechat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `openid` char(28) NOT NULL COMMENT '用户openid',
  `unionid` char(29) NOT NULL COMMENT '用户unionid',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE( `unionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户微信信息' AUTO_INCREMENT=1;

# 药学分类表
DROP TABLE IF EXISTS `ys_pharmacy_category`;
CREATE TABLE IF NOT EXISTS `ys_pharmacy_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID,0:顶级分类',
  `name` varchar(55) NOT NULL COMMENT '分类名称',
  `code` varchar(55) NOT NULL COMMENT '分类编码',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '分类描述',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复--父分类删除,关联删除所有子分类)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='药学分类表' AUTO_INCREMENT=1;

# 题库表
DROP TABLE IF EXISTS `ys_admin_questions`;
CREATE TABLE IF NOT EXISTS `ys_admin_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category_id` int(10) unsigned NOT NULL COMMENT '顶级分类id',
  `pharmacy_id` int(10) unsigned NOT NULL COMMENT '药学分类ID',
  `name` varchar(55) NOT NULL COMMENT '题库名称',
  `code` varchar(55) NOT NULL COMMENT '题库编码',
  `occupation` varchar(20) NOT NULL COMMENT '1:其他,2:药师,3:医生,4：护士,5:学生;逗号分隔',
  `topic_type` varchar(20) NOT NULL COMMENT '1:单,2:多,3:填空,4：处方审核,5:用药交代,6:问答,7:材料题;逗号分隔',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/办理时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题库表' AUTO_INCREMENT=1;

# 题目表
DROP TABLE IF EXISTS `ys_subject`;
CREATE TABLE IF NOT EXISTS `ys_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID,>0则为审核入库',
  `name` varchar(55) NOT NULL COMMENT '题干',
  `topic_type` varchar(20) NOT NULL COMMENT '题目类型-->1:单,2:多,3:填空,4：处方审核,5:用药交代,6:问答,7:材料题',
  `choice` text COMMENT '可选择答案',
  `right_key` text COMMENT '正确答案',
  `score` varchar(45) NOT NULL COMMENT '分值',
  `role` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '适用角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '药师触发的职务(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `degree` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '难易程度(1:简单,2:中等,3:困难)',
  `category_id` int(10) unsigned NOT NULL COMMENT '顶级分类id',
  `pharmacy_id` int(10) unsigned NOT NULL COMMENT '药学分类ID,分类名为药理则添加治疗领域',
  `therapeutic` varchar(55) NOT NULL DEFAULT '' COMMENT '治疗领域,分类名为药理则添加治疗领域',
  `analysis` text COMMENT '解析',
  `price` varchar(45) NOT NULL COMMENT '查看解析所需分值',
  `is_price` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用查看解析付费(1:禁用,2:启用)',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片真实地址',
  `weight` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题目热度权重',
  `from` varchar(255) NOT NULL DEFAULT '' COMMENT '题目来源',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题目表' AUTO_INCREMENT=1;

# 用户题目审核表
DROP TABLE IF EXISTS `ys_subject_examine`;
CREATE TABLE IF NOT EXISTS `ys_subject_examine` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `questions_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题库ID',
  `name` varchar(55) NOT NULL COMMENT '题干',
  `topic_type` varchar(20) NOT NULL COMMENT '题目类型-->1:单,2:多,3:填空,4：处方审核,5:用药交代,6:问答,7:材料题',
  `choice` text COMMENT '可选择答案',
  `right_key` text COMMENT '正确答案',
  `score` varchar(45) NOT NULL DEFAULT '' COMMENT '分值',
  `role` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '适用角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '药师触发的职务(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `degree` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '难易程度(1:简单,2:中等,3:困难)',
  `category_id` int(10) unsigned NOT NULL COMMENT '顶级分类id',
  `pharmacy_id` int(10) unsigned NOT NULL COMMENT '药学分类ID,分类名为药理则添加治疗领域',
  `therapeutic` varchar(55) NOT NULL DEFAULT '' COMMENT '治疗领域,分类名为药理则添加治疗领域',
  `analysis` text COMMENT '解析',
  `price` varchar(45) NOT NULL DEFAULT '' COMMENT '查看解析所需分值',
  `is_price` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用查看解析付费(1:禁用,2:启用)',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片真实地址',
  `from` varchar(255) NOT NULL DEFAULT '' COMMENT '题目来源',
  `state` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '审核状态(1:待审核,2:通过,3：未通过)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户题目审核表' AUTO_INCREMENT=1;

# 实践题关联答案表
DROP TABLE IF EXISTS `ys_practice_answer`;
CREATE TABLE IF NOT EXISTS `ys_practice_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `user_answer` text COMMENT '用户提交的答案',
  `number` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数量',
  `synchro` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否同步(1:同步/前后台都可见,2:不同步/仅后台可见)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='实践题关联答案表' AUTO_INCREMENT=1;

# 实践题答案评论表
DROP TABLE IF EXISTS `ys_practice_comment`;
CREATE TABLE IF NOT EXISTS `ys_practice_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `answer_id` int(10) unsigned NOT NULL COMMENT '用户答案ID,ys_practice_answer',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID,0:顶级评论',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
  `number` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数量',
  `synchro` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否同步(1:同步/前后台都可见,2:不同步/仅后台可见)',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '评论/回复(1:评论,2:回复)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='实践题答案评论表' AUTO_INCREMENT=1;

# 答案评论点赞表
DROP TABLE IF EXISTS `ys_thumbs_up`;
CREATE TABLE IF NOT EXISTS `ys_thumbs_up` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `relation_id` int(10) unsigned NOT NULL COMMENT '关联ID,根据type',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '答案/评论(1:答案,2:评论)',
  `day` char(10) NOT NULL COMMENT '点赞日期',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '加减(1:加,2:减)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='答案评论点赞表' AUTO_INCREMENT=1;

# 题目纠错表
DROP TABLE IF EXISTS `ys_subject_error`;
CREATE TABLE IF NOT EXISTS `ys_subject_error` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `error` text COMMENT '题目纠错',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题目纠错表' AUTO_INCREMENT=1;

# 用户题目关系表
DROP TABLE IF EXISTS `ys_subject_relation`;
CREATE TABLE IF NOT EXISTS `ys_subject_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `purpose` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '关系(1:收藏,2:错题,3:查看解析; 多种关系不覆盖,多对多)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户题目关系表' AUTO_INCREMENT=1;

# 题库题目中间表(多对多关联删除)
DROP TABLE IF EXISTS `ys_questions_subject`;
CREATE TABLE IF NOT EXISTS `ys_questions_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `questions_id` int(10) unsigned NOT NULL COMMENT '题库ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/办理时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='题库题目中间表' AUTO_INCREMENT=1;

# 处方/用药交代题目中患者信息表
DROP TABLE IF EXISTS `ys_patient`;
CREATE TABLE IF NOT EXISTS `ys_patient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `subject_id` int(10) unsigned NOT NULL COMMENT '题目ID',
  `name` varchar(55) NOT NULL COMMENT '姓名',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别(1:男,2:女)',
  `age` varchar(10) NOT NULL DEFAULT '' COMMENT '年龄',
  `department` varchar(10) NOT NULL DEFAULT '' COMMENT '科室',
  `clinical_diagnosis` text COMMENT '临床诊断',
  `medicine` text COMMENT '用药',
  `state` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '来源(1:后台添加,2:用户审核题目)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='患者信息表' AUTO_INCREMENT=1;

# 患者用药表
# DROP TABLE IF EXISTS `ys_patient_medicine`;
# CREATE TABLE IF NOT EXISTS `ys_patient_medicine` (
#   `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
#   `patient_id` int(10) unsigned NOT NULL COMMENT '患者ID',
#   `name` varchar(255) NOT NULL COMMENT '药品名称',
#   `dose` varchar(255) NOT NULL COMMENT '剂量',
#   `channel` varchar(255) NOT NULL COMMENT '给药途径',
#   `rate` varchar(255) NOT NULL COMMENT '用药频率',
#   `course` varchar(255) NOT NULL COMMENT '疗程',
#   `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用药数量',
#   `price` decimal(8,2) NOT NULL DEFAULT '0' COMMENT '价格',
#   `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
#   `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
#   PRIMARY KEY (`id`)
# ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='患者用药表' AUTO_INCREMENT=1;

# 试卷分组表
DROP TABLE IF EXISTS `ys_test_group`;
CREATE TABLE IF NOT EXISTS `ys_test_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid_admin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` varchar(55) NOT NULL COMMENT '分组名称',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试卷分组表' AUTO_INCREMENT=1;

# 历年真题表(相当固定题库)
DROP TABLE IF EXISTS `ys_exercises`;
CREATE TABLE IF NOT EXISTS `ys_exercises` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(55) NOT NULL COMMENT '真题名称',
  `time` varchar(255) NOT NULL COMMENT '答题时间',
  `remark` varchar(255) NOT NULL COMMENT '真题备注',
  `price` decimal(8,2) NOT NULL DEFAULT '0' COMMENT '真题价格',
  `is_discount` tinyint(4) unsigned NOT NULL DEFAULT '2' COMMENT '状态(1:打折,2:不打折)',
  `discount` varchar(25) NOT NULL DEFAULT '' COMMENT '折扣',
  `discount_end` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '促销截止日期',
  `test_end` int(11) unsigned NOT NULL COMMENT '真题截止日期',
  `role` tinyint(4) unsigned NOT NULL COMMENT '适用角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '药师触发的职务(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo照片url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT 'logo照片真实地址',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:关闭/未销售,2:开启/销售中)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历年真题表' AUTO_INCREMENT=1;

# 历年真题题目表
DROP TABLE IF EXISTS `ys_exercises_subject`;
CREATE TABLE IF NOT EXISTS `ys_exercises_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `exercises_id` int(10) unsigned NOT NULL COMMENT '历年真题ID',
  `name` varchar(55) NOT NULL COMMENT '题干',
  `topic_type` varchar(20) NOT NULL COMMENT '题目类型-->1:单,2:多,3:填空,4：处方审核,5:用药交代,6:问答,7:材料题',
  `choice` text COMMENT '可选择答案',
  `right_key` text COMMENT '正确答案',
  `score` varchar(45) NOT NULL COMMENT '分值',
  `role` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '适用角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '药师触发的职务(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `degree` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '难易程度(1:简单,2:中等,3:困难)',
  `category_id` int(10) unsigned NOT NULL COMMENT '顶级分类id',
  `pharmacy_id` int(10) unsigned NOT NULL COMMENT '药学分类ID,分类名为药理则添加治疗领域',
  `therapeutic` varchar(55) NOT NULL DEFAULT '' COMMENT '治疗领域,分类名为药理则添加治疗领域',
  `analysis` text COMMENT '解析',
  `price` varchar(45) NOT NULL DEFAULT '' COMMENT '查看解析所需分值',
  `is_price` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用查看解析付费(1:禁用,2:启用)',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片真实地址',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '真题题目排序',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历年真题题目表' AUTO_INCREMENT=1;

# 用户购买历年真题表
DROP TABLE IF EXISTS `ys_user_exercises`;
CREATE TABLE IF NOT EXISTS `ys_user_exercises` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `exercises_id` int(10) unsigned NOT NULL COMMENT '历年真题ID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户购买历年真题表' AUTO_INCREMENT=1;

# 用户答历年真题历史记录表
DROP TABLE IF EXISTS `ys_exercises_do`;
CREATE TABLE IF NOT EXISTS `ys_exercises_do` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `exercises_id` int(10) unsigned NOT NULL COMMENT '历年真题ID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户答历年真题历史记录表' AUTO_INCREMENT=1;

# 用户历年真题答题表(重复做题则更新)
DROP TABLE IF EXISTS `ys_exercises_record`;
CREATE TABLE IF NOT EXISTS `ys_exercises_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `exercises_id` int(10) unsigned NOT NULL COMMENT '历年真题ID',
  `exercises_subject` text COMMENT '用户所答题目 json',
  `answer_time` varchar(255) NOT NULL COMMENT '答题用时(分钟)',
  `score` varchar(45) NOT NULL COMMENT '所得分值',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `update_time` int(11) unsigned NOT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户购买历年真题表(重复做题则更新)' AUTO_INCREMENT=1;

# 试卷表
DROP TABLE IF EXISTS `ys_test`;
CREATE TABLE IF NOT EXISTS `ys_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid_admin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `test_group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷分组ID',
  `name` varchar(55) NOT NULL COMMENT '试卷名称',
  `remark` varchar(255) NOT NULL COMMENT '试卷备注',
  `assess_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '考核次数',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试卷表' AUTO_INCREMENT=1;

# 试卷题目表
DROP TABLE IF EXISTS `ys_test_subject`;
CREATE TABLE IF NOT EXISTS `ys_test_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `test_id` int(10) unsigned NOT NULL COMMENT '试卷ID',
  `name` varchar(55) NOT NULL COMMENT '题干',
  `topic_type` varchar(20) NOT NULL COMMENT '题目类型-->1:单,2:多,3:填空,4：处方审核,5:用药交代,6:问答,7:材料题',
  `choice` text COMMENT '可选择答案',
  `right_key` text COMMENT '正确答案',
  `score` varchar(45) NOT NULL COMMENT '分值',
  `role` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '适用角色(1:其他,2:药师,3：医生,4：护士,5：学生)',
  `post` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '药师触发的职务(1:其他,2:临床药师,3:咨询药师,4：调剂药师,5:执业药师)',
  `degree` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '难易程度(1:简单,2:中等,3:困难)',
  `category_id` int(10) unsigned NOT NULL COMMENT '顶级分类id',
  `pharmacy_id` int(10) unsigned NOT NULL COMMENT '药学分类ID,分类名为药理则添加治疗领域',
  `therapeutic` varchar(55) NOT NULL DEFAULT '' COMMENT '治疗领域,分类名为药理则添加治疗领域',
  `analysis` text COMMENT '解析',
  `price` varchar(45) NOT NULL DEFAULT '' COMMENT '查看解析所需分值',
  `is_price` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用查看解析付费(1:禁用,2:启用)',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '处方照片真实地址',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷题目排序',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='试卷题目表' AUTO_INCREMENT=1;

# 考核成员表
DROP TABLE IF EXISTS `ys_assess_members`;
CREATE TABLE IF NOT EXISTS `ys_assess_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `uid_admin` int(11) NOT NULL COMMENT '考核管理者用户UID',
  `members_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '成员分组ID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  INDEX (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考核成员表' AUTO_INCREMENT=1;

# 考核成员分组表
DROP TABLE IF EXISTS `ys_assess_members_group`;
CREATE TABLE IF NOT EXISTS `ys_assess_members_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid_admin` int(11) NOT NULL COMMENT '考核管理者用户ID',
  `name` varchar(255) NOT NULL COMMENT '考核成员分组名称',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考核成员分组表' AUTO_INCREMENT=1;

# 考核表
DROP TABLE IF EXISTS `ys_assess`;
CREATE TABLE IF NOT EXISTS `ys_assess` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid_admin` int(11) NOT NULL COMMENT '考核管理者用户ID',
  `name` varchar(255) NOT NULL COMMENT '考核名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '考核备注',
  `test_id` int(11) NOT NULL COMMENT '关联试卷ID',
  `pass_score` varchar(25) NOT NULL DEFAULT '' COMMENT '及格分数/合格标准',
  `start_time` int(11) unsigned NOT NULL COMMENT '考核开始时间',
  `end_time` int(11) unsigned NOT NULL COMMENT '考核结束时间',
  `time` varchar(255) NOT NULL COMMENT '答题时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考核表' AUTO_INCREMENT=1;

# 用户考核记录表(多对多, 每次考核一人一次)
DROP TABLE IF EXISTS `ys_user_assess`;
CREATE TABLE IF NOT EXISTS `ys_user_assess` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `assess_id` int(11) NOT NULL COMMENT '考核ID',
  `test_subject` text COMMENT '用户考核题目 json',
  `answer_time` varchar(255) NOT NULL DEFAULT '' COMMENT '答题用时(分钟)',
  `theory_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '理论分值',
  `practice_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '实践分值',
  `one_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '单选题分值',
  `two_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '多选题分值',
  `four_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '处方题分值',
  `five_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '用药交代题分值',
  `six_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '问答题分值',
  `other_score` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '其他分值',
  `state` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:未考核,2:批阅中,3:已批阅,4:已发布(成绩),5:已结束 && 考核已过期 则显示已结束)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `complete_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '交卷状态(1:非法交卷,2:正常交卷)',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始考核时间',
  `complete_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '交卷时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户考核记录表' AUTO_INCREMENT=1;

# 订单表(产品类型: 积分购买/现金(1:10),会员办理/现金+积分抵扣,历年真题购买/现金+积分抵扣)
DROP TABLE IF EXISTS `ys_order`;
CREATE TABLE IF NOT EXISTS `ys_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `relation_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `sn` varchar(255) NOT NULL COMMENT '订单号',
  `product_subject` varchar(100) NOT NULL COMMENT '产品题目',
  `product_body` varchar(100) NOT NULL DEFAULT '' COMMENT '产品描述',
  `type` tinyint(4) NOT NULL COMMENT '产品类型 1:积分购买,2:会员办理,3:历年真题购买',
  `score` double(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '抵扣积分数值',
  `total_fee` decimal(10,2) NOT NULL COMMENT '订单总金额',
  `score_payment` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '积分抵扣金额',
  `cash_payment` decimal(10,2) NOT NULL COMMENT '现金支付金额',
  `discount_fee` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '优惠金额',
  `pay_method` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 1:支付宝,2:微信',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单状态 1:未支付,2:支付成功',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付成功时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=1;

# 积分增减详情表
DROP TABLE IF EXISTS `ys_score_details`;
CREATE TABLE IF NOT EXISTS `ys_score_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `score` double(10,2) NOT NULL DEFAULT '0' COMMENT '积分数值',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `type` tinyint(4) NOT NULL COMMENT '积分类型 1:积分购买,2:会员办理抵扣,3:历年真题购买抵扣,4:邀请码...',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  INDEX (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分增减详情表' AUTO_INCREMENT=1;

# 会员表
DROP TABLE IF EXISTS `ys_member`;
CREATE TABLE IF NOT EXISTS `ys_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `method` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开通方式(1:线上/前台,2:会员卡/后台)',
  `time_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开通时间类型(1:按月,2:按年)',
  `number` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '开通数量',
  `expiration_time` int(11) unsigned NOT NULL COMMENT '过期时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员表' AUTO_INCREMENT=1;

# 会员卡表
DROP TABLE IF EXISTS `ys_member_card`;
CREATE TABLE IF NOT EXISTS `ys_member_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员ID',
  `type_id` int(10) unsigned NOT NULL COMMENT '会员类型ID',
  `card_number` char(12) NOT NULL COMMENT '会员卡号',
  `state` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '使用状态(1:未使用,2:已使用)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡表' AUTO_INCREMENT=1;

# 会员卡类型表
DROP TABLE IF EXISTS `ys_member_card_type`;
CREATE TABLE IF NOT EXISTS `ys_member_card_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(55) NOT NULL COMMENT '会员卡类型名称',
  `time_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开通时间类型(1:按月,2:按年)',
  `number` mediumint(10) unsigned NOT NULL DEFAULT '0' COMMENT '开通数量',
  `unit_price` decimal(10,2) NOT NULL COMMENT '会员单价',
  `discount_fee` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '促销单价,0不促销',
  `total_fee` decimal(10,2) NOT NULL COMMENT '会员总价',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡类型表' AUTO_INCREMENT=1;

# 会议基础信息表
DROP TABLE IF EXISTS `ys_meeting`;
CREATE TABLE IF NOT EXISTS `ys_meeting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid_admin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '前台会议创建人ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '后台会议创建人ID',
  `contacts` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '联系手机',
  `name` varchar(55) NOT NULL COMMENT '会议名称',
  `name_english` varchar(55) NOT NULL DEFAULT '' COMMENT '会议英文名称',
  `banner` varchar(255) NOT NULL DEFAULT '' COMMENT '会议banner图url',
  `real_banner` varchar(255) NOT NULL DEFAULT '' COMMENT '会议banner图真实地址',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '会议icon图url',
  `real_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '会议icon图真实地址',
  `enroll_start` int(11) unsigned NOT NULL COMMENT '报名开始时间',
  `enroll_end` int(11) unsigned NOT NULL COMMENT '报名结束时间',
  `enroll_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '报名状态(1:开启,2:关闭)',
  `time_start` int(11) unsigned NOT NULL COMMENT '会议开始时间',
  `time_end` int(11) unsigned NOT NULL COMMENT '会议结束时间',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '会议状态(1:开启,2:关闭)',
  `attend_time` int(11) unsigned NOT NULL COMMENT '现场报到时间',
  `province_id` int(11) NOT NULL COMMENT '省ID',
  `city_id` int(11) NOT NULL COMMENT '市ID',
  `address` varchar(255) NOT NULL COMMENT '会议详细地点',
  `venue_name` varchar(255) NOT NULL COMMENT '会场名称',
  `is_credit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '学分类型(1:学分制,2:非学分制)',
  `credis` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '学分数',
  `type` varchar(55) NOT NULL COMMENT '会议类型',
  `subject` varchar(55) NOT NULL COMMENT '所属学科',
  `examine_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '审核状态(1:待审核,2:审核通过,3:审核未通过)',
  `is_release` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否发布(1:未发布,2:已发布)',
  `data` text COMMENT '会议资料,文件可下载,json存储',
  `from` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '来源(1:前台,2:后台)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议基础信息表' AUTO_INCREMENT=1;

# 会议详细信息表
DROP TABLE IF EXISTS `ys_meeting_details`;
CREATE TABLE IF NOT EXISTS `ys_meeting_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `meeting_id` int(10) unsigned NOT NULL COMMENT '会议ID',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '内容',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '图url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '图真实地址',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子项排序',
  `type` tinyint(1) unsigned NOT NULL COMMENT '子项(1:会议介绍,2:会议日程,3：会议嘉宾,4:会议邀请函)',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '子项(1:显示,2:隐藏)',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议详细信息表' AUTO_INCREMENT=1;

# 会议日程表
DROP TABLE IF EXISTS `ys_meeting_schedule`;
CREATE TABLE IF NOT EXISTS `ys_meeting_schedule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `meeting_id` int(10) unsigned NOT NULL COMMENT '会议ID',
  `days` varchar(100) NOT NULL DEFAULT '1' COMMENT '第几天',
  `photo` varchar(255) NOT NULL DEFAULT '' COMMENT '图url',
  `real_photo` varchar(255) NOT NULL DEFAULT '' COMMENT '图真实地址',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议日程表' AUTO_INCREMENT=1;

# 会议嘉宾表
DROP TABLE IF EXISTS `ys_meeting_guest`;
CREATE TABLE IF NOT EXISTS `ys_meeting_guest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `meeting_id` int(10) unsigned NOT NULL COMMENT '会议ID',
  `head` varchar(255) NOT NULL DEFAULT '' COMMENT '头像url',
  `real_head` varchar(255) NOT NULL DEFAULT '' COMMENT '头像真实地址',
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `remark` text COMMENT '介绍备注',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/开通时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会议嘉宾表' AUTO_INCREMENT=1;

# 用户会议报名表
DROP TABLE IF EXISTS `ys_meeting_user`;
CREATE TABLE IF NOT EXISTS `ys_meeting_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `meeting_id` int(10) unsigned NOT NULL COMMENT '会议ID',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1:正常,2:禁用,3：删除/不可恢复)',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间/报名时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户会议报名表' AUTO_INCREMENT=1;