DROP TABLE IF EXISTS `__PREFIX__system_plugins_domains`;

CREATE TABLE `__PREFIX__system_plugins_domains`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `host` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `plugin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `saas_appid` int NULL DEFAULT NULL,
  `create_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
