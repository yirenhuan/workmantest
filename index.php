<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once __DIR__ . '/core/Autoloader.php';

// 注意：这里与上个例子不同，使用的是websocket协议
$ws_worker = new Worker("websocket://0.0.0.0:2000");

// 启动4个进程对外提供服务
$ws_worker->count = 4;

// 当收到客户端发来的数据后返回hello $data给客户端

$ws_worker->onConnect = function($connection)
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