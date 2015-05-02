<?php

namespace Mib\Component\WebSocket;


abstract class AbstractCommand implements CommandInterface {

    /** @var string */
    private $name;

    public function __construct()
    {
        $this->configure();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return void
     */
    protected abstract function configure();

    /**
     * @param Server $server
     * @param Client $client
     */
    protected abstract function execute(Server $server, Client $client);

    public function run(Server $server, Client $client)
    {
        $this->execute($server, $client);
    }

}