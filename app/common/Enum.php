<?php

namespace app\common;

use app\common\utils\EnumBaseic;
use Exception;

/**
 * 枚举基类
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-29
 */
abstract class Enum extends EnumBaseic
{
    /**
     * 获取枚举文本
     * @param string $dataValue
     * @param string $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getText(string $dataValue,string $default = '')
    {
        $data = self::toArray();
        foreach ($data as $value) {
            if ($value['value'] == $dataValue) {
                return $value['text'];
            }
        }
        return $default;
    }

    /**
     * 获取枚举值
     * @param string $dataValue
     * @param string $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getValue(string $dataValue,string $default = '')
    {
        $data = self::toArray();
        foreach ($data as $value) {
            if ($value['value'] == $dataValue) {
                return $value;
            }
        }
        return $default;
    }

    /**
     * 获取枚举值列
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public static function getEnumValues(): array
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[] = $value['value'];
        }
        return $list;
    }

    /**
     * 获取组装完成数据
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-29
     */
    public static function parseData(): array
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = $value;
        }
        return $list;
    }

    /**
     * 别名解析
     * @param mixed $field
     * @return array<array>
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public static function parseAlias(string $field,$other = true)
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = [
                $field      => $value['text'],
            ];
            if ($other) {
                $list[$value['value']]['value'] = $value['value'];
            }
        }
        return $list;
    }

    /**
     * 获取选择组件字典
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-03
     */
    public static function dictOptions()
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = $value['text'];
        }
        return $list;
    }

    /**
     * 获取字典内数据
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getDictValues(string $name,$default = [])
    {
        $data = self::toArray();
        foreach ($data as $value) {
            if ($value['value'] == $name) {
                return $value;
            }
        }
        return $default;
    }

    /**
     * 获取字段内列数据
     * @param string $field
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getColumn(string $field)
    {
        $data = self::toArray();
        if (empty($data)) {
            return [];
        }
        return array_column($data,$field);
    }

    /**
     * 获取选择组件数据
     * @param bool|null $disabled
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getOptions(bool|null $disabled = null): array
    {
        $data = self::toArray();
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $list[$i] = [
                'label'     => $value['text'],
                'value'     => $value['value'],
            ];
            if (isset($value['disabled'])) {
                $list[$i]['disabled'] = $value['disabled'];
            }
            if ($disabled !== null) {
                $list[$i]['disabled'] = $disabled;
            }
            $i++;
        }
        return $list;
    }
}
