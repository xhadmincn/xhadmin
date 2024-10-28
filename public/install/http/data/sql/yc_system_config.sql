DROP TABLE IF EXISTS `__PREFIX__system_config`;

CREATE TABLE `__PREFIX__system_config`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据值',
  `store_id` int(10) NULL DEFAULT NULL COMMENT '渠道ID',
  `saas_appid` int(10) NULL DEFAULT NULL COMMENT '应用ID',
	PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-配置项' ROW_FORMAT = DYNAMIC;
