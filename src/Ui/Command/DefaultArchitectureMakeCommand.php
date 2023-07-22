<?php
declare(strict_types=1);

namespace App\Bundles\AppMakerBundle\Ui\Command;

use App\Bundles\AppMakerBundle\Infrastructure\Architecture\Creator\ArchitectureCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:default_architecture',
    description: 'Make default architecture by doctrine entity',
    aliases: ['mda']
)]
class DefaultArchitectureMakeCommand extends Command
{
    public function __construct(
        private readonly ArchitectureCreator $creatorByDoctrineEntityClass
    ) {
        parent::__construct($this->getName());
    }

    protected function configure(): void
    {
        $this->addArgument(
            'entityClassName',
            InputArgument::REQUIRED,
            'Doctrine entity full class name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->creatorByDoctrineEntityClass->create(
            $input->getArgument('entityClassName')
        );

        return Command::SUCCESS;
    }
}
