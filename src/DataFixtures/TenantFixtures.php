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
            'domains' => ['localhost', 'r.localhost'],
            'rconPort' => 25576,
            'rconPassword' => 'ser',
            'registrationPassword' => 'secret1',
            'bedrockPort' => 19133,
            'javaPort' => 25566,
        ],
        'tenant_p' => [
            'name' => 'S2C',
            'mcHost' => 'minecraft-p',
            'domains' => ['localhost', 'p.localhost'],
            'rconPort' => 25577,
            'rconPassword' => 'sep',
            'registrationPassword' => 'secret2',
            'bedrockPort' => 19134,
            'javaPort' => 25567,
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
        $tenant->setMcHost($data['mcHost']);
        $tenant->setDomains($data['domains']);
        $tenant->setRconPort($data['rconPort']);
        $tenant->setRconPassword($data['rconPassword']);
        $tenant->setRegistrationPassword($data['registrationPassword']);
        $tenant->setBedrockPort($data['bedrockPort']);
        $tenant->setJavaPort($data['javaPort']);
        return $tenant;
    }
}
