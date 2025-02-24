<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TenantDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface TenantServiceInterface
{
    /**
     * @return TenantDto[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAllDefinitions(): array;

    /**
     * @return TenantDto[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCurrentDefinitions(): array;
}