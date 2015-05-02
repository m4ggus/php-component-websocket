<?php

include_once 'vendor/autoload.php';

use Mib\Component\WebSocket\ServerFactory;

/** @var \Mib\Component\WebSocket\Server $server */
$server = ServerFactory::create('localhost', 9999);

$server->registerCommand(new \Mib\Component\WebSocket\Command\ServerInfo());

$server->run();