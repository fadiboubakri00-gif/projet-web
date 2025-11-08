<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    private $em;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:create-admin') // <<< THIS IS THE FIX
            ->setDescription('Create a new admin user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username of the admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Password of the admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $admin = new Admin();
        $admin->setUsername($username);
        $hashed = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashed);
        $admin->setRoles(['ROLE_ADMIN']);

        $this->em->persist($admin);
        $this->em->flush();

        $output->writeln("✅ Admin user '$username' created successfully.");

        return Command::SUCCESS;
    }
}
