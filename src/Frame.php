<?php
	
namespace Mib\Component\WebSocket;

/**
 * Class Frame
 * @package Mib\Component\WebSockets
 */
class Frame
{
    const FIN                  = 128;

    const RSV1                 = 64;
    const RSV2                 = 32;
    const RSV3                 = 16;

    const OP_CODES             = 15;

    const OP_CODE_CONTINUATION = 0;
    const OP_CODE_TEXT         = 1;
    const OP_CODE_BINARY       = 2;
    const OP_CODE_CLOSE        = 8;
    const OP_CODE_PING         = 9;
    const OP_CODE_PONG         = 10;

    const MASK                 = 128;
    const BYTE                 = 256;
    const SHORT                = 65536;
    const LENGTH_16BIT         = 126;
    const LENGTH_64BIT         = 127;


    /** @var Header */
	private $header;

    /** @var mixed */
	private $data;

    /**
     * @param Header $header
     * @param mixed  $data
     */
	public function __construct(Header $header, $data)
	{
		$this->header = $header;
		$this->data   = $data;
	}

    /**
     * @return Header
     */
	public function getHeader()
	{
		return $this->header;
	}

    /**
     * @return mixed
     */
	public function getData()
	{
		return $this->data;
	}
}