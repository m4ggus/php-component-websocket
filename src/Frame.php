<?php
	
namespace Mib\Component\WebSocket;

class Frame
{
	
	private $header;
	
	private $data;
	
	public function __construct(Header $header, $data)
	{
		$this->header = $header;
		$this->data   = $data;
	}
	
	public function getHeader()
	{
		return $this->header;
	}
	
	public function getData()
	{
		return $this->data;
	}
}