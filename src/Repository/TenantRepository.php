<?php

namespace App\Repository;

use App\Entity\Tenant;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tenant>
 */
class TenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tenant::class);
    }

    public function findFiltered(?string $domain = null, ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($domain !== null) {
            $qb->andWhere('t.domains LIKE :domain')
                ->setParameter('domain', sprintf('%%"%s"%%', $domain));
        }

        if ($user !== null) {
            $qb->leftJoin('t.user', 'u')
                ->andWhere('u = :user')->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}
