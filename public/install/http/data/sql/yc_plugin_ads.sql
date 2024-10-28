
DROP TABLE IF EXISTS `__PREFIX__plugin_ads`;
CREATE TABLE `__PREFIX__plugin_ads`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int NULL DEFAULT NULL COMMENT '项目ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '位置标识',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '位置名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '广告标题',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图片链接',
  `link_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '跳转链接',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用插件-图片广告' ROW_FORMAT = DYNAMIC;
