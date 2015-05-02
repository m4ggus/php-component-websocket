<?php

namespace Mib\Component\WebSocket;
use Mib\Component\WebSocket\Http\Request;

/**
 * Class Server
 * @package Mib\Component\WebSocket
 */
class Server
{
    /** @var Socket */
    private $socket;

    /** @var array|Client[] */
    private $clients;

    /** @var FrameDecoder */
    private $decoder;

    public function __construct($socket, $decoder)
    {
        $this->socket  = $socket;
        $this->clients = [];
        $this->decoder = $decoder;
    }

    public function addClient(Client $client)
    {
        $this->clients[] = $client;
    }

    public function removeClient(Client $client)
    {
        $index = array_search($client, $this->clients, true);

        if ($index === false)
            return false;

        unset($this->clients[$index]);

        return true;
    }

    public function getClientSockets()
    {
        $sockets = [];

        foreach ($this->clients as $client) {
            $sockets[] = $client->getSocket()->getResource();
        }

        return $sockets;
    }

    public function getClientByResource($resource)
    {
        foreach ($this->clients as $client) {
            if ($resource == $client->getSocket()->getResource()) {
                return $client;
            }
        }

        return null;
    }

    public function run()
    {
        $run    = true;
        $socket = $this->socket;

        $lastRead = null;

        $count = 0;

        while ($run) {
            ++$count;

            $readable   = $this->getClientSockets();
            $readable[] = $socket->getResource();
            $writable   = null;
            $except     = null;

            // continue if there is nothing to do
            if (socket_select($readable, $writable, $except, 0) < 1)
                continue;

            // new client on the server socket
            if (in_array($socket->getResource(), $readable)) {
                $clientSocket = $socket->accept();
                $client       = new Client(null, $clientSocket, new FrameEncoder());
                $this->addClient($client);
                echo "New client connected: {$client->getSocket()->getPeer()}" . PHP_EOL;
                $index = array_search($socket->getResource(), $readable);
                unset($readable[$index]);
            }

            // iterate over the client list
            foreach ($readable as $read) {
                $client = $this->getClientByResource($read);

                $buffer     = $client->read();
                $bufferSize = strlen($buffer);


                echo "Read from {$client->getSocket()->getPeer()} ({$bufferSize}bytes)" . PHP_EOL;

                // client closed the connection
                if ('' === $buffer) {
                    $this->removeClient($client);
                    echo "Connection reset by client";
                    continue;
                }

                // handshake
                if (!$client->isAuthenticated()) {

                    $httpRequest = Request::createFromString($buffer);

                    echo "Authorize request" . PHP_EOL;
                    $client->handshake(new Handshake(), $httpRequest);
                    continue;
                }


                $frame = $this->decoder->decode($buffer);
                $header = $frame->getHeader();

                echo $header . PHP_EOL;

                if ($header->getOpcode() == 8) {
                    $this->removeClient($client);
                    echo "Connection closed by client" . PHP_EOL;
                    continue;
                }

                echo substr($frame->getData(), 0, 40) . PHP_EOL;

                if ($frame->getData() == 'load') {
                    $client->write(`ps aux`);
                } else if ($frame->getData() == 'uptime') {
                    $client->write(`uptime`);
                }
                // do something with data
            } // end foreach

            // to prevent steady load
            usleep(1000000 / 60);
        } // while

        // close the server socket
        $socket->close();
    }
}