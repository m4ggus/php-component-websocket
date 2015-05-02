<?php

namespace Mib\Component\WebSocket;

use Mib\Component\WebSocket\Exception\InvalidArgumentException;

/**
 * Class FrameEncoder
 * @package Mib\Component\WebSocket
 */
class FrameEncoder {

    /**
     * @param $message
     * @return string
     * @throws InvalidArgumentException
     */
    public function encode($message)
    {
        if (!is_string($message)) {
            throw new InvalidArgumentException(
                sprintf('string expected, got "%s" for $message', gettype($message))
            );
        }

        $length = strlen($message);

        $prefix = chr(Frame::FIN + Frame::OP_CODE_TEXT);

        if ($length < Frame::LENGTH_16BIT) {
            $prefix .= chr($length);
        } else if ($length < Frame::SHORT) {
            // 16 bit
            $prefix .= chr(Frame::LENGTH_16BIT);
            $prefix .= chr($length / Frame::BYTE) . chr($length & Frame::BYTE);
        } else {
            // 64bit
            $prefix .= chr(Frame::LENGTH_64BIT);
            $prefix
                .=chr($length / (Frame::SHORT * Frame::SHORT * Frame::SHORT * Frame::BYTE))
                . chr($length / (Frame::SHORT * Frame::SHORT * Frame::SHORT))
                . chr($length / (Frame::SHORT * Frame::SHORT * Frame::BYTE))
                . chr($length / (Frame::SHORT * Frame::SHORT))
                . chr($length / (Frame::SHORT * Frame::BYTE))
                . chr($length / Frame::SHORT)
                . chr($length / Frame::BYTE)
                . chr($length % Frame::BYTE)
            ;
        }

        return $prefix.$message;
    }
}