DROP TABLE IF EXISTS `__PREFIX__system_auth_rule`;

CREATE TABLE `__PREFIX__system_auth_rule`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '请求地址：控制器/操作方法',
  `pid` int NULL DEFAULT 0 COMMENT '父级菜单地址',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `sort` int NULL DEFAULT 0 COMMENT '菜单排序，值越大越靠后',
  `method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求类型：GET,POST,PUT,DELETE',
  `is_api` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否接口：10否 20是',
  `component` enum('none/index','form/index','table/index','remote/index','vue/index','html/index') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none/index' COMMENT '组件类型',
  `auth_params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '附带参数：remote/index，填写远程组件路径名称',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图标类名',
  `show` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否显示：10隐藏 20显示（仅针对1-2级菜单）',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否 20是',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '默认权限：10否 20是',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `path`(`path` ASC) USING BTREE COMMENT '唯一索引'
) ENGINE = InnoDB AUTO_INCREMENT = 201 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-权限规则' ROW_FORMAT = DYNAMIC;

INSERT INTO `__PREFIX__system_auth_rule` VALUES (1, '2022-10-27 17:22:51', '2023-03-07 21:36:28', 'SystemIndex/group', 0, '首页', 0, '[\"GET\"]', '10', 'none/index', '', 'HomeOutlined', '20', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (2, '2022-10-27 17:22:51', '2023-05-10 14:47:24', 'Index/index', 1, '控制台', 0, '[\"GET\"]', '10', 'remote/index', '/remote/welcome', 'FolderOpenOutlined', '20', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (3, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/login', 1, '系统登录', 0, '[\"POST\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (4, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/site', 1, '获取应用信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (5, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/user', 1, '获取管理员信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (6, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Publics/menus', 1, '获取菜单信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (7, '2022-10-27 17:22:51', '2023-03-07 21:36:32', 'SystemSettings/group', 0, '系统', 0, '[\"GET\"]', '10', 'none/index', '', 'HomeOutlined', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (8, '2022-10-27 17:22:51', '2023-08-03 23:18:05', 'Webconfig/tabs', 7, '系统模块', 0, '[\"GET\"]', '10', 'none/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (9, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'Auth/tabs', 7, '权限管理', 0, '[\"GET\"]', '10', 'none/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (10, '2022-10-27 17:22:51', '2023-10-17 17:19:15', 'SystemConfig/settings', 8, '系统设置', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', 'group=system&type=1', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (11, '2022-10-27 17:22:51', '2023-08-03 23:18:44', 'SystemConfigGroup/index', 8, '配置分组', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (12, '2022-10-27 17:22:51', '2024-04-09 17:41:43', 'SystemAuthRule/index', 9, '菜单管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (13, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'SystemAdminRole/index', 9, '部门管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (14, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'SystemAdmin/index', 9, '账户管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (17, '2022-10-27 17:22:51', '2023-10-27 14:03:06', 'Modules/tabs', 7, '功能模块', 0, '[\"GET\"]', '10', 'none/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (20, '2022-10-27 17:22:51', '2023-10-27 17:02:58', 'SystemData/index', 17, '数据管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (21, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'SystemConfigGroup/add', 11, '添加配置分组', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (22, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'SystemConfigGroup/edit', 11, '修改配置分组', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (23, '2022-10-27 17:22:51', '2023-04-16 17:16:04', 'SystemConfigGroup/del', 11, '删除配置分组', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (24, '2022-10-27 17:22:51', '2023-07-21 12:27:49', 'SystemConfig/index', 10, '配置项列表', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (28, '2022-10-27 17:22:51', '2023-04-21 11:53:26', 'SystemAuthRule/add', 12, '添加权限菜单', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (29, '2022-10-27 17:22:51', '2023-04-21 11:53:35', 'SystemAuthRule/edit', 12, '修改权限菜单', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (30, '2022-10-27 17:22:51', '2023-04-21 11:53:55', 'SystemAuthRule/del', 12, '删除权限菜单', 0, '[\"GET\",\"DELETE\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (32, '2022-11-15 02:42:33', '2023-04-21 11:58:32', 'SystemAdminRole/add', 13, '添加部门', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (33, '2022-11-15 02:44:31', '2023-04-21 11:59:16', 'SystemAdminRole/edit', 13, '修改部门', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (34, '2022-11-15 02:46:04', '2023-04-21 11:59:24', 'SystemAdminRole/del', 13, '删除部门', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (35, '2022-11-15 02:48:09', '2023-04-21 11:59:54', 'SystemAdmin/add', 14, '添加账户', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (36, '2022-11-15 02:49:00', '2023-04-21 12:00:01', 'SystemAdmin/edit', 14, '修改账户', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (37, '2022-11-15 02:49:46', '2023-04-21 12:00:09', 'SystemAdmin/del', 14, '删除账户', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (38, '2022-11-15 09:23:53', '2023-04-21 11:59:32', 'SystemAdminRole/auth', 13, '设置权限', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (40, '2022-11-16 15:36:42', '2023-04-16 17:16:04', 'SystemConfigGroup/indexGetTable', 11, '配置分组列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (41, '2022-11-16 19:33:49', '2023-09-28 14:22:47', 'System/updateTabs', 0, '服务', 100, '[\"GET\"]', '10', 'none/index', '', 'UploadFilled', '20', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (42, '2022-11-16 19:34:37', '2023-08-03 23:19:40', 'SystemUpload/index', 182, '附件管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (43, '2022-11-16 19:35:31', '2023-04-21 11:51:04', 'SystemUpload/upload', 42, '上传附件', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (44, '2022-11-16 19:36:17', '2023-04-21 11:51:13', 'SystemUpload/del', 42, '删除附件', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (45, '2022-11-16 19:38:19', '2023-04-21 11:51:23', 'SystemUpload/table', 42, '附件列表', 0, '[\"GET\"]', '10', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (46, '2022-11-16 19:41:08', '2023-08-03 23:20:00', 'SystemUploadCate/index', 182, '附件分类', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (47, '2022-11-16 19:41:58', '2023-04-23 19:25:14', 'SystemUploadCate/add', 46, '添加附件分类', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (48, '2022-11-16 19:42:37', '2023-04-21 11:51:40', 'SystemUploadCate/edit', 46, '修改附件分类', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (49, '2022-11-16 19:43:35', '2023-04-21 11:51:48', 'SystemUploadCate/del', 46, '删除附件分类', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (50, '2022-11-16 19:45:01', '2023-04-21 11:51:56', 'SystemUploadCate/table', 46, '附件分类表格', 0, '[\"GET\"]', '10', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (51, '2022-11-16 23:04:14', '2023-04-16 17:16:04', 'Publics/loginout', 1, '退出登录', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (64, '2022-11-18 16:47:26', '2023-10-27 18:02:46', 'SystemData/optimize', 20, '优化数据表', 0, '[\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (65, '2022-11-18 16:48:09', '2023-10-27 18:03:10', 'SystemData/fix', 20, '修复数据表', 0, '[\"GET\",\"PUT\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (66, '2022-11-18 16:48:42', '2023-11-20 11:22:49', 'SystemData/fixMenus', 20, '修复菜单数据', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (67, '2022-11-19 04:13:32', '2023-04-16 17:16:04', 'Fields/index', 20, '字段管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (68, '2022-11-19 04:14:14', '2023-04-16 17:16:04', 'Fields/add', 67, '添加字段', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (69, '2022-11-19 04:14:52', '2023-04-16 17:16:04', 'Fields/edit', 67, '修改字段', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (70, '2022-11-19 04:15:52', '2023-04-16 17:16:04', 'Fields/del', 67, '删除字段', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (71, '2023-03-06 19:33:21', '2023-04-16 17:16:04', 'SystemConfig/add', 24, '添加配置项', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (72, '2023-03-06 19:33:21', '2023-04-16 17:16:04', 'SystemConfig/edit', 24, '修改配置项', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (73, '2023-03-06 19:33:21', '2023-04-16 17:16:04', 'SystemConfig/del', 24, '删除配置项', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (76, '2023-03-10 19:37:45', '2023-04-16 17:16:04', 'SystemAuthRule/indexGetTable', 12, '菜单列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (77, '2023-03-11 16:46:33', '2023-04-21 11:59:40', 'SystemAdminRole/indexGetTable', 13, '部门列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (78, '2023-03-11 16:52:34', '2023-04-21 12:00:25', 'SystemAdmin/indexGetTable', 14, '部门列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (79, '2023-03-12 13:56:06', '2023-04-21 11:52:13', 'SystemConfig/indexGetTable', 24, '配置项表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (81, '2023-03-12 17:23:11', '2023-11-20 11:29:35', 'SystemData/backup', 20, '备份数据库', 0, '[\"GET\",\"PUT\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (82, '2023-03-12 17:24:36', '2023-04-16 17:16:04', 'Fields/indexGetTable', 67, '字段列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (83, '2023-03-12 20:02:14', '2023-04-16 17:16:04', 'Index/clear', 1, '清除缓存', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (84, '2023-03-12 20:11:14', '2023-04-16 17:16:04', 'Index/lock', 1, '解除锁定', 0, '[\"POST\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (137, '2023-04-30 18:48:45', '2023-04-30 19:06:45', 'pluginGroup/group', 0, '应用', 0, '[\"GET\"]', '10', 'none/index', '', 'AppstoreOutlined', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (138, '2023-04-30 18:54:39', '2023-04-30 18:54:39', 'pluginGroup/tabs', 137, '应用插件', 0, '[\"GET\"]', '10', 'none/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (139, '2023-04-30 19:05:31', '2023-04-30 19:05:31', 'Plugin/index', 138, '应用中心', 0, '[\"GET\"]', '20', 'remote/index', 'remote/cloud/plugins', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (140, '2023-04-30 19:23:50', '2023-04-30 19:33:18', 'Plugin/buy', 139, '购买应用', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (141, '2023-04-30 19:24:28', '2023-04-30 19:33:06', 'Plugin/install', 139, '安装应用', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (142, '2023-04-30 19:32:16', '2023-04-30 19:32:56', 'Plugin/update', 139, '更新应用', 0, '[\"GET\",\"PUT\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (143, '2023-04-30 19:32:44', '2023-04-30 19:32:44', 'Plugin/uninstall', 139, '卸载应用', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (144, '2023-04-30 19:34:48', '2023-04-30 19:52:20', 'storeGroup/group', 0, '站点', 0, '[\"GET\"]', '10', 'none/index', '', 'InboxOutlined', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (145, '2023-04-30 21:11:43', '2023-04-30 21:13:12', 'Plugin/indexGetTable', 139, '表格列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (147, '2023-04-30 21:53:43', '2024-04-08 16:47:08', 'Store/index', 144, '站点管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (148, '2023-04-30 21:54:09', '2023-04-30 22:18:26', 'Store/add', 147, '创建站点', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (149, '2023-04-30 21:54:40', '2023-04-30 21:55:55', 'Store/edit', 147, '修改站点', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (150, '2023-04-30 21:55:22', '2023-04-30 21:55:22', 'Store/del', 147, '删除站点', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (151, '2023-04-30 21:57:39', '2023-04-30 21:57:39', 'Store/indexGetTable', 147, '站点表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (157, '2023-05-01 02:23:35', '2024-04-08 16:47:28', 'StoreProject/index', 144, '项目管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (158, '2023-05-01 02:39:51', '2023-05-01 02:39:51', 'StoreProject/add', 157, '创建平台', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (159, '2023-05-01 02:56:21', '2023-05-01 02:56:21', 'StoreProject/edit', 157, '修改平台', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (160, '2023-05-01 03:03:01', '2023-05-01 03:03:01', 'StoreProject/del', 157, '删除平台', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (161, '2023-05-01 03:04:09', '2023-05-01 03:04:09', 'StoreProject/indexGetTable', 157, '平台列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (162, '2023-05-03 12:18:46', '2023-05-03 12:18:46', 'Store/login', 147, '管理站点表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (163, '2023-05-03 14:45:48', '2024-04-09 15:28:53', 'StoreMenus/index', 144, '站点菜单', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (164, '2023-05-03 14:46:25', '2023-05-03 14:48:02', 'StoreMenus/add', 163, '添加站点菜单', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (165, '2023-05-03 14:46:53', '2023-05-03 16:11:18', 'StoreMenus/edit', 163, '修改站点菜单', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (166, '2023-05-03 14:47:20', '2023-05-03 14:47:20', 'StoreMenus/del', 163, '删除站点菜单', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (167, '2023-05-03 14:47:48', '2023-05-03 14:47:48', 'StoreMenus/indexGetTable', 163, '站点菜单表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (174, '2023-05-05 18:19:23', '2023-05-05 18:19:23', 'PluginCloud/index', 138, '云服务服务信息', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (175, '2023-05-06 14:53:49', '2023-05-06 15:02:38', 'PluginCloud/login', 174, '云服务登录', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (176, '2023-05-06 14:54:35', '2023-05-06 14:54:35', 'PluginCloud/captcha', 174, '云服务验证码', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (177, '2023-05-06 18:18:39', '2023-05-06 18:18:39', 'Plugin/detail', 139, '插件详情', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (178, '2023-05-06 18:20:14', '2023-05-12 16:26:59', 'Index/consoleCount', 2, '控制台数据统计', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (179, '2023-05-06 20:21:59', '2023-10-25 12:25:01', 'Plugin/getPluginData', 139, '应用物料数据', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (180, '2023-05-09 16:14:10', '2023-05-09 16:14:10', 'StoreApp/index', 147, '授权应用', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (181, '2023-05-15 13:04:59', '2023-09-28 14:23:15', 'Updated/updateCheck', 41, '版本更新', 0, '[\"GET\",\"POST\",\"PUT\"]', '20', 'remote/index', 'remote/update/index', '', '20', '20', '20');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (182, '2023-06-12 16:07:52', '2023-08-03 23:21:13', 'SystemConfig/uploadify', 8, '附件设置', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', 'group=upload&type=1', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (183, '2023-06-17 11:33:56', '2023-06-17 11:33:56', 'Store/copyrightSet', 147, '站点版权设置', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (184, '2023-06-17 12:12:00', '2023-06-17 12:12:23', 'StorePlatform/restore', 157, '恢复删除平台', 0, '[\"GET\",\"DELETE\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (185, '2023-07-01 14:03:18', '2023-07-01 14:03:18', 'Curd/index', 20, 'CURD管理', 0, '[\"GET\"]', '20', 'table/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (186, '2023-07-01 14:04:12', '2023-07-01 14:04:12', 'Curd/add', 185, '新建CURD', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (187, '2023-07-01 14:04:52', '2023-07-01 14:05:53', 'Curd/edit', 185, '修改CURD', 0, '[\"GET\",\"PUT\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (188, '2023-07-01 14:05:29', '2023-07-03 10:14:49', 'Curd/detail', 185, 'CURD详情', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (189, '2023-07-01 14:08:42', '2023-07-01 14:09:16', 'Curd/indexGetTable', 185, 'CURD数据列表', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (190, '2023-09-28 14:26:34', '2023-09-28 17:40:17', 'Updated/empower', 41, '授权信息', 0, '[\"GET\",\"POST\",\"PUT\"]', '20', 'remote/index', 'remote/update/empower', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (191, '2023-09-28 14:28:15', '2023-09-28 14:28:15', 'Updated/index', 41, '更新日志', 0, '[\"GET\"]', '20', 'remote/index', 'remote/update/log', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (192, '2023-10-02 16:43:43', '2023-10-02 16:43:43', 'SystemAuthRule/addResource', 12, '添加资源菜单', 0, '[\"GET\",\"POST\"]', '20', 'form/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (193, '2023-10-25 12:37:41', '2023-10-25 12:37:41', 'PluginCloud/bill', 174, '云服务账单', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (194, '2024-04-08 17:18:03', '2024-04-08 17:18:03', 'StoreRecycleBin/index', 144, '回收站', 0, '[\"GET\"]', '10', 'table/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (195, '2024-04-08 17:18:25', '2024-04-08 17:18:25', 'StoreRecycleBin/indexGetTable', 194, '表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (196, '2024-04-08 17:18:49', '2024-04-08 17:18:49', 'StoreRecycleBin/delete', 194, '删除', 0, '[\"GET\",\"DELETE\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (197, '2024-04-08 17:19:25', '2024-04-08 17:19:25', 'StoreRecycleBin/restore', 194, '恢复', 0, '[\"GET\",\"POST\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (198, '2024-04-09 16:36:29', '2024-04-09 16:41:03', 'SystemAdminLog/index', 9, '操作日志', 0, '[\"GET\"]', '10', 'table/index', '', '', '20', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (199, '2024-04-09 16:36:55', '2024-04-09 16:36:55', 'SystemAdminLog/indexGetTable', 198, '表格', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
INSERT INTO `__PREFIX__system_auth_rule` VALUES (200, '2024-04-09 16:36:55', '2024-04-09 16:36:55', 'SystemAdminLog/clear', 198, '清除', 0, '[\"GET\"]', '20', 'none/index', '', '', '10', '10', '10');
