<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tenant;
use App\Provider\TenantProvider;

interface TenantServiceInterface
{
    public function getTenant(int $tenantId, bool $forCurrentDomain = false, bool $forCurrentUser = false): ?Tenant;

    /** @return Tenant[] */
    public function getTenants(bool $forCurrentDomain = false, bool $forCurrentUser = false): array;

    /** @return array<int,string> */
    public function getTenantsChoices(bool $forCurrentDomain = false, bool $forCurrentUser = false): array;

    public function getProvider(bool $injectRepository = true): TenantProvider;
}