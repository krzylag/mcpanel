<?php

declare(strict_types=1);

namespace App\Service\Rcon;

class RconShellConnector
{
    public function sendCommand(string $command, string $hostname): string
    {
        $command = sprintf(
            'rconclt %s %s',
            $hostname,
            $command,
        );
        return shell_exec($command);
    }
}