<?php

namespace Mib\Component\WebSocket;
use Mib\Component\WebSocket\Http\Request;
use Mib\Component\WebSocket\Http\Response;

/**
 * Class Handshake
 * @package Mib\Component\WebSocket
 */
class Handshake
{
	const TOKEN = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    /** @var string */
	private $host;
    /** @var integer */
	private $port;
    /** @var string */
	private $path;
    /** @var string */
	private $token;

    /**
     * Creates a handshake response by the given request
     * @param $request
     * @return string
     */
	public function buildFromHeader($request)
	{
		$lines   = explode("\n", $request);
		$path    = null;
		$headers = [];

		foreach ($lines as $line) {
			if (strpos($line, ':') !== false) {
				$header = explode(':', $line, 2);
				$headers[strtolower($header[0])] = trim($header[1]);
				continue;
			}

			if (preg_match('/GET (.+?) HTTP/i', $line, $match)) {
				$path = trim($match[1]);
			}
		}

		if (null === $path) {
			return "HTTP/1.1 405 Method Not Allowed\r\n\r\n";
		}

		$socket = explode(':', $headers['host'], 2);
		$host = $socket[0];
		$port = $socket[1];
		$token = $headers['sec-websocket-key'];

		$this->host = $host;
        $this->port = $port;
        $this->token = $token;
        $this->path = $path;

		// host, upgrade != 'websocket', connection != upgrade, sec-websocket-key 400 Bad Request

		// sec-websocket-version != 13 426 Upgrade Required\r\nSec-WebSocketVersion: 13"

        return $this->getResponse();
	}

    /**
     * @return string
     */
	public function getResponse()
	{
		$accept = base64_encode(
			pack('H*', sha1($this->token . self::TOKEN))
		);
		
		return"HTTP/1.1 101 WebSocket Protocol Handshake\r\n"
			. "Upgrade: websocket\r\n"
			. "Connection: Upgrade\r\n"
			. "WebSocket-Origin: http://{$this->host}\r\n"
			. "WebSocket-Location: ws://{$this->host}:{$this->port}/{$this->path}\r\n"
			. "Sec-WebSocket-Accept:$accept\r\n\r\n";

	}

    /**
     * @param Request $request
     * @return Response
     */
    public function createResponse(Request $request)
    {
        $statusCode = 101;
        $headers    = [];
        $content    = '';

        $socket = $request->getHeader('host');
        $parts  = explode(':', $socket);
        $host   = $parts[0];
        $port   = $parts[1];
        $key    = $request->getHeader('sec-websocket-key');
        $origin = $request->hasHeader('origin') ? $request->getHeader('origin') : $host;
        $token  = $this->createToken($key);

        $headers['Upgrade']              = 'websocket';
        $headers['Connection']           = 'Upgrade';
        $headers['WebSocket-Origin']     = "http://{$host}";
        $headers['WebSocket-Location']   = "ws://{$host}:{$port}/{$request->getRequestUri()}";
        $headers['Sec-WebSocket-Accept'] = $token;

        return new Response($content, $statusCode, $headers);
    }

    private function createToken($key)
    {
        return base64_encode(
            pack('H*', sha1($key . self::TOKEN))
        );
    }
}