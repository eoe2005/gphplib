CREATE TABLE `t_admin`
(
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `login_name` varchar(50)     NOT NULL DEFAULT '' COMMENT '登录名',
    `nick_name`  VARCHAR(30)     NOT NULL DEFAULT '' COMMENT '昵称',
    `avatar`     VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '头像',
    `passwd`     VARCHAR(32)     NOT NULL DEFAULT '' COMMENT '密码',
    `sign`       VARCHAR(32)     NOT NULL DEFAULT '' COMMENT '密码加盐',
    `status`     TINYINT         not null DEFAULT 0 COMMENT '状态，0正常，10禁止登录',
    `is_del`     TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `uniq_loginname` (`login_name`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='管理员账号';


CREATE TABLE `t_admin_log`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`  BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
    `op`        VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '操作',
    `desc`      VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '描述',
    `diff`      text COMMENT '前后差异',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_admin` (`admin_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='管理员操作日志';


CREATE TABLE `t_goods_category`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级分类id',
    `name`      VARCHAR(30)     NOT NULL DEFAULT '' COMMENT '分类名字',
    `icon`      VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '分类icon',
    `avatar`    VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '分类图片',
    `is_show`   TINYINT         NOT NULL DEFAULT 1 COMMENT '是否显示',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `uniq_pname` (`parent_id`, `name`)

) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品分类';

CREATE TABLE `t_goods`
(
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor_id`          bigint unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
    `goods_name`         VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '商品名称',
    `goods_imgs`         VARCHAR(4096)   NOT NULL DEFAULT '' COMMENT '商品图片，逗号分隔',
    `category1`          BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '一级分类ID',
    `category2`          BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '二级分类ID',
    `score_rate`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '评分',
    `evaluates`          INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '评价次数',
    `market_price`       INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '市场价，单位分',
    `cost_price`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '成本价，单位分',
    `sell_price`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '售价，单位分',
    `seckill_price`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀价格，单位分',
    `stock`              INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '库存',
    `lock_stock`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '锁定库存',
    `seckill_stock`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀库存',
    `seckill_lock_stock` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀锁定库存',
    `limit_cycle`        VARCHAR(10)     NOT NULL DEFAULT '' COMMENT '限购周期',
    `limit_num`          INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '限购周期内购买数量',
    `limit_msg`          VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '限购消息',
    `labor_costs`        INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '工费',
    `spec_id`            bigint unsigned NOT NULL DEFAULT '0' COMMENT '商品模型',
    `freight_id`         BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '运费模版',
    `send_time`          tinyint         NOT NULL DEFAULT 0 COMMENT '发货时间',
    `sells`              INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '销量',
    `is_online`          TINYINT         NOT NULL DEFAULT 1 COMMENT '是否上架',
    `is_hot`             TINYINT         NOT NULL DEFAULT 0 COMMENT '是否热门',
    `is_new`             TINYINT         NOT NULL DEFAULT 0 COMMENT '是否新品',
    `is_recommend`       TINYINT         NOT NULL DEFAULT 0 COMMENT '是否推荐',
    `goods_content`      TEXT            NOT NULL COMMENT '商品介绍',
    `status`             TINYINT         NOT NULL DEFAULT 0 COMMENT '状态：0待审核，1审核成功，2审核失败',
    `audit_msg`          VARCHAR(128)    NOT NULL DEFAULT '' COMMENT '审核失败原因',
    `is_del`             TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`          VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`          VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_cate` (`category1`, `category2`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品信息表';

CREATE TABLE `t_goods_specs`
(
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '供应商ID',
    `name`        VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '名称',
    `level`       TINYINT         NOT NULL DEFAULT 0 COMMENT '0商品模型，1分类，2具体项',
    `root_id`     BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品模型ID',
    `type_id`     BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID',
    `gold_weight` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '黄金类型克数',
    `is_del`      TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`   VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`   VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_root` (`root_id`),
    KEY `idx_level` (`level`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品规格';

CREATE TABLE `t_goods_spec_price`
(
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `goods_id`           BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
    `item_key`           VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '规格ID，使用_分隔，升序排列',
    `item_name`          VARCHAR(1024)   NOT NULL DEFAULT '' COMMENT '名称',
    `item_color_bg`      VARCHAR(10)     NOT NULL DEFAULT '' COMMENT '背景颜色',
    `item_color`         VARCHAR(10)     NOT NULL DEFAULT '' COMMENT '文字颜色',
    `item_icon`          VARCHAR(255)    NOT NULL DEFAULT '' COMMENT 'icon',
    `item_img`           VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '图片地址',
    `market_price`       INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '市场价，单位分',
    `cost_price`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '成本价，单位分',
    `sell_price`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '售价，单位分',
    `seckill_price`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀价格，单位分',
    `stock`              INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '库存',
    `lock_stock`         INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '锁定库存',
    `seckill_stock`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀库存',
    `seckill_lock_stock` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀锁定库存',
    `limit_cycle`        VARCHAR(10)     NOT NULL DEFAULT '' COMMENT '限购周期',
    `limit_num`          INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '限购周期内购买数量',
    `limit_msg`          VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '限购消息',
    `is_del`             TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`          VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`          VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `idx_goodx_key` (`goods_id`, `item_key`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品价格表';


CREATE TABLE `t_goods_recharge`
(
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            varchar(30)     NOT NULL DEFAULT '' COMMENT '充值类型',
    `type`            varchar(20)     NOT NULL DEFAULT '' COMMENT '充值类型：mobile话费，electricity电费，gas天然气',
    `icon`            varchar(255)    NOT NULL DEFAULT '' COMMENT '充值icon',
    `img`             varchar(255)    NOT NULL DEFAULT '' COMMENT '图片',
    `sort_index`      int             NOT NULL DEFAULT '0' COMMENT '排序',
    `is_online`       tinyint         NOT NULL DEFAULT '0' COMMENT '是否上线',
    `waring`          varchar(100)    NOT NULL DEFAULT '' COMMENT '提示信息',
    `notice`          varchar(4096)   NOT NULL DEFAULT '' COMMENT '描述信息',
    `limit_day`       varchar(10)     NOT NULL DEFAULT '' COMMENT '限制天',
    `limit_num`       int             NOT NULL DEFAULT '0' COMMENT '限制数量',
    `limit_msg`       varchar(150)    NOT NULL DEFAULT '' COMMENT '限制提示信息',
    `in_week`         varchar(10)     NOT NULL DEFAULT '' COMMENT '充值日期',
    `in_start`        tinyint         NOT NULL DEFAULT '0' COMMENT '开始时间',
    `in_end`          tinyint         NOT NULL DEFAULT '24' COMMENT '开始时间',
    `not_in_time_msg` varchar(150)    NOT NULL DEFAULT '' COMMENT '时间提示',
    `is_del`          TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='充值中心';

CREATE TABLE `t_goods_recharge_price`
(
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `goods_id`        int             NOT NULL DEFAULT '0' COMMENT '充值商品ID',
    `name`            varchar(10)     NOT NULL DEFAULT '' COMMENT '名称',
    `price`           INT             NOT NULL DEFAULT '0.00' COMMENT '价格',
    `cost_price`      INT             NOT NULL DEFAULT '0.00' COMMENT '成本价格',
    `vendor`          varchar(20)     NOT NULL DEFAULT '' COMMENT '供应商',
    `vendor_goods_id` int             NOT NULL DEFAULT '0' COMMENT '供应商提供的商品ID',
    `is_online`       tinyint         NOT NULL DEFAULT '0' COMMENT '是否上线',
    `sort_index`      int             NOT NULL DEFAULT '0' COMMENT '显示顺序',
    `match_type`      varchar(20)     NOT NULL DEFAULT '' COMMENT '匹配类型：city|province',

    `is_del`          TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_goodsid` (`goods_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='充值价格';

CREATE TABLE `t_goods_freight`
(
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vendor_id`     BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '供应商ID',
    `name`          VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '运费规则名',
    `parent_id`     BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父规则ID',
    `is_free`       TINYINT         NOT NULL DEFAULT 1 COMMENT '是否包邮',
    `is_pay_online` TINYINT         NOT NULL DEFAULT 1 COMMENT '是否线上付款',
    `default_num`   INT UNSIGNED    NOT NULL DEFAULT 1 COMMENT '默认数量',
    `default_price` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '默认价格',
    `out_num`       INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '超出部分',
    `out_price`     INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '超出价格',
    `address`       VARCHAR(2048)   NOT NULL DEFAULT '' COMMENT '规则省份',
    `is_del`        TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`     VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`     VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_parent_id` (`parent_id`),
    KEY `idx_vendor_id` (`vendor_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='运费规则表';


CREATE TABLE `t_goods_vendor`
(
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '供应商名字',
    `passwd`         VARCHAR(32)     NOT NULL DEFAULT '' COMMENT '登录密码',
    `contact`        VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '联系人',
    `contact_mobile` VARCHAR(15)     NOT NULL DEFAULT '' COMMENT '联系人电话',
    `consignee`      VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '收货人',
    `mobile`         VARCHAR(15)     NOT NULL DEFAULT '' COMMENT '电话',
    `province`       VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '省份',
    `city`           VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '城市',
    `district`       VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '地区',
    `address`        VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '详细地址',
    `status`         TINYINT         NOT NULL DEFAULT 0 COMMENT '状态：0正常，1禁止登录',
    `is_del`         TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`      VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`      VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_name` (`name`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='供应商';


CREATE TABLE `t_content_article`
(
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '标题',
    `sub_title`  VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '副标题',
    `category`   VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '分类',
    `img`        VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '图片',
    `video`      VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '视频',
    `content`    TEXT COMMENT '内容',
    `views`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '显示次数',
    `sort_index` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '排序',
    `is_del`     TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_category_sort` (`category`, `sort_index`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='文章列表';

CREATE TABLE `t_content_gold_price`
(
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '名称',
    `price`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '价格',
    `is_show`    TINYINT         NOT NULL DEFAULT 1 COMMENT '是否展示',
    `sort_index` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '排序',
    `is_del`     TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='黄金价格列表';


CREATE TABLE `t_order`
(
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_sn`       VARCHAR(64)     NOT NULL DEFAULT '' COMMENT '订单编号',
    `order_type`     TINYINT         NOT NULL DEFAULT 0 COMMENT '订单类型：0商品订单，1秒杀订单，10花费充值订单,11电费充值，12燃气充值',
    `vendor_id`      BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '供应商ID',
    `user_id`        BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `order_status`   TINYINT         NOT NULL DEFAULT 0 COMMENT '订单状态：0待支付,10已取消，20支付完成，21已发货，22确认收货，23评价完成',
    `consignee`      varchar(60)              DEFAULT NULL COMMENT '收货人',
    `mobile`         varchar(60)              DEFAULT NULL COMMENT '手机',
    `province`       varchar(100)             DEFAULT NULL COMMENT '省份',
    `city`           varchar(100)             DEFAULT NULL COMMENT '城市',
    `district`       varchar(100)             DEFAULT NULL COMMENT '县区',
    `address`        varchar(255)             DEFAULT NULL COMMENT '地址',
    `user_note`      varchar(512)    NOT NULL DEFAULT '' COMMENT '用户备注',
    `total_amount`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '订单总额',
    `cost_amount`    INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '成本',
    `order_amount`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '应付款金额',
    `feight_money`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '运费',
    `send_type`      TINYINT                  DEFAULT '0' COMMENT '1自提2快递',
    `transaction_id` VARCHAR(128)    NOT NULL DEFAULT '' COMMENT '三方支付单号',
    `pay_way`        TINYINT         NOT NULL DEFAULT 0 COMMENT '支付方式 1微信，2支付宝 3银联',

    `express_code`   VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递公司编码',
    `express_name`   VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递公司名字',
    `express_num`    VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递编号',

    `cancel_at`      DATETIME                 DEFAULT NULL COMMENT '取消时间',
    `pay_at`         DATETIME                 DEFAULT NULL COMMENT '支付时间',
    `send_at`        DATETIME                 DEFAULT NULL COMMENT '发货时间',
    `confirm_at`     DATETIME                 DEFAULT NULL COMMENT '确认收货时间',
    `end_at`         DATETIME                 DEFAULT NULL COMMENT '到期时间',

    `is_del`         TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`      VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`      VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_vendor_id` (`vendor_id`),
    KEY `idx_user` (`user_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='订单表';
CREATE TABLE `t_order_goods`
(
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`        BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID',
    `user_id`         BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买人',
    `goods_id`        BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单商品ID',
    `vendor_id`       BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '供应商ID',
    `goods_item_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品规格ID',
    `goods_name`      VARCHAR(200)    NOT NULL DEFAULT '' COMMENT '商品名字',
    `goods_img`       VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '商品图片',
    `goods_item_name` VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '商品规格名称',
    `goods_item_key`  VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '商品规格KEY',
    `price`           INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '售价',
    `cost_price`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '成本价',
    `kill_price`      INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '秒杀价',
    `pay_price`       INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '支付价格',
    `freight_price`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '运费价格',
    `goods_num`       INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '购买数量',
    `express_code`    VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递公司编码',
    `express_name`    VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递公司名字',
    `express_num`     VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '快递编号',
    `cancel_at`       DATETIME                 DEFAULT NULL COMMENT '取消时间',
    `pay_at`          DATETIME                 DEFAULT NULL COMMENT '支付时间',
    `send_at`         DATETIME                 DEFAULT NULL COMMENT '发货时间',
    `confirm_at`      DATETIME                 DEFAULT NULL COMMENT '确认收货时间',
    `is_del`          TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`       VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_order` (`order_id`),
    KEY `idx_goods` (`goods_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='订单商品';


CREATE TABLE `t_app_conf`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conf_key`  VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '分组KEY',
    `group_key` VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '中间key',
    `item_key`  VARCHAR(50)     NOT NULL DEFAULT '' COMMENT 'key',
    `val`       TEXT            NOT NULL COMMENT '具体的值',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `uniq_conf_group_item` (`conf_key`, `group_key`, `item_key`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='系统配置表';


CREATE TABLE `t_log_req`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `req_type`  INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '请求类型：1万联充值',
    `ref_id`    BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '查询id',
    `req_url`   VARCHAR(2048)   NOT NULL DEFAULT '' COMMENT '请求地址',
    `req_data`  text COMMENT '请求参数',
    `rsp_code`  INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '返回状态',
    `res_data`  TEXT COMMENT '返回内容',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_type_ref` (`req_type`, `ref_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='网络请求日志';

CREATE TABLE `t_app_mq`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `run_times` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '执行次数',
    `consumer`  VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '执行脚本',
    `next_time` DATETIME        NOT NULL COMMENT '下次执行时间',
    `is_done`   TINYINT         NOT NULL DEFAULT 0 COMMENT '是否已经完成',
    `msg`       TEXT            NOT NULL COMMENT '消息内容',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_done_time` (`is_done`, `next_time`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='消息队列';


CREATE TABLE `t_user_address`
(
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `consignee`  varchar(60)     NOT NULL DEFAULT '' COMMENT '收货人',
    `province`   varchar(100)    NOT NULL DEFAULT '0' COMMENT '省份',
    `city`       varchar(100)    NOT NULL DEFAULT '0' COMMENT '城市',
    `district`   varchar(100)    NOT NULL DEFAULT '0' COMMENT '地区',
    `address`    varchar(120)    NOT NULL DEFAULT '' COMMENT '地址',
    `mobile`     varchar(60)     NOT NULL DEFAULT '' COMMENT '手机',
    `is_default` tinyint(1)               DEFAULT '0' COMMENT '默认收货地址',
    `is_del`     TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`  VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    KEY `idx_user` (`user_id`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='用户收货地址表';



CREATE TABLE `t_`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='';
