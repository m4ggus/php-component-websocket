<?php

namespace Mib\Component\WebSocket\Command;


use Mib\Component\WebSocket\AbstractCommand;
use Mib\Component\WebSocket\Client;
use Mib\Component\WebSocket\Server;

class SystemInfo extends AbstractCommand
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('sys:info');
    }

    /**
     * @param Server $server
     * @param Client $client
     */
    protected function execute(Server $server, Client $client)
    {
        $cpuInfo = $this->getCpuInfo();

        $memInfo = $this->getMemInfo();

        $result = sprintf("+%'-40s+\n", '-');
        $result.= sprintf("| %38s |\n", $cpuInfo['model name']);
        $result.= sprintf("+%'-40s+\n", '-');
        $result.= sprintf("| %-10s: %26s |\n", 'Core', $cpuInfo['processor']);
        $result.= sprintf("| %-10s: %26s |\n", 'Clock', round($cpuInfo['bogomips'] / 2, 0) . ' MHz');

        $client->write($result);
    }

    /**
     * @return array
     */
    protected function getCpuInfo()
    {
        $content = file_get_contents('/proc/cpuinfo');
        $lines   = explode("\n", $content);
        $cpuInfo = [];

        foreach ($lines as $line) {
            if (false === ($pos = strpos($line, ':'))) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            if ($key != 'processor' && isset($cpuInfo[$key])) {
                continue;
            }

            if ($key == 'processor') {
                if (!isset($cpuInfo[$key])) {
                    $cpuInfo[$key] = 1;
                } else {
                    $cpuInfo[$key]++;
                }
            } else {
                $cpuInfo[$key] = $val;
            }
        }

        return $cpuInfo;
    }

    protected function getMemInfo()
    {
        $content = file_get_contents('/proc/meminfo');
        $lines   = explode("\n", $content);
        $memInfo = [];

        foreach ($lines as $line) {
            if (false === ($pos = strpos($line, ':'))) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            if (isset($memInfo[$key])) {
                continue;
            }

            $memInfo[$key] = rtrim($val, ' kB');
        }

        return $memInfo;
    }
}