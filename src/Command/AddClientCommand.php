<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class AddClientCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:add-client')
            ->setDescription('Ajoute un client')
            ->addArgument('firstname', InputArgument::REQUIRED, 'Prénom')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Nom')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('role', InputArgument::REQUIRED, 'Rôle');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $existingClient = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => $email]);
        if ($existingClient) {
            $output->writeln('L\'email est déjà utilisé par un autre client.');
            return Command::FAILURE;
        }

        $client = new Client();
        $client->setFirstname($firstname);
        $client->setLastname($lastname);
        $client->setEmail($email);
        $client->setRole($role);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $output->writeln('Client ajouté avec succès.');

        return Command::SUCCESS;
    }
}
