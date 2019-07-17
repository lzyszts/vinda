CREATE TABLE `scb_bc_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '姓名',
  `gh` varchar(10) NOT NULL COMMENT '工号',
  `jt_num` varchar(10) NOT NULL COMMENT '机台编号',
  `banci` varchar(10) NOT NULL COMMENT '班次',
  `banzu` tinyint(4) NOT NULL COMMENT '班组',
  `sc_num` varchar(50) NOT NULL COMMENT '生产批号',
  `date` varchar(32) NOT NULL COMMENT '日期',
  `up_date` varchar(20) NOT NULL COMMENT '下纸时间',
  `cp_class` varchar(150) NOT NULL COMMENT '产品种类',
  `cp_num` varchar(50) NOT NULL COMMENT '产品编码',
  `speed` varchar(32) NOT NULL COMMENT '车速(m/min)',
  `dingliang` varchar(32) NOT NULL COMMENT '定量(g/m3)',
  `zhou_num` varchar(32) NOT NULL COMMENT '原纸张轴号',
  `guige` varchar(32) NOT NULL COMMENT '规格',
  `juanchang` varchar(32) NOT NULL COMMENT '卷长(m)',
  `maozhong` varchar(32) NOT NULL COMMENT '毛重(kg)',
  `jingzhong` varchar(32) NOT NULL COMMENT '净重(KG)',
  `buhege` varchar(32) NOT NULL DEFAULT '0' COMMENT '不合格重量',
  `zhijing` varchar(32) NOT NULL COMMENT '直径(mm)',
  `is_hg` varchar(32) NOT NULL COMMENT '是否合格',
  `is_hd` varchar(32) NOT NULL COMMENT '换刀情况',
  `is_dt` tinyint(4) NOT NULL COMMENT '断头个数',
  `zz_level` varchar(15) NOT NULL COMMENT '皱纹级别',
  `next` varchar(15) NOT NULL COMMENT '下道工序',
  `bz` text COMMENT '备注',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `edittime` int(10) DEFAULT NULL COMMENT '修改时间',
  `wg_num` varchar(50) DEFAULT NULL COMMENT '完工单号',
  `is_lock` tinyint(4) DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `scb_bc_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(32) NOT NULL COMMENT '类型名称',
  `cp_num` varchar(32) DEFAULT NULL COMMENT '产品编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8 COMMENT='产品种类表';


CREATE TABLE `scb_bc_next` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `next_name` varchar(32) NOT NULL COMMENT '工序名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8 COMMENT='下道工序表';
