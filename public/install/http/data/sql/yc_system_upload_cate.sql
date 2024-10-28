DROP TABLE IF EXISTS `__PREFIX__system_upload_cate`;

CREATE TABLE `__PREFIX__system_upload_cate`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `store_id` int NULL DEFAULT NULL COMMENT '渠道ID',
  `saas_appid` int NULL DEFAULT NULL COMMENT '应用ID',
  `uid` int NULL DEFAULT NULL COMMENT '用户ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `dir_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类目录',
  `sort` int NULL DEFAULT 0 COMMENT '分类排序',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件分类' ROW_FORMAT = DYNAMIC;

INSERT INTO `__PREFIX__system_upload_cate` VALUES (1, '2022-11-16 20:06:19', '2023-03-06 12:00:36', NULL, NULL, NULL, '默认分类', 'system_upload', 0, '20');
