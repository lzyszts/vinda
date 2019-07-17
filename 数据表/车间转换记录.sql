--转换记录表
CREATE TABLE `scb_change_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `samaccountname` varchar(32) NOT NULL COMMENT '工号',
  `group_name` varchar(100) DEFAULT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00' COMMENT '日期',
  `time_left` varchar(100) NOT NULL COMMENT '开始时间段',
  `time_right` varchar(100) DEFAULT NULL COMMENT '结束时间段',
  `hour` varchar(100) DEFAULT NULL COMMENT '耗时',
  `decimal` varchar(100) DEFAULT NULL,
  `machine` varchar(100) DEFAULT NULL COMMENT '机台',
  `name` varchar(100) DEFAULT NULL,
  `old` varchar(100) DEFAULT NULL COMMENT '原产品',
  `new` varchar(100) DEFAULT NULL COMMENT '更换产品',
  `standard` varchar(32) DEFAULT NULL COMMENT '标准产量',
  `supply` varchar(32) DEFAULT NULL COMMENT '补产产量',
  `create_time` varchar(32) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `samaccountname` (`samaccountname`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='转换记录表';



--机台
create table `scb_change_machine`(
	id int unsigned not null primary key auto_increment,
	`machine` varchar(255) NOT NULL COMMENT '机台名'
)engine=InnoDB default charset=utf8 comment '机台';

--车间分组
create table `scb_change_group`(
	id int unsigned not null primary key auto_increment,
	`group_name` varchar(100) NOT NULL COMMENT '分组名字'
)engine=InnoDB default charset=utf8 comment '分组信息';

--用户分组
create table `scb_change_user`(
	id int unsigned not null primary key auto_increment,
	`group_id` varchar(100) NOT NULL COMMENT '分组id',
	`samaccountname` varchar(100) COMMENT '工号',
	`name` varchar(100) COMMENT '姓名',
	`zhiwei` varchar(100) COMMENT '职位'
)engine=InnoDB default charset=utf8 comment '用户信息';
