<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Tenant;
use App\Entity\TenantUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TenantUserFixtures extends Fixture implements DependentFixtureInterface
{
    public const string PREFIX = 'tenantuser_';

    private const array DEFINITIONS = [
        [
            'user' => 'user1',
            'tenant' => 'tenant_r',
            'roles' => ['TENANT_ROLE_MANAGER'],
        ],
        [
            'user' => 'user2',
            'tenant' => 'tenant_p',
            'roles' => ['TENANT_ROLE_MANAGER'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEFINITIONS as $key => $data) {
            $tenant = $this->getReference(TenantFixtures::PREFIX.$data['tenant'], Tenant::class);
            $user = $this->getReference(UserFixtures::PREFIX.$data['user'], User::class);
            $tenantUser = $this->createTenantUser($tenant, $user, $data);
            $manager->persist($tenantUser);
            $this->addReference(self::PREFIX . $key, $tenantUser);
        }
        $manager->flush();
    }

    private function createTenantUser(Tenant $tenant, User $user, array $data): TenantUser
    {
        $tu = new TenantUser();
        $tu->setTenant($tenant);
        $tu->setUser($user);
        $tu->setRoles($data['roles']);
        return $tu;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TenantFixtures::class,
        ];
    }
}
