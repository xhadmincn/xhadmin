<?php

namespace app\admin\controller;

use app\admin\utils\SystemFixUtil;
use app\common\builder\ListBuilder;
use app\common\BaseController;
use app\common\manager\DbMgr;
use support\Request;
use think\facade\Db;

/**
 * 系统数据库管理
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class SystemDataController extends BaseController
{
    /**
     * 数据表列表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 160
            ])
            ->pageConfig()
            ->rowConfig([
                'keyField'      => 'TABLE_NAME'
            ])
            ->addTopButton('backup', '备份数据库', [
                'api'       => 'admin/SystemData/backup',
                'path'      => '/SystemData/backup',
                'type'      => 'confirm',
            ], [
                'type'      => 'success',
                'title'     => '温馨提示',
                'content'   => '是否确认备份数据库？',
            ], [
                'type'      => 'primary',
            ])
            ->addTopButton('fixMenus', '修复菜单数据', [
                'api'       => 'admin/SystemData/fixMenus',
                'path'      => '/SystemData/fixMenus',
                'type'      => 'confirm',
            ], [
                'type'      => 'success',
                'title'     => '温馨提示',
                'content'   => '是否确认修复菜单数据？',
            ], [
                'type' => 'success',
            ])
            ->addRightButton('optimize', '优化', [
                'type'      => 'confirm',
                'api'       => 'admin/SystemData/optimize',
                'method'    => 'delete',
                'params'    => [
                    'field' => 'OPTIMIZE',
                    'value' => 'yes',
                ],
            ], [
                'type'      => 'success',
                'title'     => '温馨提示',
                'content'   => '是否确认开始优化该表数据？',
            ], [
                'type' => 'primary',
            ])
            ->addRightButton('field_fix', '修复', [
                'type'      => 'confirm',
                'api'       => 'admin/SystemData/fix',
                'method'    => 'delete',
                'params'    => [
                    'field' => 'FIELD_FIX',
                    'value' => 'yes',
                ],
            ], [
                'type'      => 'warning',
                'title'     => '温馨提示',
                'content'   => '是否确认开始修复该表字段结构？',
            ], [
                'type' => 'warning',
            ])
            ->addColumn('CREATE_TIME', '创建时间', [
                'width' => 160
            ])
            ->addColumn('TABLE_NAME', '表名')
            ->addColumn('TABLE_ROWS', '记录数', [
                'width' => 100
            ])
            ->addColumn('ENGINE', '引擎', [
                'width' => 130
            ])
            ->addColumn('TABLE_COLLATION', '字符集', [
                'width' => 180
            ])
            ->addColumn('TABLE_COMMENT', '备注')
            ->addColumn('FIX_REMARKS', '修复理由')
            ->create();
        return $this->successRes($data);
    }

    /**
     * 获取表
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function index(Request $request)
    {
        # 分页
        $page   = (int) $request->get('page', 1);
        $limit  = (int) $request->get('limit', 20);
        $offset = ($page - 1) * $limit;
        # 字段名
        $field = $request->get('field', 'CREATE_TIME');
        $field = DbMgr::filterAlphaNum($field);
        # 排序
        $order = $request->get('order', 'desc');
        # 库名称
        $database = config('database.connections.mysql.database', '');
        # 表前缀
        $prefix = config('database.connections.mysql.prefix', '');
        # 获取总数
        $sql   = "SELECT count(*)total FROM  information_schema.`TABLES` WHERE TABLE_SCHEMA='{$database}' and TABLE_NAME LIKE '%{$prefix}system_%' or TABLE_NAME LIKE '%{$prefix}plugin_%'";
        $total = Db::query($sql)[0]['total'] ?? 0;
        # 获取表信息列表
        $sql    = "SELECT TABLE_NAME,TABLE_COMMENT,ENGINE,TABLE_ROWS,CREATE_TIME,UPDATE_TIME,TABLE_COLLATION FROM  information_schema.`TABLES` WHERE TABLE_SCHEMA='{$database}' and TABLE_NAME LIKE '{$prefix}system_%' or TABLE_NAME LIKE '{$prefix}plugin_%' or TABLE_NAME LIKE '{$prefix}store%' order by {$field} {$order} limit {$offset},{$limit}";
        $tables = Db::query($sql);
        # 重构表部分数据
        if ($tables) {
            # 获取系统表结构
            $tablesFields  = SystemFixUtil::getTableFields();
            # 使用系统表名称作为键分组
            $tableGroup = array_column($tablesFields, null,'name');
            foreach ($tables as $key=>$value) {
                # 获取无表前缀的表名
                $nonePrefixTableName    = str_replace($prefix, '', $value['TABLE_NAME']);
                # 是否可修复表
                $tables[$key]['FIELD_FIX'] = 'no';
                # 是否可优化表
                $tables[$key]['OPTIMIZE'] = 'no';

                # 查询真实记录数
                $rowTotal = Db::name($nonePrefixTableName)->count();
                $tables[$key]['TABLE_ROWS'] = $rowTotal;

                # 获取系统表字段
                $systemFields = $tableGroup[$nonePrefixTableName]['fields'] ?? [];
                # 查询表所有字段
                $fields = Db::query("SHOW FULL COLUMNS FROM {$value['TABLE_NAME']}");
                $fieldList = array_column($fields, null,'Field');
                # 系统表对比现有表
                foreach ($systemFields as $fieldName=>$fieldType) {
                    $item = $fieldList[$fieldName] ?? null;
                    # 字段不存在
                    if (empty($item)) {
                        $tables[$key]['FIELD_FIX'] = 'yes';
                        $tables[$key]['FIX_REMARKS'] = "字段【{$fieldName}】不存在";
                        continue;
                    }
                    # 截取字段类型长度
                    preg_match("/(.*?)\(/", $item['Type'], $matches);
                    $type = $matches[1] ?? $item['Type'];
                    # 是否可修复表
                    if ($type !== $fieldType) {
                        $tables[$key]['FIX_REMARKS'] = "字段【{$fieldName}】类型匹配失败";
                        $tables[$key]['FIELD_FIX'] = 'yes';
                    }
                }
            }
            # 对数据重新排序
            $tables = list_sort_by($tables,'TABLE_NAME');
        }
        # 返回数据
        $data = [
            'current_page'  => $page,
            'per_page'      => $limit,
            'total'         => $total,
            'data'          => $tables
        ];
        return $this->successRes($data);
    }

    /**
     * 备份数据库
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function backup(Request $request)
    {
        SystemFixUtil::backup();
        return $this->success('数据库备份成功');
    }

    /**
     * 修复菜单
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function fixMenus(Request $request)
    {
        SystemFixUtil::fixMenus();
        return $this->success('菜单修复成功');
    }

    /**
     * 修复表结构
     * @param \support\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function fix(Request $request)
    {
        # 完整表名称
        $tableName = $request->post('TABLE_NAME', '');
        if (!$tableName) {
            return $this->fail('表名称参数错误');
        }
        # 表前缀
        $prefix = config('database.connections.mysql.prefix', '');
        # 获取无表前缀的表名
        $nonePrefixTableName    = str_replace($prefix, '', $tableName);
        # 获取系统表结构
        $allTable = SystemFixUtil::getTableFields();
        # 使用系统表名称作为键分组
        $tableGroup = array_column($allTable, null,'name');
        # 当前表字段
        $tableFields = $tableGroup[$nonePrefixTableName]['fields'] ?? [];
        # 查询表所有字段
        $fields = Db::query("SHOW FULL COLUMNS FROM {$tableName}");
        $fieldList = array_column($fields, null,'Field');
        # 所需要修复的字段名
        $fieldData = [];
        foreach ($tableFields as $fieldName=>$fieldType) {
            $item = $fieldList[$fieldName] ?? null;
            # 字段不存在
            if (empty($item)) {
                $fieldData[] = $fieldName;
                continue;
            }
            # 截取字段类型长度
            preg_match("/(.*?)\(/", $item['Type'], $matches);
            $type = $matches[1] ?? $item['Type'];
            # 是否可修复表
            if ($type !== $fieldType) {
                $fieldData[] = $fieldName;
            }
        }
        # 获取所需要修复的SQL语句
        $data = SystemFixUtil::getSql($tableName, $fieldData);
        if (empty($data)) {
            return $this->fail('表结构无需修复');
        }
        foreach ($data as $strSql) {
            # 拆分SQL语句
            $array = explode("\n", $strSql);
            foreach ($array as $sql) {
                # 字段操作
                Db::query($sql);
            }
        }
        return $this->success('表结构修复成功');
    }
}