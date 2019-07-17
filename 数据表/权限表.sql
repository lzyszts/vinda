--用户表（老）
CREATE TABLE `user` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `dept` varchar(40) not null comment '部门名 rzb(人力资源部) xzb(行政部) cgb(采购部) cck(仓储科) pgb(品管部) (gcb)工程技术部 scb(生产部) ddk(调度科) zzb(制造部) xxk(信息科)',
  `name` varchar(30) not null comment '姓名',
  `role_id` varchar(50) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `email` varchar(100) default '',
  `thumb` varchar(100) default '' COMMENT '头像',
  `addtime` int(10) unsigned NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--用户表（新加域后）
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `samaccountname` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `company` varchar(40)  comment '公司',
  `department` varchar(50) comment '部门',
  `title` varchar(40) comment '职位',
  `name` varchar(30) comment '姓名',
  `telephonenumber` varchar(30) comment '座机',
  `mobile` varchar(30) comment '手机',
  `mail` varchar(40) comment '邮箱',
  `office` varchar(100) comment '办公地点',
  `auth_ids` varchar(255) DEFAULT 'N' COMMENT '角色权限ids',
  `role_id` varchar(50) NOT NULL DEFAULT '1' COMMENT '角色id',
  PRIMARY KEY(id),
  index (samaccountname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert into user values(null,'admin','sc.6330','维达纸业（四川）有限公司',null,null,null,'0838-2906330','18608381747','l.zy@vinda.com','德阳',null,'0');
insert into user values(null,'1','1','维达纸业（四川）有限公司',null,null,null,'0838-2906330','18608381747','l.zy@vinda.com','德阳',null,'0');


--权限表
CREATE TABLE `auth` (
  `auth_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `auth_name` varchar(20) NOT NULL COMMENT '权限名称',
  `auth_pid` smallint(6) unsigned NOT NULL COMMENT '父id',
  `module` varchar(32) DEFAULT NULL COMMENT '模型',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(32) NOT NULL DEFAULT '' COMMENT '操作方法',
  `open` varchar(6) DEFAULT '0' COMMENT '是否新窗口打开0不打开1打开',
  `auth_level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '权限级别，从0开始计数',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB CHARSET=utf8;



 



--角色表
CREATE TABLE `role` (
  `role_id` smallint(6) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL COMMENT '角色名称',
  `role_auth_ids` varchar(128) NOT NULL DEFAULT '' COMMENT '权限ids,1,2,5',
  `role_auth_ac` text COMMENT '控制器-操作,控制器-操作,控制器-操作'
) ENGINE=InnoDB DEFAULT CHARSET=utf8



① 权限数据
公共类(质询会报表、)
行政部(电话汇总)
权限管理(分组列表、角色列表、权限列表)

权限表
id   name   pid    c    a    path    level
insert into auth values (101,'公共类',0,'','',101,0);
insert into auth values (102,'信息科',0,'','',102,0);
insert into auth values (103,'人力资源及行政部',0,'','',103,0);
insert into auth values (104,'人资部',0,'','',104,0);
insert into auth values (105,'采购部',0,'','',105,0);
insert into auth values (106,'仓储科',0,'','',106,0);
insert into auth values (107,'品管部',0,'','',107,0);
insert into auth values (108,'工程技术部',0,'','',108,0);
insert into auth values (109,'生产部',0,'','',109,0);
insert into auth values (110,'调度科',0,'','',110,0);
insert into auth values (111,'制造部',0,'','',111,0);
insert into auth values (115,'权限管理',0,'','',115,0);
--行政部
insert into auth values (120,'电话汇总',103,'Xzb','phone',"103-120",1);
--公共类
insert into auth values (121,'质询会报表',101,'Rzb','zxh',"101-121",1);--这里要显示到公共类所以父id101
--超级管理员
insert into auth values (170,'申请列表',115,'User','showuser',"115-170",1);
insert into auth values (171,'角色列表',115,'Role','showlist',"115-171",1);
insert into auth values (172,'权限列表',115,'Auth','showlist',"115-172",1);




//角色表数据
信息科  公共类、质询会报表  
行政部：公共类、质询会报表  行政类、电话汇总
人资部：公共类、质询会报表	行政类、电话汇总 

角色表
id   name   ids   ca
insert into role values (50,'信息科','101,121,115,170,171,172','Rzb-zxh,User-showlist,Role-showlist,Auth-showlist');
insert into role values (51,'人力资源部','101,121,103,120','Rzb-zxh');
insert into role values (52,'行政部','101,121,103,120','Rzb-zxh,Xzb-phone');

基础权限
insert into role values (1,'基础权限','101,124','Kpi-index');




role_id



50	信息科	xxk
51	人力资源及行政部rzb
52	财务部	cwb
53	采购部	cgb
54	仓储科	cck
55	市场部	shicb	
56	品管部	pgb	
57	工程技术部 gcb		
58	生产部	scb	
59	调度科	ddk	
60	制造部	zzb	




--注册待验证表
CREATE TABLE `user_loding` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `dept` varchar(40) not null comment '部门名 rzb(人力资源部) xzb(行政部) cgb(采购部) cck(仓储科) pgb(品管部) (gcb)工程技术部 scb(生产部) ddk(调度科) zzb(制造部) xxk(信息科)',
  `name` varchar(30) not null comment '姓名',
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `email` varchar(100) default '',
  `addtime` int(10) unsigned COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;