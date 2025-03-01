<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserWithTenant(string $username, int $tenantId): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.tenants', 't')
            ->andWhere('u.username = :username')
            ->andWhere('(u.roles LIKE :roleSuperAdmin OR t.id = :tenantId)')
            ->setParameter('username', $username)
            ->setParameter('tenantId', $tenantId)
            ->setParameter('roleSuperAdmin', sprintf('%%%s%%',User::ROLE_SUPER_ADMIN))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByIdentity(string $username1, ?string $username2 = null): ?User
    {
        $usernames = array_filter([$username1, $username2]);
        if (count($usernames) === 0) {
            return null;
        }
        return $this->createQueryBuilder('u')
            ->andWhere('(u.username IN (:usernames) OR u.email IN (:usernames))')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    //    /**S
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
