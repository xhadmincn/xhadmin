<?php

namespace app\admin\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\manager\ModulesMgr;
use app\admin\validate\Fields;
use app\common\BaseController;
use app\common\exception\RedirectException;
use app\common\manager\DbMgr;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use support\Request;
use think\facade\Db;

/**
 * @title 数据表字段管理
 * @desc 控制器描述
 * @author 楚羽幽 <admin@hangpu.net>
 */
class FieldsController extends BaseController
{
    private $tableName;
    private $prefixTableName;

    /**
     * 构造函数
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function __construct()
    {
        $request               = request();
        $prefix                = config('database.connections.mysql.prefix');
        $this->prefixTableName = $request->input('TABLE_NAME', '');
        $this->tableName       = str_replace($prefix, '', $this->prefixTableName);
        if (!DbMgr::hasTable($this->tableName)) {
            throw new Exception('该数据表不存在');
        }
        if (in_array($this->tableName, ModulesMgr::dropTables())) {
            throw new RedirectException('系统表，禁止操作字段', '/Modules/index');
        }
    }

    /**
     * 字段表格配置
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function indexGetTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->pageConfig()
            ->rowConfig([
                'keyField' => 'COLUMN_NAME'
            ])
            ->addTopButton('add', '新建字段', [
                'api' => 'admin/Fields/add',
                'path' => '/Fields/add',
                'queryParams' => [
                    'TABLE_NAME' => $this->tableName,
                ],
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('edit', '编辑', [
                'api' => 'admin/Fields/edit',
                'path' => '/Fields/edit',
                'queryParams' => [
                    'TABLE_NAME' => $this->tableName
                ]
            ], [], [
                'type' => 'primary',
                'link' => true
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => 'admin/Fields/del',
                'method' => 'delete',
            ], [
                'title' => '温馨提示',
                'content' => '是否确认删除该数据，该操作将不可恢复',
            ], [
                'type' => 'danger',
                'link' => true
            ])
            ->addColumn('COLUMN_NAME', '字段')
            ->addColumn('DATA_TYPE', '字段类型', [
                'width' => 130
            ])
            ->addColumn('COLUMN_COMMENT', '备注')
            ->addColumn('CHARACTER_MAXIMUM_LENGTH', '长度', [
                'width' => 130
            ])
            ->addColumn('COLUMN_DEFAULT', '默认值', [
                'width' => 180
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 字段列表数据
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function index(Request $request)
    {
        # 分页参数
        $page   = (int) $request->get('page', 1);
        $limit  = (int) $request->get('limit', 20);
        $offset = ($page - 1) * $limit;
        # 数据参数
        $database = config('database.connections.mysql.database');
        $table    = $this->prefixTableName;
        # 数据表字段
        $list = DbMgr::instance()->select("SELECT * FROM information_schema.COLUMNS where TABLE_SCHEMA = '$database' and table_name = '$table' order by ORDINAL_POSITION limit $offset,$limit");
        # 获取总数
        $total = DbMgr::instance()->select("SELECT count(*) as total from information_schema.COLUMNS WHERE table_schema='{$database}' and table_name='{$table}';")[0]->total ?? 0;
        # 返回数据
        $data = [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'data' => $list
        ];
        return $this->successRes($data);
    }

    /**
     * 新建字段
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() === 'POST') {
            # 获取数据
            $post = $request->post();
            # 数据验证
            hpValidate(Fields::class, $post);
            # 枚举必须输入额外数据
            if ($post['type'] === 'enum' && empty($post['extra'])) {
                return $this->fail('请输入扩展数据');
            }
            # 枚举类型值验证
            if ($post['type'] === 'enum' && stripos($post['extra'], ',') === false) {
                return $this->fail('枚举类型必须以小写逗号隔开');
            }
            # 枚举必须有默认值
            if ($post['type'] === 'enum' && empty($post['default'])) {
                return $this->fail('请输入默认值');
            }
            if (!in_array($post['default'], array_map('trim', explode(',', $post['extra'])))) {
                return $this->fail('枚举默认值错误');
            }
            # 数字及浮点验证
            if (in_array($post['type'], ['integer', 'string', 'char', 'float', 'decimal', 'double'])) {
                if (empty($post['length'])) {
                    return $this->fail('请输入数据长度');
                }
                $value = intval($post['length']);
                if ($value > 11 && $post['type'] === 'integer') {
                    return $this->fail('数字类型最大11位');
                }
                if ($value > 10 && in_array($post['type'], ['float', 'decimal', 'double'])) {
                    return $this->fail('浮点类型最大10位');
                }
                if ($value > 255 && in_array($post['type'], ['string', 'char'])) {
                    return $this->fail('字符串类型最大255位');
                }
            }
            # 验证字段是否存在
            if (DbMgr::schema()->hasColumn($this->tableName, $post['name'])) {
                return $this->fail('字段已存在');
            }
            # 创建字段
            DbMgr::schema()
                ->table($this->tableName, function (Blueprint $table) use ($post) {
                    $params = [
                        'comment' => $post['comment']
                    ];
                    # 判断是否有长度
                    if (in_array($post['type'], ['string', 'char']) || stripos($post['type'], 'time') !== false) {
                        $params['length'] = $post['length'];
                    }
                    # 检测是否枚举类型
                    if ($post['type'] === 'enum') {
                        $params['default'] = $post['default'];
                        $params['allowed'] = array_map('trim', explode(',', $post['extra']));
                    }
                    # 检测是否浮点类型
                    if (in_array($post['type'], ['float', 'decimal', 'double'])) {
                        $params['length'] = array_map('trim', explode(',', $post['length']));
                    }
                    # 设置字段基础参数
                    $table->addColumn($post['type'], $post['name'], $params);

                    # 设置引擎
                    $table->charset   = 'utf8mb4';
                    $table->collation = 'utf8mb4_general_ci';
                    $table->engine    = 'InnoDB';
                });
            return $this->success('创建成功');
        }
        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('POST')
            ->addRow('name', 'input', '字段名称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('comment', 'input', '字段注释', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('type', 'select', '字段类型', '', [
                'col' => [
                    'span' => 12
                ],
                'options' => ModulesMgr::getFieldTypeSelect()
            ])
            ->addRow('extra', 'textarea', '扩展数据', '', [
                'col' => [
                    'span' => 12
                ],
                'placeholder' => '可选填，扩展数据'
            ])
            ->addRow('length', 'input', '数据长度', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('default', 'input', '默认数据', '', [
                'col' => [
                    'span' => 12
                ],
                'placeholder' => '不输入默认值，据则默认为null'
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 修改字段
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public function edit(Request $request)
    {
        $column_name = $request->get('COLUMN_NAME', '');
        if (in_array($column_name, ['id', 'create_at', 'update_at'])) {
            throw new RedirectException('系统字段，禁止修改', "/Fields/index?TABLE_NAME={$this->prefixTableName}");
        }
        $sql       = "SELECT * FROM information_schema.COLUMNS WHERE table_name = ('{$this->prefixTableName}') and COLUMN_NAME = '{$column_name}' ORDER BY ordinal_position";
        $columnObj = DbMgr::instance()->select($sql);
        if (empty($columnObj)) {
            throw new RedirectException('获取字段数据失败', "/Fields/index?TABLE_NAME={$this->prefixTableName}");
        }
        isset($columnObj[0]) && $columnData = $columnObj[0];
        if (empty($columnData)) {
            throw new RedirectException('字段数据出错', "/Fields/index?TABLE_NAME={$this->prefixTableName}");
        }
        # 重设数据
        $data['name']    = $columnData->COLUMN_NAME ?? '';
        $data['comment'] = $columnData->COLUMN_COMMENT ?? '';
        $data['type']    = $columnData->DATA_TYPE ?? '';
        $data['length']  = $columnData->CHARACTER_MAXIMUM_LENGTH ?? '';
        $data['extra']   = '';
        $data['default'] = '';
        if ($data['type'] === 'enum') {
            $str             = ['enum(', ')', "'"];
            $default         = implode(',', explode(',', str_replace($str, '', $columnData->COLUMN_TYPE)));
            $data['extra']   = $default;
            $data['default'] = $columnData->COLUMN_DEFAULT;
        }
        if ($request->method() === 'PUT') {
            # 获取数据
            $post = $request->post();
            # 数据验证
            hpValidate(Fields::class, $post);

            # 获取字段数据
            $table     = DbMgr::filterAlphaNum($this->prefixTableName);
            $method    = DbMgr::filterAlphaNum($post['type']);
            $field     = DbMgr::filterAlphaNum($post['name']);
            $old_field = DbMgr::filterAlphaNum($column_name ?? null);
            $default   = $post['default'] ? DbMgr::pdoQuote($post['default']) : '';
            $comment   = DbMgr::pdoQuote($post['comment']);
            $length    = (int) $post['length'];

            # 枚举必须输入额外数据
            if ($method === 'enum' && empty($post['extra'])) {
                return $this->fail('请输入扩展数据');
            }
            # 枚举类型值验证
            if ($method === 'enum' && stripos($post['extra'], ',') === false) {
                return $this->fail('枚举类型必须以小写逗号隔开');
            }
            # 枚举必须有默认值
            if ($method === 'enum' && empty($post['default'])) {
                return $this->fail('请输入默认值');
            }
            if ($method === 'enum' && !in_array($post['default'], array_map('trim', explode(',', $post['extra'])))) {
                return $this->fail('枚举默认值错误');
            }
            # 数字及浮点验证
            if (in_array($method, ['integer', 'string', 'char', 'float', 'decimal', 'double'])) {
                if (empty($length)) {
                    return $this->fail('请输入数据长度');
                }
                if ($length > 11 && $method === 'integer') {
                    return $this->fail('数字类型最大11位');
                }
                if ($length > 10 && in_array($method, ['float', 'decimal', 'double'])) {
                    return $this->fail('浮点类型最大10位');
                }
                if ($length > 255 && in_array($method, ['string', 'char'])) {
                    return $this->fail('字符串类型最大255位');
                }
            }
            # 验证字段是否存在
            if (!DbMgr::schema()->hasColumn($this->tableName, $column_name)) {
                return $this->fail('该字段不存在');
            }
            # 字段名称
            if ($old_field && $old_field !== $field) {
                $sql = "ALTER TABLE `$table` CHANGE COLUMN `$old_field` `$field` ";
            } else {
                $sql = "ALTER TABLE `$table` MODIFY `$field` ";
            }
            # 整型类型
            if (stripos($post['type'], 'integer') !== false) {
                $type = str_ireplace('integer', 'int', $post['type']);
                if (stripos($post['type'], 'unsigned') !== false) {
                    $type = str_ireplace('unsigned', '', $type);
                    $sql .= "$type ";
                    $sql .= 'unsigned ';
                } else {
                    $sql .= "$type ";
                }
            } else {
                switch ($post['type']) {
                    case 'string':
                        $length = $post['length'] ?: 255;
                        $sql .= "varchar($length) ";
                        break;
                    case 'char':
                        $length = $post['length'] ?: 255;
                        $sql .= "varchar($length) ";
                    case 'time':
                        $sql .= $length ? "$method($length) " : "$method ";
                        break;
                    case 'enum':
                        $args = array_map('trim', explode(',', (string) $post['extra']));
                        foreach ($args as $key => $value) {
                            $args[$key] = DbMgr::pdoQuote($value);
                        }
                        $sql .= 'enum(' . implode(',', $args) . ') ';
                        break;
                    case 'double':
                    case 'float':
                    case 'decimal':
                        if (trim($post['length'])) {
                            $args    = array_map('intval', explode(',', $post['length']));
                            $args[1] = $args[1] ?? $args[0];
                            $sql .= "$method($args[0], $args[1]) ";
                            break;
                        }
                        $sql .= "$method ";
                        break;
                    default:
                        $sql .= "$method ";

                }
            }
            # 设置默认值只
            if ($method != 'text' && $default) {
                $sql .= "DEFAULT $default ";
            }
            # 设置注释
            if ($comment !== null) {
                $sql .= "COMMENT $comment ";
            }
            # 执行SQL
            if (!Db::execute($sql)) {
                return $this->fail('修改失败');
            }
            # 返回消息
            return $this->success('修改成功');
        }

        $builder = new FormBuilder;
        $data    = $builder
            ->setMethod('PUT')
            ->addRow('name', 'input', '字段名称', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('comment', 'input', '字段注释', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('type', 'select', '字段类型', '', [
                'col' => [
                    'span' => 12
                ],
                'options' => ModulesLogic::getFieldTypeSelect()
            ])
            ->addRow('extra', 'textarea', '扩展数据', '', [
                'col' => [
                    'span' => 12
                ],
                'placeholder' => '可选填，扩展数据'
            ])
            ->addRow('length', 'input', '数据长度', '', [
                'col' => [
                    'span' => 12
                ],
            ])
            ->addRow('default', 'input', '默认数据', '', [
                'col' => [
                    'span' => 12
                ],
                'placeholder' => '不输入默认值，据则默认为null'
            ])
            ->setFormData($data)
            ->create();
        return $this->successRes($data);
    }

    /**
     * 删除字段
     * @param \support\Request $request
     * @return \support\Response
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function del(Request $request)
    {
        $column_name = $request->post('COLUMN_NAME', '');
        if (in_array($column_name, ['id', 'create_at', 'update_at'])) {
            return $this->fail('系统字段，禁止删除');
        }
        # 删除字段
        DbMgr::schema()->dropColumns($this->tableName, $column_name);

        # 操作返回
        return $this->success('删除成功');
    }
}