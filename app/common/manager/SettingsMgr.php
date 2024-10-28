<?php
namespace app\common\manager;

use app\common\model\SystemConfig;

/**
 * 系统设置管理
 * @author 贵州猿创科技有限公司
 * @copyright (c) 2023
 */
class SettingsMgr
{
    /**
     * 获取配置项数据
     * @param int|null $appid
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function get(int|null $appid, string $name, mixed $default = null)
    {
        $where = [
            'saas_appid' => $appid
        ];
        $value = self::getOriginConfig($where, $default);
        if (empty($value)) {
            return $default;
        }
        $configValue = [];
        $names       = explode(',', $name);
        foreach ($names as $field) {
            if (isset($value[$field])) {
                $configValue[$field] = $value[$field];
            }
        }
        if (isset($configValue['children'])) {
            $configValue['children'] = self::getConfigValue($configValue['children']);
        }
        $data = self::getConfigValue($configValue);
        return $data;
    }

    /**
     * 获取配置项
     * @param int|null $appid 取系统null或应用ID
     * @param string $group 分组标识
     * @param string $name 多个配置以逗号分割！支持取层级：name.name
     * @param mixed $default 返回默认数据
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function config(int|null $appid, string $group, string $name, mixed $default = null)
    {
        $where = [
            'group'         => $group,
            'saas_appid'    => $appid,
        ];
        $data = self::getOriginConfig($where, $default);
        if (empty($data)) {
            return $default;
        }
        $names      = explode(',', $name);
        if (count($names) <= 1) {
            if (!isset($data[$name])) {
                return $default;
            }
            $data = [
                $name       => $data[$name]
            ];
            $configValue = self::getConfigValue($data);
            return $configValue[$name] ?? $default;
        }
        $configValue = [];
        foreach ($names as $field) {
            if (isset($data[$field])) {
                $configValue[$field] = $data[$field];
            }
        }
        $configValue = self::getConfigValue($configValue);
        return $configValue;
    }

    /**
     * 获取配置分组数据
     * @param int|null $appid
     * @param string $group
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function group(int|null $appid, string $group, mixed $default = null)
    {
        $where = [
            'group' => $group,
            'saas_appid' => $appid
        ];
        $value = self::getOriginConfig($where, $default);
        if (empty($value)) {
            return $default;
        }
        if (isset($value['children'])) {
            $value['children'] = self::getConfigValue($value['children']);
        }
        $value = self::getConfigValue($value);
        return $value;
    }

    /**
     * 获取子级配置项数据
     * @param int|null $appid
     * @param string $group
     * @param mixed $default
     * @return mixed
     * @author John
     */
    public static function getChildren(int|null $appid, string $group, mixed $default = null)
    {
        $data = self::group($appid, $group, $default);
        if (empty($data) || !isset($data['children'])) {
            return $default;
        }
        $data = $data['children'];
        return $data;
    }

    /**
     * 获取原始未解析配置项数据
     * @param array $where
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getOriginConfig(array $where, mixed $default = null)
    {
        $value = SystemConfig::where($where)->order('id desc')->value('value');
        if (empty($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * 获取配置项数据
     * @param array $data
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getConfigValue(array $data)
    {
        $configValue = [];
        foreach ($data as $field => $value) {
            if (strrpos($field, '.') !== false) {
                # 解析层级键值
                $dataField   = explode('.', $field);
                $resutil     = self::createNestedArray($dataField, $value);
                $configValue = array_merge_recursive($configValue, $resutil);
            } else {
                $configValue[$field] = $value;
            }
        }
        return $configValue;
    }

    /**
     * 组装为层级值
     * @param array $data
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function createNestedArray(array $data, mixed $config)
    {
        $data2   = [];
        $current = &$data2;
        foreach ($data as $field) {
            $current = &$current[$field];
        }
        $current = $config;
        return $data2;
    }
}