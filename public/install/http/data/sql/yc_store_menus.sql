DROP TABLE IF EXISTS `__PREFIX__store_menus`;

CREATE TABLE `__PREFIX__store_menus`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求地址：控制器/操作方法',
  `pid` int NULL DEFAULT 0 COMMENT '父级菜单地址',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `sort` int NULL DEFAULT 0 COMMENT '菜单排序，值越大越靠后',
  `method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求类型：GET,POST,PUT,DELETE',
  `is_api` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否接口：10否，20是',
  `component` enum('none/index','form/index','table/index','remote/index') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'none/index' COMMENT '组件类型',
  `auth_params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '附带参数：remote/index，填写远程组件路径名称',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图标类名',
  `show` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否显示：10隐藏，20显示',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '默认权限：10否，20是',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `path`(`path` ASC) USING BTREE COMMENT '唯一索引'
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '渠道-菜单' ROW_FORMAT = DYNAMIC;

INSERT INTO `__PREFIX__store_menus` VALUES (1, '2022-10-27 17:22:51', '2023-10-10 16:53:15', 'Index/index', 0, '控制台', 0, '[\"GET\"]', '10', 'remote/index', '/remote/store/index', 'HomeOutlined', '20', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (2, '2022-10-27 17:22:51', '2023-07-24 14:14:16', 'Index/welcome', 1, '控制台', 0, '[\"GET\"]', '10', 'remote/index', '/remote/store/index', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (3, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/login', 1, '系统登录', 0, '[\"POST\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (4, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/site', 1, '获取应用信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (5, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/user', 1, '获取管理员信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (6, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/menus', 1, '获取菜单信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (7, '2022-11-16 19:33:49', '2023-04-16 17:16:04', 'Uploadify/tabs', 1, '附件模块', 0, '[\"GET\"]', '10', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (8, '2022-11-16 19:34:37', '2023-05-03 16:12:30', 'SystemUpload/index', 7, '附件管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (9, '2022-11-16 19:35:31', '2023-05-03 16:12:42', 'SystemUpload/upload', 8, '上传附件', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (10, '2022-11-16 19:36:17', '2023-05-03 16:12:57', 'SystemUpload/del', 8, '删除附件', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (11, '2022-11-16 19:38:19', '2023-05-03 16:13:24', 'SystemUpload/table', 8, '附件列表', 0, '[\"GET\"]', '10', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (12, '2022-11-16 19:41:08', '2023-04-16 17:16:04', 'SystemUploadCate/index', 7, '附件分类', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (13, '2022-11-16 19:41:58', '2023-04-23 19:25:14', 'SystemUploadCate/add', 12, '添加附件分类', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (14, '2022-11-16 19:42:37', '2023-04-21 11:51:40', 'SystemUploadCate/edit', 12, '修改附件分类', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (15, '2022-11-16 19:43:35', '2023-04-21 11:51:48', 'SystemUploadCate/del', 12, '删除附件分类', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (16, '2022-11-16 19:45:01', '2023-04-21 11:51:56', 'SystemUploadCate/table', 12, '附件分类表格', 0, '[\"GET\"]', '10', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (17, '2022-11-16 23:04:14', '2023-04-16 17:16:04', 'Publics/loginout', 1, '退出登录', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__store_menus` VALUES (18, '2023-05-03 16:57:25', '2023-05-03 17:06:25', '', 0, '平台', 0, '[\"GET\"]', '10', 'none/index', '', 'ShopOutlined', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (19, '2023-05-03 17:05:10', '2023-10-02 16:30:37', 'Develop/create', 21, '创建开发者项目', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', 'UserOutlined', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (20, '2023-05-03 17:08:07', '2023-07-24 14:45:43', 'StoreApp/applet', 21, '小程序配置', 0, '[\"GET\",\"PUT\",\"POST\"]', '20', 'remote/index', 'remote/store/applet', 'SettingTwotone', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (21, '2023-05-03 17:11:21', '2023-07-23 15:19:34', 'StoreApp/index', 18, '项目管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (22, '2023-05-03 17:11:55', '2023-07-23 15:24:18', 'StoreApp/create', 21, '创建项目', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (23, '2023-05-03 17:13:06', '2023-10-10 16:53:28', 'Users/index', 0, '用户管理', 0, '[\"GET\"]', '20', 'table/index', '', 'UserOutlined', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (24, '2023-05-03 17:13:59', '2023-07-23 15:25:13', 'StoreApp/config', 21, '项目设置', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (25, '2023-05-03 17:38:41', '2023-05-03 17:38:41', 'Users/add', 23, '添加用户', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (26, '2023-05-03 17:39:14', '2023-07-23 15:25:36', 'Users/edit', 23, '修改用户', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (27, '2023-05-03 17:39:56', '2023-05-03 17:39:56', 'Users/del', 23, '删除用户', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (28, '2023-05-03 17:40:24', '2023-05-03 17:40:34', 'Users/indexGetTable', 23, '用户表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (29, '2023-05-03 17:42:57', '2023-05-03 17:42:57', 'UserFinance/index', 23, '财务管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (30, '2023-05-03 17:44:02', '2023-05-03 17:44:36', 'UserFinance/actionFinance', 29, '操作财务', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (31, '2023-05-03 19:53:40', '2023-07-23 16:11:28', 'StoreApp/edit', 21, '修改项目', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (32, '2023-06-16 11:44:08', '2023-07-23 15:27:31', 'StoreApp/del', 21, '删除项目', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (33, '2024-04-09 15:32:48', '2024-04-09 15:32:48', 'StoreAppRecycleBin/index', 0, '回收站', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (34, '2024-04-09 15:33:08', '2024-04-09 15:33:08', 'StoreAppRecycleBin/indexGetTable', 33, '表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (35, '2024-04-09 15:33:38', '2024-04-09 15:33:38', 'StoreAppRecycleBin/restore', 33, '恢复', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (36, '2024-04-09 15:33:56', '2024-04-09 15:33:56', 'StoreAppRecycleBin/delete', 33, '删除', 0, '[\"GET\",\"POST\"]', '10', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (37, '2024-04-09 17:45:04', '2024-04-09 17:45:04', 'StoreLog/index', 0, '操作日志', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (38, '2024-04-09 17:45:25', '2024-04-09 17:45:25', 'StoreLog/indexGetTable', 37, '表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__store_menus` VALUES (39, '2024-04-09 17:45:25', '2024-04-09 17:45:25', 'StoreLog/clear', 37, '清除', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
