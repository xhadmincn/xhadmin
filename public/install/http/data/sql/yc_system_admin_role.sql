DROP TABLE IF EXISTS `__PREFIX__system_admin_role`;

CREATE TABLE `__PREFIX__system_admin_role`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `pid` int NULL DEFAULT 0 COMMENT '上级管理员，0顶级',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '部门名称',
  `rule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '部门权限',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，1是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-角色管理' ROW_FORMAT = DYNAMIC;

INSERT INTO `__PREFIX__system_admin_role` VALUES (1, '2022-10-28 07:11:51', '2022-11-16 11:30:39', 0, '系统管理员', '[\"Index\\/index\",\"SystemConfigGroup\\/table\",\"Plugin\\/create\"]', '20');
