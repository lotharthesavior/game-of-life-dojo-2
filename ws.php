<?php

require 'vendor/autoload.php';

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use App\MessageHandler;

if (!defined('GLOBAL_GRID_SIZE_KEY')) {
    define('GLOBAL_GRID_SIZE_KEY', 1);
}

$grid = new Swoole\Table(1024);
$grid->column('gridsize', Swoole\Table::TYPE_STRING, 64);
$grid->create();

$server = new Server("0.0.0.0", 8181);

$server->set([
    'reactor_num' => 8,
    'worker_num' => 8,
    'enable_coroutine' => true,
    'max_coroutine' => 3000,
]);

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

$server->on('Open', function(Server $server, Request $request) {
    echo "connection open: {$request->fd}\n";
});

$server->on('Message', function(Server $server, Frame $frame) use ($grid) {
    (new MessageHandler($server, $frame, $grid))();
});

$server->on('Close', function(Server $server, int $fd) {
    echo 'Connection close: ' . $fd . "\n";
});

$server->on('Disconnect', function(Server $server, int $fd) {
    echo 'Connection disconnect: ' . $fd . "\n";
});

$server->start();