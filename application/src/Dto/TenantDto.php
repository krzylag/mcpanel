<?php


declare(strict_types=1);

namespace App\Dto;

use App\Entity\Tenant;
use App\Provider\TenantProvider;

readonly class TenantDto
{
    public function __construct(
        private string $host,
        private string $domain,
        private int    $rconPort,
        private string $rconPassword,
        private int    $javaPort,
        private int    $bedrockPort,
        private ?Tenant $tenant = null,
    ) {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getDomains(): array
    {
        return array_unique(array_merge(
            [$this->domain],
            TenantProvider::DEFAULT_DOMAINS
        ));
    }

    public function getRconPort(): int
    {
        return $this->rconPort;
    }

    public function getRconPassword(): string
    {
        return $this->rconPassword;
    }

    public function getJavaPort(): int
    {
        return $this->javaPort;
    }

    public function getBedrockPort(): int
    {
        return $this->bedrockPort;
    }

    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'domain' => $this->domain,
            'rconPort' => $this->rconPort,
            'rconPassword' => $this->rconPassword,
            'javaPort' => $this->javaPort,
            'bedrockPort' => $this->bedrockPort,
        ];
    }

    public function getTenantEntity(): ?Tenant
    {
        return $this->tenant;
    }
}

