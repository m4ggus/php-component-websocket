<?php

namespace Mib\Component\WebSocket\Http;

use Mib\Component\WebSocket\Exception\InvalidArgumentException;

/**
 * Class Request
 * @package Mib\Component\WebSocket\Http
 */
class Request {

    /** @var string */
    private $method;

    /** @var string */
    private $requestUri;

    /** @var array */
    private $headers;

    /**
     * @param string $method
     * @param string $requestUri
     * @param array  $headers
     */
    public function __construct($method, $requestUri, array $headers) {
        $this->method = $method;
        $this->requestUri = $requestUri;
        $this->headers = $headers;
    }

    public static function createFromString($str)
    {
        $requestParts = explode("\r\n\r\n", $str);
        $headerParts  = explode("\n", $requestParts[0]);

        $requestPath  = array_shift($headerParts);

        if (!preg_match('/(GET|POST) (.+?) HTTP/i', $requestPath, $match)) {
            throw new InvalidArgumentException('path not found in request header');
        }

        $method = strtolower($match[1]);
        $path   = $match[2];

        $headers = [];

        foreach ($headerParts as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            $header = explode(':', $line, 2);
            $headers[strtolower($header[0])] = trim($header[1]);
        }

        return new self($method, $path, $headers);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name) {
        return isset($this->headers[$name]);
    }

    /**
     * @param string $name The header name
     * @return string
     * @throws InvalidArgumentException
     */
    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            throw new InvalidArgumentException(sprintf('header "%s" not set', $name));
        }

        return $this->headers[$name];
    }
}
