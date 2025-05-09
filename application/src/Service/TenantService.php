<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tenant;
use App\Entity\User;
use App\Provider\TenantProvider;
use App\Repository\TenantRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class TenantService implements TenantServiceInterface
{
    public function __construct(
        private TenantRepository    $tenantRepository,
        private TenantProvider      $tenantProvider,
        private TranslatorInterface $translator,
        private RequestStack        $requestStack,
        private Security            $security,
    )
    {
    }

    public function getTenant(int $tenantId, bool $forCurrentDomain = false, bool $forCurrentUser = false): ?Tenant
    {
        $tenants = $this->getTenants($forCurrentDomain, $forCurrentUser);
        foreach ($tenants as $tenant) {
            if ($tenant->getId() === $tenantId) {
                return $tenant;
            }
        }
        return null;
    }

    /** @return Tenant[] */
    public function getTenants(bool $forCurrentDomain = false, bool $forCurrentUser = false): array
    {
        if ($forCurrentUser) {
            $currentUser = $this->security->getUser();
            if ($currentUser->hasRole(User::ROLE_SUPER_ADMIN)) {
                $forCurrentUser = false;
            }
        }

        $tenants =  $this->tenantRepository->findFiltered(
            $forCurrentDomain ? $this->requestStack->getCurrentRequest()->getHost() : null,
            $forCurrentUser ? $currentUser : null,
        );
        if (count($tenants) === 0) {
            $tenants =  $this->tenantRepository->findFiltered(
                null,
                $forCurrentUser ? $this->security->getUser() : null,
            );
        }
        return $tenants;
    }

    /** @return array<int,string> */
    public function getTenantsChoices(bool $forCurrentDomain = false, bool $forCurrentUser = false): array
    {
        if ($forCurrentUser) {
            $currentUser = $this->security->getUser();
            if ($currentUser->hasRole(User::ROLE_SUPER_ADMIN)) {
                $forCurrentUser = false;
            }
        }

        $tenants = $this->getTenants($forCurrentDomain, $forCurrentUser);
        $choices = [];
        foreach ($tenants as $tenant) {
            $choices[$tenant->getId()] = $this->translator->trans($tenant->getName());
        }
        return $choices;
    }

    public function getProvider(bool $injectRepository = true): TenantProvider
    {
        if ($injectRepository) {
            $this->tenantProvider->injectRepository($this->tenantRepository);
        }
        return $this->tenantProvider;
    }
}