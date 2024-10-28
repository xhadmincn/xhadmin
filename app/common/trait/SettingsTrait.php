<?php
namespace app\common\trait;

use app\common\trait\config\ComponentConfig;
use app\common\trait\config\Config;
use app\common\trait\config\DividerConfig;
use app\common\trait\config\TabsConfig;
use app\common\trait\config\UploadConfig;
use app\common\utils\Json;

/**
 * 系统设置管理
 * 对外暴露接口
 * 
 * 1. settings 普通配置项
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 * 
 * 
 * 2. config 无默认选中选项卡
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 * 
 * 
 * 3. divider 虚线配置项
 * 储存数据格式：{"xxxx":"xxx","xxxx":"xxx"}
 * 
 * 
 * 4. tabs 选项卡配置项
 * 储存数据格式：
 * {
 * "active":"xxx",
 * "children":{
 *      "xxxx":{
 *          "xxx":"xxxx",
 *          "xxxx":"xxx"
 *      },
 *    }
 * }
 * 
 * 
 * 5. uploadify 附件库配置项
 * 储存数据格式：
 * {
 * "active":"xxx",
 * "children":{
 *      "xxxx":{
 *          "xxx":"xxxx",
 *          "xxxx":"xxx"
 *      },
 *    }
 * }
 * 
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
trait SettingsTrait
{
    # 使用JSON工具类
    use Json;
    # 使用基础配置
    use Config;
    # 使用选项卡配置
    use TabsConfig;
    # 使用附件库配置
    use UploadConfig;
    # 使用虚线配置
    use DividerConfig;
    # 使用组件式配置
    use ComponentConfig;

    /**
     * 应用ID（null则获取系统配置）
     * @var string|null
     */
    protected $saas_appid = null;
}