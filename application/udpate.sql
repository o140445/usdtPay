-- 服务器列表
CREATE TABLE IF NOT EXISTS `fa_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器描述',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='服务器列表';

-- 支付配置
CREATE TABLE IF NOT EXISTS `fa_pay_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '支付名称',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '支付代码',
  `sign` varchar(255) NOT NULL DEFAULT '' COMMENT '唯一标识',
  `usdt_address` varchar(255) NOT NULL DEFAULT '' COMMENT 'USDT地址',
  `image` varchar(1024) NOT NULL DEFAULT '' COMMENT '图片',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1启用 0禁用',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付配置';

-- 订单
CREATE TABLE IF NOT EXISTS `fa_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(255) NOT NULL DEFAULT '' COMMENT '订单号',
  `pay_no` varchar(255) NOT NULL DEFAULT '' COMMENT '支付订单号',
  `server_id` int(11) NOT NULL DEFAULT '0' COMMENT '服务器ID',
  `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `contact` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式',
  `pay_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `actual_amount` decimal(10,4) NOT NULL DEFAULT '0.00' COMMENT '实际金额',
  `amount_nonce` decimal(10,4) NOT NULL DEFAULT '0.00' COMMENT '随机数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0未支付 1已支付 2支付失败',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `expire_time` int(11) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
    KEY `order_no` (`order_no`),
    KEY `server_id` (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单';


-- 公告
CREATE TABLE IF NOT EXISTS `fa_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '内容',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1启用 0禁用',
  `position` tinyint(1) NOT NULL DEFAULT '1' COMMENT '位置 1公告 2金额提示',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公告';

-- 消息通知
CREATE TABLE IF NOT EXISTS `fa_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1未读 2已读',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息通知';