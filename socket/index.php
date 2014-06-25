<?php

// WebSocket server
// (c) Antti Stenvall
// antti@stenvall.fi
//

// run websocket server:
// nohup php index.php &
// kill websocket server:
// ps aux | grep index.php
// kill [pid]

require_once '../vendor/autoload.php';
require_once '../model/temperatureSocket.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$ts = new temperatureSocket();

$server = IoServer::factory(
    new HttpServer(
    new WsServer(
    $ts
    )
    ), 8081
);

// add $ts->broadcast to loop
$server->loop->addPeriodicTimer(1, function () use ($ts) {
    $ts->broadcast();
  });

$server->run();
?>
