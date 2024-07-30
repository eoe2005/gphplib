CREATE TABLE `t_admin`
(
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `login_name` varchar(50)     NOT NULL DEFAULT '' COMMENT '登录名1',
    `nick_name`  VARCHAR(30)     NOT NULL DEFAULT '' COMMENT '昵称2',
    `avatar`     VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '头像3',
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
  DEFAULT CHARSET = utf8mb4 COMMENT ='管理员账测试一下号';



