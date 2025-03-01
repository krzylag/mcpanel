<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TenantsCollection;

interface TenantServiceInterface
{
    public function getTenants(bool $forCurrentDomain = false, bool $forCurrentUser = false): TenantsCollection;

    /** @return array<int,string> */
    public function getTenantsChoices(bool $forCurrentDomain = false, bool $forCurrentUser = false): array;
}