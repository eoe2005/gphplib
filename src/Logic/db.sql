CREATE TABLE `t_sys_admin`
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

CREATE TABLE `t_sys_conf`
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

CREATE TABLE `t_sys_links`
(
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `target_type` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '类型:10网站，20app,30ios',
    `name`        VARCHAR(100)    NOT NULL DEFAULT '' COMMENT '名称',
    `img`         VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '图片',
    `links`       VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '地址',
    `content`     VARCHAR(4096)   NOT NULL DEFAULT '' COMMENT '描述',
    `is_show`     TINYINT         NOT NULL DEFAULT 0 COMMENT '是否显示',
    `is_del`      TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip`   VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip`   VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='友情链接配置';

CREATE TABLE `t_sys_dot_conf`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`      VARCHAR(50)     NOT NULL DEFAULT '' COMMENT '配置名称',
    `conf_key`  VARCHAR(32)     NOT NULL DEFAULT '' COMMENT '打点key',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `uniq_key` (`conf_key`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='打点配置';

CREATE TABLE `t_sys_dot_log`
(
    `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `day`       DATETIME        NOT NULL COMMENT '日期',
    `conf_id`   BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置ID',
    `val`       BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '值',
    `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
    `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
    `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
    PRIMARY KEY (`id`),
    KEY `idx_create` (`create_at`),
    KEY `idx_update` (`update_at`),
    UNIQUE KEY `uniq_conf_day` (`conf_id`, `day`),
    KEY `idx_day` (`day`)
) ENGINE = innodb
  DEFAULT CHARSET = utf8mb4 COMMENT ='打点日志';

