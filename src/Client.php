<?php

namespace Mib\Component\WebSocket;

/**
 * Class Client
 * @package Mib\Component\WebSocket
 */
class Client {

    /** @var string */
    private $id;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var boolean
     */
    private $authenticated;

    private $encoder;

    /**
     * Constructor
     * @param null|string  $id
     * @param Socket       $socket
     * @param FrameEncoder $encoder
     */
    public function __construct($id = null,Socket $socket, FrameEncoder $encoder)
    {
        $this->socket = $socket;

        if ($id === null) {
            $id = uniqid('u');
        }

        $this->id = $id;

        $this->encoder = $encoder;
    }

    /**
     * Returns the socket associated with the client
     * @return Socket
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Returns true if the web socket is already authenticated
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * Handles the handshake by the given handshake provider
     * @param Handshake $handshake
     * @param           $buffer
     * @throws Exception
     */
    public function handshake(Handshake $handshake, $buffer)
    {
        $response = $handshake->buildFromHeader($buffer);

        $this->socket->write($response);

        $this->authenticated = true;
    }
    
    public function read()
    {
        $message = '';
        
        do {
            $buffer   = $this->socket->read();            
            $bytes    = strlen($buffer);
            $message .= $buffer;
        } while ($bytes == Socket::BUFFER_SIZE);
        
        return $message;
    }
    
    public function write($message)
    {
        /*
         * @TODO if the encoded frame is split up on the socket write process the js client will got a broken package
         * socket_get_option($this->resource, SOL_SOCKET, SO_SNDBUF) ??
         */

        $frameData = $this->encoder->encode($message);
        $frameLength = strlen($frameData);

//        while ($frameLength > Socket::BUFFER_SIZE) {
//            $buffer = substr($frameData, 0, Socket::BUFFER_SIZE);
//            $bytes = $this->socket->write($buffer, Socket::BUFFER_SIZE);
//
//            $frameData = substr($frameData, $bytes);
//            $frameLength = strlen($frameData);
//        }
//
//        if ($frameLength == 0) {
//            return;
//        }
        
        $this->socket->write($frameData, $frameLength);
    }
}