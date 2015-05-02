<?php
	
namespace Mib\Component\WebSocket;

/**
 * Class FrameDecoder
 * @package Mib\Component\WebSocket
 */
class FrameDecoder
{
    /**
     * Decodes a web socket message frame
     * @param $message
     * @return Frame
     */
	public function decode($message)
	{
        $opCode  = ord($message[0]) & Frame::OP_CODES;
		$mask    = ($message[1] & chr(Frame::MASK)) == chr(Frame::MASK);
		$maskKey = !$mask ? '' : $message[2] . $message[3] . $message[4] . $message[5];
		$payload = ord($message[1]) > 128 ? ord($message[1]) - 128 : ord($message[1]);
		$dataOffset = !$mask ? 2 : 6;
		
		if ($payload == FRAME::LENGTH_16BIT) {
			
			if ($mask) {
				$maskKey = $message[4] . $message[5] . $message[6] . $message[7];
			}
			
			$payload = ord($message[2]) * Frame::BYTE + ord($message[3]);
			$dataOffset = 8;
						
		} elseif ($payload == FRAME::LENGTH_64BIT) {
			
			if ($mask) {
				$maskKey = $message[10] . $message[11] . $message[12] . $message[13];
			}
			
			$payload = 
				  ord($message[2]) * Frame::SHORT * Frame::SHORT * Frame::SHORT * Frame::BYTE
				+ ord($message[3]) * Frame::SHORT * Frame::SHORT * Frame::SHORT
				+ ord($message[4]) * Frame::SHORT * Frame::SHORT * Frame::BYTE
				+ ord($message[5]) * Frame::SHORT * Frame::SHORT
				+ ord($message[6]) * Frame::SHORT * Frame::BYTE
				+ ord($message[7]) * Frame::SHORT
				+ ord($message[8]) * Frame::BYTE
				+ ord($message[9])
			;
				
			$dataOffset = 14;
		}
		
		$header = new Header();		
		$header->setFin(($message[0] & chr(Frame::FIN)) == chr(Frame::FIN));		
		$header->setRsv1(($message[0] & chr(Frame::RSV1)) == chr(Frame::RSV1));
		$header->setRsv2(($message[0] & chr(Frame::RSV2)) == chr(Frame::RSV2));
		$header->setRsv3(($message[0] & chr(Frame::RSV3)) == chr(Frame::RSV3));
        $header->setOpcode($opCode);
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