--周计划表
create database vinda;
use vinda;
set names gbk;
create table rzb_zxh_weekplan(
	id int unsigned not null primary key auto_increment,
	samaccountname varchar(32) comment '工号',
	number varchar(15) not null comment '主题序号',
	theme varchar(50) not null comment '主题',
	name varchar(17) not null comment '填表人',
	dept varchar(30) not null comment '部门',
	writetime date not null comment '填表时间',
	starttime date not null comment '开始时间',
	endtime date not null comment '结束时间',
	xuhao varchar(50) comment '序号',
	ywmodel varchar(400) comment '业务模块',
	ejmodel varchar(400) comment '二级模块',
	result varchar(400) comment '结果定义',
	statustime varchar(400) comment '状态时间',
	status varchar(400) comment '状态',
	sf varchar(400) comment '是否自罚',
	zd varchar(400) comment '是否重点',
	upload varchar(200) comment '附件地址',
	remark varchar(400) comment '备注',
	line int unsigned comment '总行数',
	time_stamp varchar(15) comment '提交时间戳',
	alldata text comment '序列化后的所有数据的数组',
	hb varchar(10) comment '是否填写汇报表 1代表是'
);
--周汇报表

create table rzb_zxh_weekhb(
	id int unsigned not null primary key auto_increment,
	samaccountname varchar(32) comment '工号',
	number varchar(15) not null comment '主题序号',
	theme varchar(50) not null comment '主题',
	name varchar(17) not null comment '填表人',
	dept varchar(30) not null comment '部门',
	writetime date not null comment '填表时间',
	starttime date not null comment '开始时间',
	endtime date not null comment '结束时间',
	xuhao varchar(50) comment '序号',
	ywmodel varchar(400) comment '业务模块',
	ejmodel varchar(400) comment '二级模块',
	result varchar(400) comment '结果定义',
	statustime varchar(400) comment '状态时间',
	status varchar(400) comment '状态',
	jieguo varchar(400) comment '结果',
	yuanyin varchar(400) comment '未完成原因',
	cuoshi varchar(400) comment '解决措施',
	zd varchar(400) comment '是否重点',
	upload varchar(200) comment '附件地址',
	remark varchar(400) comment '备注',
	pid int comment '父id',
	line int unsigned comment '总行数',
	time_stamp varchar(15) comment '提交时间戳',
	alldata text comment '序列化后的所有数据的数组'
);



--月计划表
create table rzb_zxh_monthplan(
	id int unsigned not null primary key auto_increment,
	samaccountname varchar(32) comment '工号',
	number varchar(15) not null comment '主题序号',
	theme varchar(50) not null comment '主题',
	name varchar(17) not null comment '填表人',
	dept varchar(30) not null comment '部门',
	writetime date not null comment '填表时间',
	starttime date not null comment '开始时间',
	endtime date not null comment '结束时间',
	xuhao varchar(50) comment '序号',
	ywmodel varchar(400) comment '业务模块',
	ejmodel varchar(400) comment '二级模块',
	result varchar(400) comment '结果定义',
	statustime varchar(400) comment '行动措施时间',
	status varchar(400) comment '行动措施状态',
	sf varchar(400) comment '是否自罚',
	zd varchar(400) comment '是否重点',
	upload varchar(200) comment '附件地址',
	remark varchar(400) comment '备注',
	line int unsigned comment '总行数',
	time_stamp varchar(15) comment '提交时间戳',
	alldata text comment '序列化后的所有数据的数组'
);

--月汇报表
create table rzb_zxh_monthhb(
	id int unsigned not null primary key auto_increment,
	samaccountname varchar(32) comment '工号',
	number varchar(15) not null comment '主题序号',
	theme varchar(50) not null comment '主题',
	name varchar(17) not null comment '填表人',
	dept varchar(30) not null comment '部门',
	writetime date not null comment '填表时间',
	starttime date not null comment '开始时间',
	endtime date not null comment '结束时间',
	xuhao varchar(50) comment '序号',
	ywmodel varchar(400) comment '业务模块',
	ejmodel varchar(400) comment '二级模块',
	result varchar(400) comment '结果定义',
	statustime varchar(400) comment '状态时间',
	status varchar(400) comment '状态',
	jieguo varchar(400) comment '结果',
	yuanyin varchar(400) comment '未完成原因',
	cuoshi varchar(400) comment '解决措施',
	zd varchar(400) comment '是否重点',
	upload varchar(200) comment '附件地址',
	remark varchar(400) comment '备注',
	pid int comment '父id',
	line int unsigned comment '总行数',
	time_stamp varchar(15) comment '提交时间戳',
	alldata text comment '序列化后的所有数据的数组'
);


--汇总数据
create table rzb_zxh_hz(
	id int unsigned not null primary key auto_increment,
	content text not null comment '序列化后的整表数据',
	line int unsigned comment '总表数',
	year varchar(6) comment '年',
	month varchar(4) comment '月',
	time_stamp varchar(15) comment '提交时间戳'
);



--对比时间戳
create table rzb_zxh_public(
	id int unsigned not null primary key auto_increment,
	modify_timestamp varchar(15) comment '修改的时间戳',
	forbid_timestamp varchar(15) comment '禁止的时间戳',
	forbid_week varchar(15) comment '禁止的周，之前的周不能再插入',
	remark varchar(255) comment '备注'
);

insert into rzb_zxh_public values('1','1466610683','1466610683','20160606','禁止修改的时间');
insert into rzb_zxh_public values('2','1466610683','1466610683','201606','月禁止修改的时间');



--交流议题表
create table rzb_zxh_talk(
	id int unsigned not null primary key auto_increment,
	xuhao varchar(15) comment '序号',
	title varchar(120) comment '标题',
	dept varchar(30) comment '部门',
	name varchar(30) comment '姓名',
	time varchar(20) comment '时长',
	upload varchar(200) comment '附件',
	add_name varchar(20) comment '添加的人',
);

--决议事项
create table rzb_zxh_decision(
	id int unsigned not null primary key auto_increment,
	xuhao varchar(15) comment '序号',
	content varchar(255) comment '决议事项纪要',
	dept varchar(60) comment '责任人部门',
	plan_time varchar(60) comment '计划完成时间',
	end_time varchar(60) comment '实际完成日期及情况',
	add_name varchar(20) comment '添加的人',
	add_time varchar(15) comment '添加的时间'
);

--公告表
create table rzb_zxh_inform(
	id int unsigned not null primary key auto_increment,
	content varchar(120) comment '公告内容',
	add_name varchar(20) comment '添加的人',
	add_time varchar(15) comment '添加的时间'
);


--周计划修改信息表
create table rzb_zxh_weekplan_log(
	id int unsigned not null primary key auto_increment,
	operate varchar(10) comment '操作(修改,更新)',
	op_name varchar(20) comment '操作的人',
	op_id varchar(10) comment '操作行id',
	op_theme varchar(50) comment '操作行主题',
	op_number varchar(15) comment '操作行序号',
	user_name varchar(20) comment '被操作的人',
	`samaccountname` varchar(32) DEFAULT NULL comment '被操作的人的工号',
	op_time varchar(15) comment '操作的时间',
	preview varchar(20) comment '预览的控制器名',
	state varchar(5) comment '是否查看yes or no'
);