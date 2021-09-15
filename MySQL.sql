CREATE TABLE `user`
(
    `id`                BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`          VARCHAR(32)         NOT NULL DEFAULT '' COMMENT '用户名',
    `password`          VARCHAR(255)        NOT NULL DEFAULT '' COMMENT '密码',
    `phone`             VARCHAR(16)         NOT NULL DEFAULT '' COMMENT '手机号码',
    `avatar`            VARCHAR(256)        NOT NULL DEFAULT '' COMMENT '头像',
    `subdistrict_id`    INT                 NOT NULL DEFAULT 0 COMMENT '小区id',
    `salt`              VARCHAR(16)         NOT NULL DEFAULT '' COMMENT '盐值',
    `openid`            VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '微信openid',
    `created_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_sign_in_time` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最后登录时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='用户信息表';

CREATE TABLE `user_sign_in`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户',
    `ip_address`   VARCHAR(255)        NOT NULL DEFAULT '' COMMENT 'IP 地址',
    `sign_in_time` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登录时间',
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='用户登录记录表';

CREATE TABLE IF NOT EXISTS `circle_posts`
(
    `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`        BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
    `content`    VARCHAR(600)                 DEFAULT '' COMMENT '动态类容',
    `images`     VARCHAR(512)                 DEFAULT '' COMMENT '动态图片',
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='圈子动态';

CREATE TABLE IF NOT EXISTS `circle_comments`
(
    `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `post_id`    BIGINT(20) UNSIGNED NOT NULL COMMENT '圈子动态id',
    `uid`        BIGINT(20) UNSIGNED NOT NULL COMMENT '评论用户id',
    `reply_uid`  BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被回复用户id',
    `content`    VARCHAR(600)                 DEFAULT '' COMMENT '评论类容',
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='圈子动态评论';

CREATE TABLE IF NOT EXISTS `car_places`
(
    `id`              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`             BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
    `subdistrict_id`  INT                 NOT NULL COMMENT '小区id',
    `type`            ENUM ('出租', '出售')            DEFAULT '出租' COMMENT '车位状态',
    `price`           FLOAT               NOT NULL DEFAULT 0 COMMENT '车位租售价格',
    `post_status`     TINYINT(2) UNSIGNED          DEFAULT 2 COMMENT '内容状态: 1-下架，2-发布',
    `is_standard`     TINYINT(2) UNSIGNED          DEFAULT 1 COMMENT '是否是标准车位: 1-不是，2-是',
    `floorage`        FLOAT               NOT NULL DEFAULT 0 COMMENT '建筑面积',
    `floor`           VARCHAR(16)         NOT NULL DEFAULT '负一楼' COMMENT '楼层',
    `subdistrict`     VARCHAR(64)         NOT NULL COMMENT '小区名称',
    `building_number` INT                 NOT NULL DEFAULT 1 COMMENT '楼号',
    `describe`        VARCHAR(512)                 DEFAULT '' COMMENT '车位描述',
    `phone_number`    VARCHAR(11)                  DEFAULT '' COMMENT '手机号码',
    `weixin`          VARCHAR(20)                  DEFAULT '' COMMENT '微信号',
    `images`          VARCHAR(128)                 DEFAULT '' COMMENT '图片',
    `created_at`      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='车位';

CREATE TABLE IF NOT EXISTS `car_place_comments`
(
    `id`           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `car_place_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '车位id',
    `uid`          BIGINT(20) UNSIGNED NOT NULL COMMENT '评论用户id',
    `reply_uid`    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被回复用户id',
    `content`      VARCHAR(600)                 DEFAULT '' COMMENT '评论类容',
    `created_at`   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='车位评论';

CREATE TABLE IF NOT EXISTS `secondhand_goods`
(
    `id`             BIGINT(20) UNSIGNED                           NOT NULL AUTO_INCREMENT,
    `uid`            BIGINT(20) UNSIGNED                           NOT NULL COMMENT '评论用户id',
    `title`          VARCHAR(128)                                           DEFAULT '' COMMENT '商品标题',
    `images`         VARCHAR(128)                                           DEFAULT '' COMMENT '图片',
    `price`          FLOAT                                         NOT NULL DEFAULT 0 COMMENT '商品价格',
    `original_price` FLOAT                                         NOT NULL DEFAULT 0 COMMENT '商品原始价格',
    `describe`       VARCHAR(600)                                           DEFAULT '' COMMENT '描述类容',
    `delivery`       ENUM ('自取', '包邮')                                      DEFAULT '自取' COMMENT '配送方式',
    `address`        VARCHAR(256)                                  NOT NULL DEFAULT '' COMMENT '商品地址',
    `category`       ENUM ('数码产品', '家用电器', '儿童玩具', '家居用品', '其他物品') NOT NULL COMMENT '商品分类',
    `status`         TINYINT(2) UNSIGNED                                    DEFAULT 2 COMMENT '商品状态: 1-下架，2-发布',
    `created_at`     TIMESTAMP                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='二手闲置商品';

CREATE TABLE IF NOT EXISTS `secondhand_goods_comments`
(
    `id`                  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `secondhand_goods_id` BIGINT(20) UNSIGNED NOT NULL COMMENT '二手商品id',
    `uid`                 BIGINT(20) UNSIGNED NOT NULL COMMENT '评论用户id',
    `reply_uid`           BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被回复用户id',
    `content`             VARCHAR(600)                 DEFAULT '' COMMENT '评论类容',
    `created_at`          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='二手闲置商品评论';

CREATE TABLE IF NOT EXISTS `pinche`
(
    `id`                  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`                 BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
    `type`                TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '拼车类型: 1-人找车，2-车找人',
    `status`              TINYINT(2) UNSIGNED          DEFAULT 2 COMMENT '内容状态: 1-下架，2-发布',
    `departure_city_id`   VARCHAR(7)          NOT NULL COMMENT '出发地城市id',
    `destination_city_id` VARCHAR(7)          NOT NULL COMMENT '目的地城市id',
    `departure_geohash`   VARCHAR(16)         NOT NULL COMMENT '',
    `destination_geohash` VARCHAR(16)         NOT NULL COMMENT '',
    `departure_name`      VARCHAR(128)        NOT NULL COMMENT '出发地名字',
    `departure_address`   VARCHAR(128)        NOT NULL COMMENT '出发地地址',
    `destination_name`    VARCHAR(128)        NOT NULL COMMENT '目的地名字',
    `destination_address` VARCHAR(128)        NOT NULL COMMENT '目的地地址',
    `departure_lat`       DECIMAL(10, 6)      NOT NULL COMMENT '出发地纬度',
    `departure_lng`       DECIMAL(10, 6)      NOT NULL COMMENT '出发地经度',
    `destination_lat`     DECIMAL(10, 6)      NOT NULL COMMENT '目的地纬度',
    `destination_lng`     DECIMAL(10, 6)      NOT NULL COMMENT '目的地经度',
    `condition`           VARCHAR(512)                 DEFAULT '' COMMENT '乘车条件',
    `price`               FLOAT               NOT NULL DEFAULT 0 COMMENT '路费价格',
    `sex`                 ENUM ('男', '女')              DEFAULT '男' COMMENT '性别',
    `username`            VARCHAR(64)         NOT NULL DEFAULT '' COMMENT '名称',
    `mobile`              VARCHAR(11)         NOT NULL DEFAULT '' COMMENT '电话',
    `start_time`          INT                 NOT NULL COMMENT '出发时间',
    `seat_num`            INT                 NOT NULL DEFAULT 3 COMMENT '座位数',
    `images`              VARCHAR(128)                 DEFAULT '' COMMENT '图片',
    `created_at`          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='拼车';

CREATE TABLE IF NOT EXISTS `collects`
(
    `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`        BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
    `type`       TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '收藏类型: 1-车位，2-拼车',
    `title`      TINYINT(2) UNSIGNED          DEFAULT 2 COMMENT '收藏标题',
    `post_id`    INT                 NOT NULL COMMENT '收藏数据id',
    `images`     VARCHAR(128)                 DEFAULT '' COMMENT '图片',
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='收藏';

CREATE TABLE IF NOT EXISTS `rent`
(
    `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`        BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
    `type`       TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '租用类型: 1-出租，2-找租',
    `status`     TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '发布状态: 1-下架，2-发布',
    `title`      VARCHAR(64)         NOT NULL COMMENT '标题',
    `mobile`     VARCHAR(11)         NOT NULL COMMENT '手机号码',
    `price`      VARCHAR(11)         NOT NULL COMMENT '价格',
    `images`     VARCHAR(128)                 DEFAULT '' COMMENT '图片',
    `desc`       VARCHAR(1024)                DEFAULT '' COMMENT '描述',
    `address`    VARCHAR(64)                  DEFAULT '' COMMENT '地址',
    `lat`        DECIMAL(10, 6)      NOT NULL COMMENT '纬度',
    `lng`        DECIMAL(10, 6)      NOT NULL COMMENT '经度',
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='租用';

CREATE TABLE IF NOT EXISTS `address`
(
    `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uid`         BIGINT(20) UNSIGNED NOT NULL COMMENT '用户id',
    `name`        VARCHAR(32)         NOT NULL COMMENT '用户名称',
    `mobile`      VARCHAR(11)         NOT NULL COMMENT '手机号码',
    `mark`        VARCHAR(1024)                DEFAULT '' COMMENT '备注',
    `address`     VARCHAR(64)                  DEFAULT '' COMMENT '地址',
    `gps_address` VARCHAR(64)                  DEFAULT '' COMMENT '定位地址',
    `lat`         DECIMAL(10, 6)      NOT NULL COMMENT '纬度',
    `lng`         DECIMAL(10, 6)      NOT NULL COMMENT '经度',
    `is_default`  TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '是否是默认地址：1-不是，2-是默认地址',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='用户地址';

CREATE TABLE IF NOT EXISTS `recycle_order`
(
    `id`               BIGINT(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `uid`              BIGINT(20) UNSIGNED                                         NOT NULL COMMENT '用户id',
    `address_id`       INT                                                         NOT NULL COMMENT '用户订单地址',
    `appointment_time` INT(11)                                                     NOT NULL COMMENT '预约时间',
    `category`         ENUM ('paper', 'plastic', 'metal', 'clothes', 'electronic') NOT NULL COMMENT '废品分类',
    `weight`           VARCHAR(32)                                                          DEFAULT '' COMMENT '预估重量',
    `actual_weight`    FLOAT                                                                DEFAULT 0.0 COMMENT '实际重量',
    `status`           TINYINT(1)                                                  NOT NULL DEFAULT 1 COMMENT '回收订单状态：1-预约，2-已经接单，3-完成，4-取消',
    `mark`             VARCHAR(1024)                                                        DEFAULT '' COMMENT '备注',
    `created_at`       TIMESTAMP                                                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP                                                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='回收订单';

CREATE TABLE IF NOT EXISTS `orders`
(
    `id`               BIGINT(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `uid`              BIGINT(20) UNSIGNED                                         NOT NULL COMMENT '用户id',
    `address_id`       INT                                                         NOT NULL COMMENT '用户订单地址',
    `appointment_time` INT(11)                                                     NOT NULL COMMENT '预约时间',
    `category`         ENUM ('paper', 'plastic', 'metal', 'clothes', 'electronic') NOT NULL COMMENT '废品分类',
    `weight`           VARCHAR(32)                                                          DEFAULT '' COMMENT '预估重量',
    `actual_weight`    FLOAT                                                                DEFAULT 0.0 COMMENT '实际重量',
    `status`           TINYINT(1)                                                  NOT NULL DEFAULT 1 COMMENT '回收订单状态：1-预约，2-已经接单，3-完成，4-取消',
    `mark`             VARCHAR(1024)                                                        DEFAULT '' COMMENT '备注',
    `created_at`       TIMESTAMP                                                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP                                                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='回收订单';

CREATE TABLE IF NOT EXISTS `categories`
(
    `id`                 BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(32)         NOT NULL COMMENT '分类名称',
    `sort`               INT                 NOT NULL COMMENT '排序',
    `category_image_url` VARCHAR(64)         NOT NULL COMMENT '分类图片',
    `created_at`         TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品分类';

CREATE TABLE IF NOT EXISTS `products`
(
    `id`               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`             VARCHAR(32)         NOT NULL COMMENT '商品名称',
    `no`               VARCHAR(32)         NOT NULL COMMENT '商品编号',
    `description`      VARCHAR(256)        NOT NULL COMMENT '商品描述',
    `category_id`      INT                 NOT NULL COMMENT '分类id',
    `material_id`      INT                 NOT NULL COMMENT '商品辅料id',
    `label_id`         INT                 NOT NULL COMMENT '商品标签id',
    `support_takeaway` TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '是否能带出：1-可以，2-不可以',
    `sort`             INT                 NOT NULL COMMENT '排序',
    `price`            FLOAT               NOT NULL COMMENT '价格',
    `images`           VARCHAR(128)                 DEFAULT '' COMMENT '图片',
    `created_at`       TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品';

CREATE TABLE IF NOT EXISTS `labels`
(
    `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT                 NOT NULL COMMENT '分类id',
    `name`        VARCHAR(32)         NOT NULL COMMENT '标签名称',
    `color`       VARCHAR(8)                   DEFAULT '#BABABA' COMMENT '标签颜色',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品标签';

CREATE TABLE IF NOT EXISTS `materials`
(
    `id`          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT                 NOT NULL COMMENT '分类id',
    `name`        VARCHAR(32)         NOT NULL COMMENT '名称',
    `params`      VARCHAR(512)        NOT NULL COMMENT '辅料要求',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='商品辅料';
