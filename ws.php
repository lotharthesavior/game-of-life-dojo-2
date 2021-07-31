<?php

require 'vendor/autoload.php';

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use App\MessageHandler;

$users = new Swoole\Table(1024);
$users->column('name', Swoole\Table::TYPE_STRING, 64);
$users->create();

$server = new Server("0.0.0.0", 8181);

$server->on("Start", function(Server $server) {
    $pid_file = __DIR__ . '/ws-server-pid';
    if (file_exists($pid_file)) {
        unlink($pid_file);
    }   
    file_put_contents($pid_file, $server->master_pid);
    echo "Swoole WebSocket Server is started at http://127.0.0.1:8181\n";
});

function report_missing_name($server, $fd) {
    $server->disconnect($fd, 400, 'Please, try again informing your name!');
}

$server->on('Open', function(Server $server, Request $request) use ($users) {
    echo "connection open: {$request->fd}\n";

    // $users->set($request->fd, ['name' => $parsedQuery['name']]);
});

$server->on('Message', function(Server $server, Frame $frame) use ($users) {
    // echo "received message: {$frame->data}\n";

    // foreach($server->connection_list(0) as $fd) {
    //     $server->push($fd, json_encode([
    //         'name' => $users->get($frame->fd, 'name'), 
    //         'message' => $frame->data,
    //     ]));
    // }
    
    (new MessageHandler($server, $frame))();
});

$server->on('Close', function(Server $server, int $fd) {
    echo 'Connection close: ' . $fd . "\n";
});

$server->on('Disconnect', function(Server $server, int $fd) {
    echo 'Connection disconnect: ' . $fd . "\n";
});

$server->start();