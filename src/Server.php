<?php

namespace Mib\Component\WebSocket;

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
	
	private $decoder;

	public function __construct($socket, $decoder)
	{
		$this->socket = $socket;
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

		foreach ($this->clients as $client)
		{
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
		$run = true;
		$socket = $this->socket;

		$lastRead = null;

		$count = 0;

		while ($run) {			
			++$count;

			$readable   = $this->getClientSockets();
			$readable[] = $socket->getResource();
			$writable   = null;
			$except     = null;

			$before = count($readable);
			if (socket_select($readable, $writable, $except, 0) < 1)
				continue;

			if (in_array($socket->getResource(), $readable)) {
				$clientSocket = $socket->accept();
                $client = new Client(null, $clientSocket);
				$this->addClient($client);
				echo "New client connected: {$client->getSocket()->getPeername()}".PHP_EOL;
				$index = array_search($socket->getResource(), $readable);
				unset($readable[$index]);
			}

			foreach ($readable as $read) {
				$client = $this->getClientByResource($read);
				
				$buffer = $client->read();
				$bufferSize = strlen($buffer);
				

				echo "Read from {$client->getSocket()->getPeername()} ({$bufferSize}bytes)".PHP_EOL;

				if ('' === $buffer) {
					$this->removeClient($client);
					echo "Connection reset by client";
					continue;
				}
                
                if (!$client->isAuthenticated()) {
                    echo "Authorize request".PHP_EOL;
                    $client->handshake(new Handshake(), $buffer);
					continue;
                } 
				
				$frame = $this->decoder->decode($buffer);

				$header = $frame->getHeader();
				
				echo $header.PHP_EOL;
				
				if ($header->getOpcode() == 8) {
					$this->removeClient($client);
					echo "Connection closed by client".PHP_EOL;
					continue;
				}
				
				echo substr($frame->getData(), 0, 40).PHP_EOL;
				
				if ($frame->getData() == 'load') {
					$client->write(`ps aux`);
				}
				// do something with data
			} // end foreach
			//usleep(1000000 / 60);
		} // while

		$socket->close();

	}
}