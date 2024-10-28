<?php
namespace app\common\manager;

use app\common\model\SystemCurd;
use Exception;

class CurdRule
{
    /**
     * 获取表格规则
     * @param string $app_name
     * @param string $tableName
     * @throws \Exception
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static function getTableRule(string $app_name,string $tableName)
    {
        # 表格字符串
        $str = "\n";
        # 类名
        $className = ucfirst($tableName);
        # 设置添加表单
        $where  = [
            ['table_name', '=', $tableName],
            ["form_add", '=', '20'],
        ];
        $formAdd = self::getRuleData($where);
        # 设置编辑表单
        $where  = [
            ['table_name', '=', $tableName],
            ["form_edit", '=', '20'],
        ];
        $formEdit = self::getRuleData($where);
        # 设置选项
        if ($formAdd || $formEdit) {
            $str .= <<<STR
            \t\t #设置操作选项
            \t\t\$builder->addActionOptions('操作选项');\n\n
            STR;
        }

        if ($formAdd) {
            $str .= <<<STR
            \t\t# 设置添加按钮
            \t\t\$builder->addTopButton('add', '添加', [
                \t\t'api'         => "app/{$app_name}/admin/{$className}/add",
                \t\t'path'        => '/{$className}/add',
            \t\t], [], [
                    \t'type' => 'success',
                    \t'icon' => 'PlusOutlined'
                \t]);
            STR;
        }
        if ($formEdit) {
            $str .= <<<STR
            \n
            \t\t# 设置编辑按钮
            \t\t\$builder->addRightButton('edit', '编辑', [
                \t\t'api'         => 'app/{$app_name}/admin/{$className}/edit',
                \t\t'path'        => '/{$className}/edit',
            \t\t], [
            \t\t], [
                \t\t'type' => 'success',
                \t\t'icon' => 'EditOutlined'
            \t\t]);
            STR;
        }
        # 删除按钮
        $where     = [
            ['table_name', '=', $tableName],
            ['field_name', '=', 'id'],
            ["is_del", '=', '20'],
        ];
        $delButton = self::getRuleData($where);
        if ($delButton) {
            $str .= <<<STR
            \n
            \t\t# 设置删除按钮
            \t\t\$builder->addRightButton('del', '删除', [
                \t\t'type' => 'confirm',
                \t\t'api' => 'app/{$app_name}/admin/{$className}/edit',
                \t\t'method' => 'delete',
            \t\t], [
                \t\t'title' => '温馨提示',
                \t\t'content' => '该数据删除将不可恢复，谨慎操作',
            \t\t], [
                \t\t'type' => 'error',
                \t\t'icon' => 'RestOutlined'
            \t\t]);
            STR;
        }
        # 设置表格单元
        $where  = [
            ['table_name', '=', $tableName],
            ['list_type', '=', '20'],
        ];
        $data = self::getRuleData($where);
        if (empty($data)) {
            throw new Exception('表格规则数据为空，无法生成');
        }
        $str .= "\n\n\t\t # 设置表格单元\n\t\t";
        foreach ($data as $value) {
            $str .= <<<STR
            \$builder->addColumn('{$value['field_name']}', '{$value['field_comment']}');\n\t\t
            STR;
        }
        return $str;
    }

    /**
     * 获取表单规则
     * @param string $tableName
     * @param string $formType
     * @return string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected static function getFormRule(string $tableName,string $formType)
    {
        $where  = [
            ['table_name', '=', $tableName],
            ["form_{$formType}", '=', '20'],
        ];
        $data = self::getRuleData($where);
        if (empty($data)) {
            return '';
        }
        $str = "\n\t\t# 设置表单列\n";
        foreach ($data as $value) {
            $str .= <<<STR
            \t\t\$builder->addRow('{$value['field_name']}', 'input', '{$value['field_comment']}', '', [
                \t\t'col'       => 12,
            \t\t]);\n
            STR;
        }
        return $str;
    }

    /**
     * 获取CURD列表数据
     * @param array $where
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function getRuleData(array $where)
    {
        $fields = [
            'field_name',
            'field_comment',
            'list_type',
            'form_add',
            'form_edit',
        ];
        $data   = SystemCurd::where($where)
            ->order('list_sort asc')
            ->field($fields)
            ->select()
            ->toArray();
        return $data;
    }
}