<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Tenant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TenantFixtures extends Fixture
{
    public const string PREFIX = 'tenant_';

    private const array DEFINITIONS = [
        [
            'name' => 'Serwer typu R',
            'mcHost' => 'minecraft-r',
            'registrationPassword' => 'secret1',
        ],
        [
            'name' => 'S2C',
            'mcHost' => 'minecraft-p',
            'registrationPassword' => 'secret2',
        ],
        [
            'name' => 'Autostrada przez Zacisze',
            'mcHost' => 'minecraft-k',
            'registrationPassword' => 'secret3',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEFINITIONS as $data) {
            $tenant = $this->createTenant($data);
            $manager->persist($tenant);
            $this->addReference(self::PREFIX . $tenant->getName(), $tenant);
        }
        $manager->flush();
    }

    private function createTenant(array $data): Tenant
    {
        $tenant = new Tenant();
        $tenant->setName($data['name']);
        $tenant->setMcTenantId($data['mcHost']);
        $tenant->setRegistrationPassword($data['registrationPassword']);
        return $tenant;
    }
}
