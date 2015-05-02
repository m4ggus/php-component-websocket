<?php

namespace Mib\Component\WebSocket;
use Mib\Component\WebSocket\Exception\InvalidArgumentException;
use Mib\Component\WebSocket\Exception\IOException;

/**
 * Class Socket
 * @package Mib\Component\WebSocket
 */
class Socket
{
	const BUFFER_SIZE = 4096;

    const TYPE_BINARY = PHP_BINARY_READ;
    const TYPE_TEXT   = PHP_NORMAL_READ;

    /** @var null|resource */
	private $resource;

    /**
     * Constructor
     *
     * Uses the passed socket resource, otherwise
     * a default tcp stream socket will be created
     *
     * @param resource $resource The resource
     * @throws InvalidArgumentException
     */
	public function __construct($resource = null)
	{
        if ($resource && !is_resource($resource)) {
            throw new InvalidArgumentException(
                sprintf(
                    'resource expected, got "%s" for $resource',
                    gettype($resource)
                )
            );
        }

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
     * @param  int    $port
     * @throws Exception
     */
	public function bind($interface = 'localhost', $port = 0)
	{
		@socket_set_option($this->resource, SOL_SOCKET, SO_REUSEADDR, 1);

        $res = @socket_bind($this->resource, $interface, $port);

        if (false === $res) {
            $error = socket_last_error($this->resource);
            $message = socket_strerror($error);
            throw new Exception($message);
        }
	}

	public function close()
	{
        if ($this->resource === null) {
            return;
        }

		@socket_close($this->resource);
	}

    /**
     * Listens for incomming connections on the socket
     * @throws Exception
     */
	public function listen()
	{
		$res = @socket_listen($this->resource);

        if (false === $res) {
            $error = socket_last_error($this->resource);
            $message = socket_strerror($error);
            throw new Exception($message);
        }
	}

    /**
     * Accepts a pending connection and returns a new Socket
     *
     * @return Socket
     * @throws Exception
     */
	public function accept()
	{
		$socket = socket_accept($this->resource);

		if (false === $socket) {
            $error = socket_last_error($this->resource);
            $message = socket_strerror($error);
			throw new Exception($message);
		}

		return new Socket($socket);
	}

    /**
     * @return string
     * @throws Exception
     */
	public function getPeer()
	{
		if (false === @socket_getpeername($this->resource, $ip, $port)) {
            $error = socket_last_error($this->resource);
            $message = socket_strerror($error);
			throw new Exception($message);
		}

		return $ip;
	}

    /**
     * Read from the socket and returns the string
     *
     * The representation is based on the given type
     *
     * @param int $length
     * @param int $type
     * @return string
     * @throws IOException
     */
	public function read($length = self::BUFFER_SIZE, $type = self::TYPE_BINARY)
	{
		$buffer = @socket_read($this->resource, $length, $type);

        if (false === $buffer) {
            $error   = socket_last_error($this->resource);
            $message = socket_strerror($error);
            throw new IOException($message);
        }

        return $buffer;
	}

    /**
     * Write buffer to the socket
     * @param     $buffer
     * @param int $length
     * @return int
     * @throws IOException
     */
	public function write($buffer, $length = self::BUFFER_SIZE)
	{	
//		$length      = strlen($buffer);
//		$processed   = 0;

//		while ($processed < $length) {
			$sent = @socket_write(
				$this->resource, 
				$buffer, 
				$length
			);

			if ($sent === false) {
                $error = socket_last_error($this->resource);
                $message = socket_strerror($error);
				throw new IOException($message);
			}

//			$processed += $sent;
//		}
        return $sent;
	}

    /**
     * Returns the underlying socket resource
     * @return null|resource
     */
	public function getResource()
	{
		return $this->resource;
	}
}