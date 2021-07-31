<?php

require 'vendor/autoload.php';

use Swoole\HTTP\Server;
use Swoole\Http\Response;
use Swoole\Http\Request;
use League\Plates\Engine;

$server = new Server("0.0.0.0", 8080, SWOOLE_PROCESS);

$server->set([
    'document_root' => '/app/public',
    'enable_static_handler' => true,
]);

$server->on("start", function($server) {
	$pid_file = __DIR__ . '/http-server-pid';
	if (file_exists($pid_file)) {
		unlink($pid_file);
	}
	file_put_contents($pid_file, $server->master_pid);
    echo 'Server started with PID: ' . $server->master_pid . " at http://127.0.0.1:8181\n";
});

$server->on('request', function(Request $request, Response $response) {
	$templates = new Engine(__DIR__ . '/src/views');
	
	$response->end($templates->render('index'));
});

$server->start();
