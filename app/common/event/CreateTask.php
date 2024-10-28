<?php

declare(strict_types=1);

namespace app\common\event;

use think\facade\Log;
use Swoole\Timer;
use think\App;
use think\swoole\Manager;
use app\common\utils\SwooleUtil;

class CreateTask
{
    protected $manager;
    public function __construct(App $app)
    {
        $this->manager         = $app->make(Manager::class);
    }
    public function handle()
    {
        $this->manager->addWorker(function () {
            $this->manager->runWithBarrier(function () {
                $this->process();
            });
        }, 'xhadmin-task');
    }
    public function process()
    {
        try {
            $task = SwooleUtil::getTask();
            if (empty($task)) {
                return;
            }
            $task[] = [
                'plugin' => 'CreateTask',
                'class' => \app\common\process\CreateTask::class,
                'handler' => 'run',
                'time' => 1
            ];
            foreach ($task as $key => $value) {
                $time = 1;
                if (!empty($value['time'])) {
                    $time = abs((int)$value['time']);
                }
                Timer::tick($time * 1000, function () use ($value) {
                    try {
                        if (!empty($value['package'])) {
                            require_once $value['package'];
                        }
                        $class = new $value['class'];
                        $class->{$value['handler']}();
                    } catch (\Throwable $th) {
                        Log::channel('swoole')->error("{$value['plugin']} task error：{$th->getMessage()}，file：{$th->getFile()}:{$th->getLine()}");
                    }
                });
            }
            Log::channel('swoole')->info('create task start：' . date('Y-m-d H:i:s'));
        } catch (\Throwable $th) {
            Log::channel('swoole')->error("create task error：{$th->getMessage()}，file：{$th->getFile()}:{$th->getLine()}");
        }
    }
}
