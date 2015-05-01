<?php

namespace Mib\Component\WebSocket;

class ServerFactory
{
	
	public static function create($interface, $port)
	{
		$socket = new Socket();

		$decoder = new FrameDecoder();

		$socket->bind($interface, $port);

		$socket->listen();

		return new Server($socket, $decoder);
	}
}