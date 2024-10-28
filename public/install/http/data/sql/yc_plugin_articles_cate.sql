
DROP TABLE IF EXISTS `__PREFIX__plugin_articles_cate`;
CREATE TABLE `__PREFIX__plugin_articles_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int NULL DEFAULT NULL COMMENT '项目ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类标题',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `sort` int NULL DEFAULT NULL COMMENT '分类排序',
  `alias` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用插件-文章分类' ROW_FORMAT = DYNAMIC;
