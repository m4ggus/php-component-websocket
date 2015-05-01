<?php

namespace Mib\Component\WebSocket;

/**
 * Class Socket
 * @package Mib\Component\WebSocket
 */
class Socket
{
	const BUFFER_SIZE = 4096;
	
	private $resource;

	/**
	 * Constructor
	 *
	 * Uses the passed socket resource, otherwise
	 * a default tcp stream socket will be created
	 * 
	 * @param resource $resource The resource
	 */
	public function __construct($resource = null)
	{
		if (null === $resource) {
			$resource = socket_create(
				AF_INET, 
				SOCK_STREAM, 
				SOL_TCP
			);
		}
		$this->resource = $resource;
	}

    /**
     * Binds the socket to the passed device
     *
     * @param  string $interface The interface to bind to
     * @param int     $port
     */
	public function bind($interface = 'localhost', $port = 0)
	{
		socket_set_option($this->resource, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($this->resource, $interface, $port);
	}

	public function close()
	{
		socket_close($this->resource);
	}

	/**
	 * Listens for incomming connections on the socket
	 * @return void
	 */
	public function listen()
	{
		socket_listen($this->resource);
	}

	public function accept()
	{
		$socket = socket_accept($this->resource);

		if (false === $socket) {
			throw new Exception('socket_accept failed');
		}

		return new Socket($socket);
	}

	public function getPeername()
	{
		if (false === socket_getpeername($this->resource, $ip)) {
			throw new Exception('socket_getpeername failed');
		}

		return $ip;
	}

	public function read($length = self::BUFFER_SIZE, $type = PHP_BINARY)
	{
		return @socket_read($this->resource, $length);
	}

	public function write($buffer)
	{	
		$length      = strlen($buffer);
		$processed   = 0;

		while ($processed < $length) {
			$sent = @socket_write(
				$this->resource, 
				$buffer, 
				$length < self::BUFFER_SIZE 
				? $length 
				: self::BUFFER_SIZE
			);

			if ($sent === false) {				
				throw new Exception('socket_write failed');
			}

			$processed += $sent;
			echo sprintf("send %s from %s bytes...\n", $processed, $length);
		}
				
	}

	public function getResource()
	{
		return $this->resource;
	}
}