<?php

declare(strict_types=1);

namespace App\Service\Rcon;

use App\Entity\Tenant;

interface RconServiceInterface
{
    public function sendCommand(string $command, Tenant $tenant): string;
}