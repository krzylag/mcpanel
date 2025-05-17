<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-superadmin',
    description: 'Create user with superadmin role',
)]
class CreateSuperadminCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'New user name')
            ->addArgument('password', InputArgument::OPTIONAL, 'Plaintext password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        if ($password === null) {
            $password = bin2hex(random_bytes(8));
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles([User::ROLE_SUPER_ADMIN]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('username: %s', $username));
        $io->success(sprintf('password: %s', $password));

        return Command::SUCCESS;
    }
}
