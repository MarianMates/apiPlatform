<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:hash-password', description: 'Hash a password')]
final class PasswordHasher extends Command
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('password', InputArgument::REQUIRED, 'The password to hash', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = $input->getArgument('password');
        $hashedPassword = $this->passwordHasher->hashPassword(
            new User(), $password
        );
        $output->writeln($hashedPassword);

        return Command::SUCCESS;
    }
}