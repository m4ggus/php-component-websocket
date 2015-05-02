<?php

namespace Mib\Component\WebSocket\Http;

/**
 * Class Response
 * @package Mib\Component\WebSocket\Http
 */
class Response {

    public static $statusCodes = [
        101 => 'WebSocket Protocol Handshake',
        200 => 'OK',
    ];

    private $statusCode;

    private $headers;

    private $content;

    public function __construct($content, $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }



    public function __toString()
    {
        $headers       = $this->getHeaders();
        $content       = $this->getContent();
        $statusCode    = $this->getStatusCode();
        $statusMessage = isset(self::$statusCodes[$statusCode])
            ? self::$statusCodes[$statusCode]
            : '';

        $buffer = "HTTP/1.1 {$statusCode} {$statusMessage}\r\n";

        foreach ($headers as $name => $option) {
            $buffer .= "{$name}: {$option}\r\n";
        }

        $buffer .= "\r\n{$content}";

        return $buffer;
    }
}