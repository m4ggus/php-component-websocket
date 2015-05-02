<?php
/**
 * User: marcus
 * Date: 5/2/15
 * Time: 11:07 PM
 */

namespace Mib\Component\WebSocket;


interface CommandInterface {

    public function getName();

    public function run(Server $server, Client $client);

}