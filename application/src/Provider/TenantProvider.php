<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\TenantCollection;
use App\Dto\TenantDto;
use App\Repository\TenantRepository;
use InvalidArgumentException;

class TenantProvider
{
    public const array DEFAULT_DOMAINS = [
        'localhost',
    ];

    private const SEPARATOR = ' ';

    private TenantCollection $tenants;
    private ?TenantRepository $tenantRepository = null;

    public function __construct(
        string $hostsString,
        string $domainsString,
        string $rconPortsString,
        string $rconPasswordsString,
        string $javaPortsString,
        string $bedrockPortsString,
    )
    {
        $configurations = [
            'hosts' => $this->explodeString($hostsString),
            'domains' => $this->explodeString($domainsString),
            'rconPorts' => $this->explodeString($rconPortsString),
            'rconPasswords' => $this->explodeString($rconPasswordsString),
            'javaPorts' => $this->explodeString($javaPortsString),
            'bedrockPorts' => $this->explodeString($bedrockPortsString),
        ];
        $this->tenants = new TenantCollection();
        $itemsCount = null;
        foreach ($configurations as $key => $configuration) {
            if ($itemsCount === null) {
                $itemsCount = count($configuration);
            } elseif (count($configuration) !== $itemsCount) {
                throw new InvalidArgumentException('All configuration strings must have the same number of items. Offending string: ' . $key);
            }
        }
        for ($i = 0; $i < $itemsCount; $i++) {
            $this->tenants->add(
                new TenantDto(
                    $configurations['hosts'][$i],
                    $configurations['domains'][$i],
                    (int)$configurations['rconPorts'][$i],
                    $configurations['rconPasswords'][$i],
                    (int)$configurations['javaPorts'][$i],
                    (int)$configurations['bedrockPorts'][$i],
                )
            );
        }
    }

    private function explodeString(string $string): array
    {
        return explode(self::SEPARATOR, $string);
    }

    public function getCollection(): TenantCollection
    {
        return $this->tenants;
    }

    /** @return TenantDto[] */
    public function getArray(): array
    {
        return $this->tenants->toArray();
    }

    public function getById(string $id): ?TenantDto
    {
        foreach ($this->tenants->toArray() as $tenant) {
            if ($tenant->getHost() === $id) {
                return $tenant;
            }
        }
        return null;
    }

    public function getByHost(string $host): ?TenantDto
    {
        foreach ($this->tenants->toArray() as $tenant) {
            if ($tenant->getHost() === $host) {
                return $tenant;
            }
        }
        return null;
    }

    /** @return TenantDto[] */
    public function getByDomain(string $domain): array
    {
        return array_filter(
            $this->tenants->toArray(),
            function ($tenant) use ($domain) {
                return array_filter(
                    $tenant->getDomains(),
                    function (string $candidate) use ($domain) {
                        return $candidate === trim($domain);
                    }
                );
            }
        );
    }

    public function injectRepository(TenantRepository $tenantRepository): void
    {
        if ($this->tenantRepository === null) {
            $this->tenantRepository = $tenantRepository;
            $replacements = new TenantCollection();
            foreach ($this->tenants->toArray() as $tenantDto) {
                $domain = array_intersect($tenantDto->getDomains(), self::DEFAULT_DOMAINS);
                $replacements->add(
                    new TenantDto(
                        $tenantDto->getHost(),
                        array_pop($domain),
                        $tenantDto->getRconPort(),
                        $tenantDto->getRconPassword(),
                        $tenantDto->getJavaPort(),
                        $tenantDto->getBedrockPort(),
                        $this->tenantRepository->findOneBy(['mcTenantId' => $tenantDto->getHost()])
                    )
                );
            }
            $this->tenants = $replacements;
        }
    }
}