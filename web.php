<?php
use Workerman\Worker;
require_once 'E:\workerman\core\Autoloader.php';
 
// 创建一个Worker监听2345端口，使用http协议通讯
$ws_worker = new Worker("http://0.0.0.0:1111");
 
//启动4个进程对外提供服务 
$ws_worker->count = 4; 
