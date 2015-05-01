<?php
	
namespace Mib\Component\WebSocket;

class FrameDecoder
{
	const FIN = 128;
	const RSV1 = 64;
	const RSV2 = 32;
	const RSV3 = 16;
	
	const OPCODE = 15;
	
	const MASK = 128;
	
	const BYTE = 256;
	const SHORT = 65536;

	
	public function decode($message)
	{
		$mask    = ($message[1] & chr(self::MASK)) == chr(self::MASK);
		$maskKey = !$mask ? '' : $message[2] . $message[3] . $message[4] . $message[5];
		$payload = ord($message[1]) > 128 ? ord($message[1]) - 128 : ord($message[1]);
		$dataOffset = !$mask ? 2 : 6;
		
		if ($payload == 126) {
			
			if ($mask) {
				$maskKey = $message[4] . $message[5] . $message[6] . $message[7];
			}
			
			$payload = ord($message[2]) * self::BYTE + ord($message[3]);
			$dataOffset = 8;
						
		} elseif ($payload == 127) {
			
			if ($mask) {
				$maskKey = $message[10] . $message[11] . $message[12] . $message[13];
			}
			
			$payload = 
				  ord($message[2]) * self::SHORT * self::SHORT * self::SHORT * self::BYTE
				+ ord($message[3]) * self::SHORT * self::SHORT * self::SHORT
				+ ord($message[4]) * self::SHORT * self::SHORT * self::BYTE
				+ ord($message[5]) * self::SHORT * self::SHORT
				+ ord($message[6]) * self::SHORT * self::BYTE
				+ ord($message[7]) * self::SHORT
				+ ord($message[8]) * self::BYTE
				+ ord($message[9])
			;
				
			$dataOffset = 14;
		}
		
		$header = new Header();		
		$header->setFin(($message[0] & chr(self::FIN)) == chr(self::FIN));		
		$header->setRsv1(($message[0] & chr(self::RSV1)) == chr(self::RSV1));
		$header->setRsv2(($message[0] & chr(self::RSV2)) == chr(self::RSV2));
		$header->setRsv3(($message[0] & chr(self::RSV3)) == chr(self::RSV3));		
		$header->setOpcode(ord($message[0]) & self::OPCODE);		
		$header->setMask($mask);		
		$header->setPayload($payload);
		$header->setMaskKey($maskKey);				
		
		$data = substr($message, $dataOffset);
		
		if ($mask) {
			// xor by i % 4 with the given mask 			
			for ($i = 0, $l = strlen($data); $i < $l; ++$i) {
				$data[$i] = $data[$i] ^ $maskKey[$i % 4];
			}			
		}
		
		return new Frame($header, $data);
	}
	
	public static function charToBinary($chr)
	{
		$chr = ord($chr);
		$str = '';
		do {			
			$str = ($chr % 2).$str;
			$chr /= 2;
		} while ($chr >= 1);
		
		return $str;
	}
}