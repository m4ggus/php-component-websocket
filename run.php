<?php

include_once 'vendor/autoload.php';

use Mib\Component\WebSocket\ServerFactory;

$server = ServerFactory::create('localhost', 9999);

$server->run();