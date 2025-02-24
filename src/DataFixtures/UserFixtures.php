<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const string PREFIX = 'user_';

    private const array DEFINITIONS = [
        'admin1' => [
            'password' => 'secret',
            'roles' => ['ROLE_ADMIN'],
        ],
        'user1' => [
            'password' => 'secret',
            'roles' => ['ROLE_USER'],
        ],
        'user2' => [
            'password' => 'secret',
            'roles' => ['ROLE_USER'],
        ],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::DEFINITIONS as $name => $data) {
            $user = $this->createUser($name, $data);
            $manager->persist($user);
            $this->addReference(self::PREFIX . $name, $user);
        }
        $manager->flush();
    }

    private function createUser(string $username, array $data): User
    {
        $user = new User();
        $user->setUsername($username);
        $password = $this->hasher->hashPassword($user, $data['password']);
        $user->setPassword($password);
        return $user;
    }
}
