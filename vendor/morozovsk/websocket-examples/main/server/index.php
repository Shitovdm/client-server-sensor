#!/usr/bin/env php
<?php

if (empty($argv[1]) || !in_array($argv[1], array('start', 'stop', 'restart'))) {
    die("не указан параметр (start|stop|restart)\r\n");
}

$config = array(
    'class' => 'morozovsk\websocket\examples\game\server\GameWebsocketDaemonHandler',
    'pid' => '/tmp/websocket_game.pid',
    'websocket' => 'tcp://192.168.1.47:8002',
    //'websocket' => 'tcp://192.168.1.36:8002',
    //'localsocket' => 'tcp://127.0.0.1:8002',
    //'master' => 'tcp://127.0.0.1:8020',
    //'eventDriver' => 'event',
    'timer' => 0.1
);

require_once __DIR__ . '/../../../../autoload.php';

$WebsocketServer = new morozovsk\websocket\Server($config);
call_user_func(array($WebsocketServer, $argv[1]));
