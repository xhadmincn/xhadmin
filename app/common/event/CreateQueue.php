<?php

declare(strict_types=1);

namespace app\common\event;

use think\facade\Log;
use Swoole\Timer;
use think\App;
use think\swoole\Manager;
use app\common\utils\SwooleUtil;
use queue\Redis;

class CreateQueue
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
        }, 'xhadmin-queue');
    }
    public function process()
    {
        try {
            $queue = SwooleUtil::getQueue();
            if (empty($queue)) {
                return;
            }
            foreach ($queue as $key => $value) {
                $used = 1;
                if (!empty($value['used'])) {
                    $used = abs((int)$value['used']);
                }
                for ($i = 0; $i < $used; $i++) {
                    Timer::tick(1000, function () use ($value) {
                        try {
                            if (!empty($value['package'])) {
                                require_once $value['package'];
                            }
                            $data = Redis::lpop(strtoupper($value['queue_name']));
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
                            Log::channel('swoole')->error("{$value['queue_name']} queue error：{$th->getMessage()}，file：{$th->getFile()}:{$th->getLine()}");
                        }
                    });
                }
            }
            Log::channel('swoole')->info('create queue start：' . date('Y-m-d H:i:s'));
        } catch (\Throwable $th) {
            Log::channel('swoole')->error("create queue error：{$th->getMessage()}，file：{$th->getFile()}:{$th->getLine()}");
        }
    }
}
