DROP TABLE IF EXISTS `__PREFIX__store_plugins`;

CREATE TABLE `__PREFIX__store_plugins`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` int NULL DEFAULT NULL,
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `plugin_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `auth_num` int NULL DEFAULT 0,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;