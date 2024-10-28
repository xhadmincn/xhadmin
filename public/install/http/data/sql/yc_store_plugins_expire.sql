DROP TABLE IF EXISTS `__PREFIX__store_plugins_expire`;
CREATE TABLE `__PREFIX__store_plugins_expire`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` int NULL DEFAULT NULL,
  `store_plugins_id` int NULL DEFAULT NULL,
  `auth_num` int NULL DEFAULT NULL,
  `expire_time` date NULL DEFAULT NULL,
  `create_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;