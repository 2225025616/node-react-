

create table `user` (
  `id`  integer AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) not null comment '姓名',
  `phone_number` varchar(20) not null comment '手机号码',
  `id_card` varchar(20) comment '身份证号',
  `address` varchar(50) comment '钱包地址',
  `department` varchar(100) comment '部门',
  `createdAt` datetime default now() comment '创建时间',
  `updatedAt` datetime comment '修改时间',
  `status` tinyint(1) default 0 comment '0 正常 1 离职', 
  primary key (`id`),
  unique index `address` (`address`)
);

create table `admin` (
  `id`  integer AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) not null comment '姓名',
  `phone_number` varchar(20) not null comment '手机号码',
  `id_card` varchar(20) comment '身份证号',
  `createdAt` datetime default now() comment '创建时间',
  `updatedAt` datetime comment '修改时间',
  `username` varchar(50) not null comment '登陆用户名',
  `pw` varchar(128) not null comment '密码',
  `salt` varchar(16) not null comment 'pw加密salt',
  `token` varchar(128) not null comment '登陆token',
  primary key (`id`),
  unique index `username` (`username`),
  unique index `token` (`token`)
);


create table `stock_option` (
  `id` integer AUTO_INCREMENT comment 'ID',
  `symbol` varchar(10) not null comment '期权代号',
  `supply` int comment '发行量',
  `open_time` datetime comment '期权开始时间',
  `expire_time` datetime comment '期权到期时间',
  `price` int comment '行权价格',
  `freeze` int comment '冻结比例',
  `contract_address` varchar(50) comment '合约地址',
  `createdAt` datetime default now() comment '创建时间',
  `period` tinyint(2) comment '期权期号（0：股权）',
  primary key (`id`),
  unique index `symbol` (`symbol`),
  unique index `period` (`period`),
  unique index `contract_address` (`contract_address`)

);

create table `transactions` (
  `id` integer AUTO_INCREMENT comment 'ID',
  `from` varchar(50) comment 'from地址',
  `to` varchar(50) comment 'to地址',
  `value` int comment '金额',
  `txHash`  varchar(100) comment '交易hash',
  `type` tinyint(1) comment '0 股权 1 期权一期 2 期权二期',
  `subtype` tinyint(1) comment '1 交易 2 回收 3 发行 4 解锁 5 行权..',
  `time` datetime comment '交易时间',
  primary key (`id`)
);

CREATE TABLE if not exists `configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  `value` varchar(1024) NOT NULL,

  PRIMARY KEY (`id`),
  unique index `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 comment='配置表';

create table `option_expected_batch` (
  `id`  integer AUTO_INCREMENT COMMENT 'ID',
  `option_id` integer comment '期权ID',
  `name` varchar(50) not null comment '名称',
  `status` tinyint(1) default 0 comment '0 未发放，1 已发放',
  `txHash` varchar(70) default NULL COMMENT '交易Hash',
  `createdAt` datetime default now() comment '创建时间',
  primary key (`id`)
);

create table `option_expected_list` (
  `id`  integer AUTO_INCREMENT COMMENT 'ID',
  `user_id` integer  comment '用户ID',
  `batch_id` integer comment '批次ID',
  `value` int comment '发放数量',
  primary key (`id`),
  unique index `option_expected_list_uk` (`user_id`,`batch_id`)
);

CREATE TABLE `option_unlock_remind` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `option_id` int(11) DEFAULT NULL COMMENT '期权ID',
  `datetime` datetime DEFAULT NULL COMMENT '日期',
  `value` int(11) DEFAULT NULL COMMENT '数量（比例）',
  `status` tinyint(1) DEFAULT '0' COMMENT '0 未解锁，1 已解锁',
  `txHash` varchar(70) COLLATE utf8_bin DEFAULT NULL COMMENT '交易Hash',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;