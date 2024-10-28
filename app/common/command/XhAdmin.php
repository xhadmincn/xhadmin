<?php

declare(strict_types=1);

namespace app\common\command;

use EasyTask\Task;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use queue\Redis;
class XhAdmin extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('xhadmin')
            ->addArgument('action', Argument::OPTIONAL, "操作：start|stop|status")
            ->addOption('d','-d', Option::VALUE_NONE, '启动守护进程:-d')
            ->addOption('f','-f', Option::VALUE_NONE, '强制停止进程:-f')
            ->setDescription('the xhadmin command');
    }

    protected function execute(Input $input, Output $output)
    {
        $EasyTask = new Task;
        # 开启守护进程
        $EasyTask->setDaemon($input->hasOption('d'));
        $EasyTask->setPrefix('XhAdmin');
        $EasyTask->setRunTimePath(runtime_path());
        switch (trim($input->getArgument('action'))) {
            case 'start':
                $task = glob(root_path() . "plugin/*/config/task.php");
                $listNum = 0;
                foreach ($task as $path) {
                    # 插件名称
                    $plugin = basename(dirname(dirname($path)));
                    $packagePath = root_path("plugin/{$plugin}/package");
                    if (is_dir($packagePath) && file_exists($packagePath . "/vendor/autoload.php")) {
                        require_once $packagePath . "/vendor/autoload.php";
                    }
                    $list = include $path;
                    foreach ($list as $key => $value) {
                        $listNum++;
                        if (isset($value['type']) && $value['type'] == 'queue') {
                            $used = 1;
                            if (!empty($value['used'])) {
                                $used = abs((int)$value['used']);
                            }
                            $EasyTask->addFunc($this->createQueueListened($plugin, $key, $value), $plugin . '_' . $key, 1, $used);
                        } else {
                            $time = 1;
                            if (!empty($value['time'])) {
                                $time = abs((int)$value['time']);
                            }
                            $EasyTask->addClass($value['class'], $value['handler'], $plugin . '_' . $key, $time, 1);
                        }
                    }
                }
                $EasyTask->start();
                break;
            case 'stop':
                $EasyTask->stop($input->hasOption('force'));
                break;
            default:
                $EasyTask->status();
                break;
        }
    }
    protected function createQueueListened($plugin, $key, $value)
    {
        return function () use ($plugin, $key, $value) {
            try {
                $data=Redis::lpop(strtoupper($plugin . $key));
                if (empty($data)) {
                    return;
                }
                $data = json_decode($data, true);
                if (empty($data)) {
                    return;
                }
                $class = new $value['class'];
                $class->{$value['handler']}($data);
            } catch (\Throwable $th) {
                echo "队列异常：{$th->getMessage()}，file：{$th->getFile()}:{$th->getLine()}"  . PHP_EOL;
            }
        };
    }
}
