<?php
	
namespace Mib\Component\WebSocket;

/**
 * Class Frame
 * @package Mib\Component\WebSockets
 */
class Frame
{
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