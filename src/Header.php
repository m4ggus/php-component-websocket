<?php
	
namespace Mib\Component\WebSocket;

/**
 * Class Header
 * @package Mib\Component\WebSocket
 */
class Header
{	
	private $fin;
	private $rsv1;
	private $rsv2;
	private $rsv3;
	private $opcode;
	private $mask;
	private $payload;
	private $maskKey;
	
	public function getFin()
	{
		return $this->fin;
	}
	
	public function setFin($fin)
	{
		$this->fin = $fin;
	}
	
	public function getRsv1()
	{
		return $this->rsv1;
	}
	
	public function setRsv1($rsv1)
	{
		$this->rsv1 = $rsv1;
	}
	
	public function getRsv2()
	{
		return $this->rsv2;
	}
	
	public function setRsv2($rsv2)
	{
		$this->rsv2 = $rsv2;
	}
		
	public function getRsv3()
	{
		return $this->rsv3;
	}
	
	public function setRsv3($rsv3)
	{
		$this->rsv3 = $rsv3;
	}
	
	public function getOpcode()
	{
		return $this->opcode;
	}
	
	public function setOpcode($opcode)
	{
		$this->opcode = $opcode;
	}
	
	public function getMask()
	{
		return $this->mask;
	}
	
	public function setMask($mask)
	{
		$this->mask = $mask;
	}
	
	public function getMaskKey()
	{
		return $this->maskKey;
	}
	
	public function setMaskKey($maskKey)
	{
		$this->maskKey = $maskKey;
	}
	
	public function getPayload()
	{
		return $this->payload;
	}
	
	public function setPayload($payload)
	{
		$this->payload = $payload;
	}
	
	public function __toString()
	{
		return sprintf(
			"[FIN:%d|RSV1:%d|RSV2:%d|RSV3:%d|OPCODE:%d|MASK:%d|PAYLOAD:%d]",
			$this->fin,
			$this->rsv1,
			$this->rsv2,
			$this->rsv3,
			$this->opcode,
			$this->mask,
			$this->payload
		);
	}
}