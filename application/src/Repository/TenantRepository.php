<?php

namespace App\Repository;

use App\Dto\TenantDto;
use App\Entity\Tenant;
use App\Entity\User;
use App\Provider\TenantProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tenant>
 */
class TenantRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly TenantProvider $tenantProvider
    ) {
        parent::__construct($registry, Tenant::class);
    }

    public function findFiltered(?string $domain = null, ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($domain !== null) {
            $tenantsWithDomain = $this->tenantProvider->getByDomain($domain);
            $pluckedMcHostnames = array_map(
                static fn(TenantDto $tenantDto) => $tenantDto->getHost(),
                $tenantsWithDomain
            );
            $qb->andWhere($qb->expr()->in('t.mcTenantId', ':domainList'))
                ->setParameter('domainList', $pluckedMcHostnames);
        }

        if ($user !== null) {
            $qb->leftJoin('t.users', 'u')
                ->andWhere($qb->expr()->in('u', ':user'))->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}
