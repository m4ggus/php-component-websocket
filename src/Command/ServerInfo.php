<?php

namespace Mib\Component\WebSocket\Command;

use Mib\Component\WebSocket\AbstractCommand;
use Mib\Component\WebSocket\Client;
use Mib\Component\WebSocket\Server;

class ServerInfo extends AbstractCommand {

    protected function configure()
    {
        $this->setName('server:info');
    }

    protected function execute(Server $server, Client $client)
    {
        $clients = $server->getClients();

        $message = '';

        for ($i = 0, $l = count($clients); $i < $l; ++$i) {
            $socket = $clients[$i]->getSocket();
            $index  = $i + 1;
            $ip     = $socket->getPeer();
            $port   = $socket->getPort();

            if (!$i) {
                $message .= sprintf("+%'-24s+\n", '-');
                $message .= sprintf("|%24s|\n", 'Server Information');
                $message .= sprintf("|%24s|\n", 'Clients: '.$l);
                $message .= sprintf("+%'-24s+\n", '-');
            }

            $message .= sprintf("%03s: %15s:%-5s\n", $index, $ip, $port);
        }

        $client->write($message);
    }


}