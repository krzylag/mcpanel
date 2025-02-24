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
        'tenant_r' => [
            'host' => 'minecraft-r',
            'domain' => 'localhost',
            'rconPort' => 25576,
            'rconPassword' => 'secretr',
            'registrationPassword' => 'secret1',
            'bedrockPort' => 19133,
            'javaPort' => 25566,
        ],
        'tenant_p' => [
            'host' => 'minecraft-p',
            'domain' => 'localhost',
            'rconPort' => 25577,
            'rconPassword' => 'secretp',
            'registrationPassword' => 'secret2',
            'bedrockPort' => 19134,
            'javaPort' => 25567,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEFINITIONS as $name => $data) {
            $tenant = $this->createTenant($name, $data);
            $manager->persist($tenant);
            $this->addReference(self::PREFIX . $name, $tenant);
        }
        $manager->flush();
    }

    private function createTenant(string $name, array $data): Tenant
    {
        $tenant = new Tenant();
        $tenant->setName($name);
        $tenant->setHost($data['host']);
        $tenant->setExternalDomain($data['domain']);
        $tenant->setRconPort($data['rconPort']);
        $tenant->setRconPassword($data['rconPassword']);
        $tenant->setRegistrationPassword($data['registrationPassword']);
        $tenant->setBedrockPort($data['bedrockPort']);
        $tenant->setJavaPort($data['javaPort']);
        return $tenant;
    }
}
