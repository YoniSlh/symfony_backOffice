<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'app:import-products')]
class ImportProductsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private Filesystem $filesystem;

    public function __construct(EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importe des produits depuis un fichier CSV')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Chemin du fichier CSV à importer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $csvPath = $input->getArgument('filePath');

        if (!$this->filesystem->exists($csvPath)) {
            $output->writeln('<error>Le fichier CSV n\'existe pas.</error>');
            return Command::FAILURE;
        }

        $file = fopen($csvPath, 'r');

        if ($file === false) {
            $output->writeln('<error>Impossible d\'ouvrir le fichier.</error>');
            return Command::FAILURE;
        }

        fgetcsv($file);

        while (($data = fgetcsv($file, 0, ';')) !== false) {
            if (count($data) < 3) {
                continue;
            }

            $product = new Product();
            $product->setName($data[0]);
            $product->setDescription($data[1]);
            $product->setPrice((float) $data[2]);

            $this->entityManager->persist($product);
        }

        fclose($file);
        $this->entityManager->flush();

        $output->writeln('<info>Importation terminée.</info>');

        return Command::SUCCESS;
    }
}
