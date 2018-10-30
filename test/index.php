<?php
use Workerman\Worker;
require_once '../core/Autoloader.php';
 header("Content-type: text/html; charset=utf-8");
// 创建一个Worker监听2345端口，使用http协议通讯
$ws_worker = new Worker("websocket://0.0.0.0:2345");
 
//启动4个进程对外提供服务 
$ws_worker->count = 4; 
 
$ws_worker->onWorkerStart = function($worker)
{
    echo "Worker starting...\n";
};
   
//当接收到客户端发来的数据后显示数据并回发到客户端 
$ws_worker->onMessage = function($connection, $data) { 
    global $ws_worker;
    //显示数据 
    echo "you just received: $data\n"; 
       
    //向客户端回发数据 
   if(!isset($connection->uid))
    {
       // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
       $connection->uid = $data;
       /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
        * 实现针对特定uid推送数据
        */
       $ws_worker->uidConnections[$connection->uid] = $connection;
       return $connection->send('login success, your uid is ' . $connection->uid);
    }
    // uid 为 all 时是全局广播
    list($recv_uid, $message) = explode(':', $data);
    // 全局广播
    if($recv_uid == 'all')
    {
        broadcast($message);
    }
    // 给特定uid发送
    else
    {
        sendMessageByUid($recv_uid, $message);
    }
};
$ws_worker->onClose = function($connection)
{
    global $ws_worker;
    echo "connection closed ".count($ws_worker->uidConnections);
};
// 向所有验证的用户推送数据
function broadcast($message)
{
   global $ws_worker;
   foreach($ws_worker->uidConnections as $connection)
   {
        echo '1/';
        $connection->send($message);
   }
}
 
// 针对uid推送数据
function sendMessageByUid($uid, $message)
{
    global $ws_worker;
    if(isset($ws_worker->uidConnections[$uid]))
    {
        $connection = $ws_worker->uidConnections[$uid];
        $connection->send($message);
    }
}
 
// 运行worker
Worker::runAll();
?>