<?php

require 'vendor/autoload.php';
require 'app/WebSocketServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketServer()
        )
    ),
    8080 // The WebSocket server runs on port 8080
);

echo "WebSocket Server started on port 8080...\n";
$server->run();
