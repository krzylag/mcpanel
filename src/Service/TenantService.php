<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TenantsCollection;
use App\Repository\TenantRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class TenantService implements TenantServiceInterface
{
    public function __construct(
        private readonly TenantRepository $tenantRepository,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
    )
    {
    }

    public function getTenants(bool $forCurrentDomain = false, bool $forCurrentUser = false): TenantsCollection
    {
        $tenants =  $this->tenantRepository->findFiltered(
            $forCurrentDomain ? $this->requestStack->getCurrentRequest()->getHost() : null,
            $forCurrentUser ? $this->security->getUser() : null,
        );
        if (count($tenants) === 0) {
            $tenants =  $this->tenantRepository->findFiltered(
                null,
                $forCurrentUser ? $this->security->getUser() : null,
            );
        }
        return new TenantsCollection($tenants);
    }

    /** @return array<int,string> */
    public function getTenantsChoices(bool $forCurrentDomain = false, bool $forCurrentUser = false): array
    {
        $tenants = $this->getTenants($forCurrentDomain, $forCurrentUser);
        $choices = [];
        foreach ($tenants as $tenant) {
            $choices[$tenant->getId()] = $this->translator->trans($tenant->getName());
        }
        return $choices;
    }
}