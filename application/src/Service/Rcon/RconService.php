<?php

declare(strict_types=1);

namespace App\Service\Rcon;

use App\Entity\Tenant;

readonly class RconService implements RconServiceInterface
{

    public function __construct(
        private RconShellConnector $rconConnector,
    ) {
    }

    public function sendCommand(string $command, Tenant $tenant): string
    {
        $response = $this->rconConnector->sendCommand($command, $tenant->getMcTenantId());
        return $this->parseMinecraftColors(trim($response));
    }

    private function parseMinecraftColors($string) {
        $string = mb_convert_encoding(htmlspecialchars($string, ENT_QUOTES, "UTF-8"), 'ISO-8859-1', 'UTF-8');
        $string = preg_replace('/\xA7([0-9a-f])/i', '<span class="mc-color mc-$1">', $string, -1, $count) . str_repeat("</span>", $count);
        return mb_convert_encoding(preg_replace('/\xA7([k-or])/i', '<span class="mc-$1">', $string, -1, $count) . str_repeat("</span>", $count), 'UTF-8', 'ISO-8859-1');
    }
}