<?php
use \Workerman\Worker;
use \Workerman\Lib\Timer;
require_once './core/Autoloader.php';

$task = new Worker();
$task->onWorkerStart = function($task)
{
    // 每2秒执行一次
    $time_interval = 2;
    $connect_time = time();
    // 给connection对象临时添加一个timer_id属性保存定时器id
    $connection->timer_id = Timer::add($time_interval, function()use($connection, $connect_time)
    {
         $connection->send($connect_time);
    });
};

// 运行worker
Worker::runAll();