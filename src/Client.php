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


    /**
     * Constructor
     * @param null|string $id
     * @param Socket      $socket
     */
    public function __construct($id = null,Socket $socket)
    {
        $this->socket = $socket;

        if ($id === null) {
            $id = uniqid('u');
        }

        $this->id = $id;
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
        // FIN, NO RSV1, NO RSV2, NORSV3, OPCODE 1   
        $m = chr(129);
        
        $l = strlen($message);
        if ($l < 127)
            // NO MASK, 7-BIT LENGTH
            $m .= chr($l);
        elseif ($l < 65536) {
            // NO MASK, 16-BIT LENGTH
            $m .= chr(126);
            $m .= chr($l / 256);
            $m .= chr($l % 256);
        } else {
            // NO MASK, 64-BIT LENGTH
        }
                
        $message = $m . $message;
        
        $messageLength = strlen($message);
        
        while ($messageLength > Socket::BUFFER_SIZE) {
            $buffer = substr($message, 0, Socket::BUFFER_SIZE);
            $this->socket->write($buffer, Socket::BUFFER_SIZE);
            
            $message = substr($message, Socket::BUFFER_SIZE);
            $messageLength = strlen($message);
        }
        
        if ($messageLength == 0) {
            return;
        }
        
        $this->socket->write($message, $messageLength);
    }
}