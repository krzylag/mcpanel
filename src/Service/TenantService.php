<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TenantDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TenantService implements TenantServiceInterface
{
    public function __construct(
        private readonly ParameterBagInterface $containerBag,
        private readonly RequestStack $requestStack,
    )
    {
    }

    /**
     * @return TenantDto[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllDefinitions(): array
    {
        return array_values(array_map(
            fn (array $config): TenantDto => TenantDto::fromConfiguration($config),
            $this->containerBag->get('app.tenants')
        ));
    }

    /**
     * @return TenantDto[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCurrentDefinitions(): array
    {
        $currentHost = $this->requestStack?->getCurrentRequest()?->getHost();
        if ($currentHost === null) {
            return [];
        }
        return array_values(array_filter(
            $this->getAllDefinitions(),
            fn (TenantDto $config): bool => $config->getHost() === $currentHost
        ));
    }
}