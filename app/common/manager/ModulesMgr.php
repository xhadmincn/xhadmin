<?php

namespace app\common\manager;
use Illuminate\Database\Schema\Blueprint;

/**
 * 表结构相关
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class ModulesMgr
{
    /**
     * 获取默认表结构
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function defaultColumns()
    {
        return [
            [
                'field'                 => 'id',
                'primary_key'           => true,
                'auto_increment'        => true,
                'length'                => 11,
                'type'                  => 'integer',
                'comment'               => '序号',
                'nullable'              => null,
                'default'               => null,
            ],
            [
                'field'                 => 'create_at',
                'primary_key'           => false,
                'auto_increment'        => false,
                'length'                => null,
                'type'                  => 'dateTime',
                'comment'               => '创建时间',
                'nullable'              => 1,
                'default'               => null,
            ],
            [
                'field'                 => 'update_at',
                'primary_key'           => false,
                'auto_increment'        => false,
                'length'                => null,
                'type'                  => 'dateTime',
                'comment'               => '更新时间',
                'nullable'              => 1,
                'default'               => null,
            ],
        ];
    }

    /**
     * 获取禁止删除的表名称
     * @return array<string>
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function dropTables()
    {
        return [
            'store',
            'store_app',
            'store_menus',
            'store_platform',
            'store_platform_config',
            'system_admin',
            'system_admin_log',
            'system_admin_role',
            'system_auth_rule',
            'system_config',
            'system_config_group',
            'system_upload',
            'system_upload_cate',
            'users',
            'users_integral_bill',
            'users_money_bill',
            'system_curd'
        ];
    }

    /**
     * 创建字段
     * @param mixed $column
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function createColumn($column, Blueprint $table)
    {
        $method = $column['type'];
        $args = [$column['field']];
        if (stripos($method, 'int') !== false) {
            // auto_increment 会自动成为主键
            if ($column['auto_increment']) {
                $column['nullable'] = false;
                $column['default'] = null;
                $args[] = true;
            }
        } elseif (in_array($method, ['string', 'char']) || stripos($method, 'time') !== false) {
            if ($column['length']) {
                $args[] = $column['length'];
            }
        } elseif ($method === 'enum') {
            $args[] = array_map('trim', explode(',', $column['length']));
        } elseif (in_array($method, ['float', 'decimal', 'double'])) {
            if ($column['length']) {
                $args = array_merge($args, array_map('trim', explode(',', $column['length'])));
            }
        } else {
            $column['auto_increment'] = false;
        }

        $column_def = call_user_func_array([$table, $method], $args);
        if (!empty($column['comment'])) {
            $column_def = $column_def->comment($column['comment']);
        }

        if (!$column['auto_increment'] && $column['primary_key']) {
            $column_def = $column_def->primary(true);
        }

        if ($column['auto_increment'] && !$column['primary_key']) {
            $column_def = $column_def->primary(false);
        }
        $column_def = $column_def->nullable($column['nullable']);

        if ($column['primary_key']) {
            $column_def = $column_def->nullable(false);
        }

        if ($method != 'text' && $column['default'] !== null) {
            $column_def->default($column['default']);
        }
        return $column_def;
    }


    /**
     * 表单类型到插件的映射
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function methodControlMap(): array
    {
        return  [
            //method=>[控件]
            'integer' => 'InputNumber',
            'string' => 'input',
            'text' => 'textarea',
            'date' => 'DatePicker',
            'enum' => 'Select',
            'float' => 'Input',

            'tinyInteger' => 'InputNumber',
            'smallInteger' => 'InputNumber',
            'mediumInteger' => 'InputNumber',
            'bigInteger' => 'InputNumber',

            'unsignedInteger' => 'InputNumber',
            'unsignedTinyInteger' => 'InputNumber',
            'unsignedSmallInteger' => 'InputNumber',
            'unsignedMediumInteger' => 'InputNumber',
            'unsignedBigInteger' => 'InputNumber',

            'decimal' => 'Input',
            'double' => 'Input',

            'mediumText' => 'TextArea',
            'longText' => 'TextArea',

            'dateTime' => 'DateTimePicker',

            'time' => 'DateTimePicker',
            'timestamp' => 'DateTimePicker',

            'char' => 'Input',

            'binary' => 'Input',

            'json' => 'input'
        ];
    }

    /**
     * 获取字段类型选项
     * @return array<array>
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getFieldTypeSelect()
    {
        $list = array_keys(self::methodControlMap());
        $data = [];
        foreach ($list as $key => $value) {
            $data[$key] = [
                'label'     => $value,
                'value'     => $value
            ];
        }
        return $data;
    }
}
