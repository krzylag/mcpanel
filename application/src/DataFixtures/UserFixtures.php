<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Tenant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const string PREFIX = 'user_';

    private const array DEFINITIONS = [
        [
            'name' => 'admin',
            'password' => 'secret',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'tenants' => [],
        ],
        [
            'name' => 'adminr',
            'password' => 'secretr',
            'roles' => ['ROLE_ADMIN'],
            'tenants' => [
                'Serwer typu R'
            ],
        ],
        [
            'name' => 'adminp',
            'password' => 'secretp',
            'roles' => ['ROLE_ADMIN'],
            'tenants' => [
                'S2C'
            ],
        ],
        [
            'name' => 'Ciemnyzenek',
            'roles' => ['ROLE_PLAYER'],
            'tenants' => [
                'Serwer typu R',
                'S2C',
                'Autostrada przez Zacisze',
            ],
        ],
        [
            'name' => 'GraczR',
            'roles' => ['ROLE_PLAYER'],
            'tenants' => [
                'Serwer typu R',
            ],
        ],
        [
            'name' => 'GraczP',
            'roles' => ['ROLE_PLAYER'],
            'tenants' => [
                'S2C',
            ],
        ],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEFINITIONS as $data) {
            $user = $this->createUser($data);
            $manager->persist($user);
            foreach ($data['tenants'] as $tenantName) {
                $tenant = $this->getReference(TenantFixtures::PREFIX . $tenantName, Tenant::class);
                $user->addTenant($tenant);
            }
            $this->addReference(self::PREFIX . $user->getUsername(), $user);
        }
        $manager->flush();
    }

    private function createUser(array $data): User
    {
        $user = new User();
        $user->setUsername($data['name']);
        if (isset($data['password'])) {
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
        }
        $user->setRoles($data['roles'] ?? []);
        return $user;
    }

    public function getDependencies(): array
    {
        return [
            TenantFixtures::class,
        ];
    }
}
