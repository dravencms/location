<?php declare(strict_types=1);

namespace Dravencms\Location\Console;

use Dravencms\Location\Setup\DefaultDataInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

final class SetupCommand extends Command
{
    public function __construct(private DefaultDataInstaller $installer) { parent::__construct('location:setup'); }

    protected function configure(): void
    {
        $this->setDescription('Add missing location defaults without replacing existing data')
            ->addOption('example-address', null, InputOption::VALUE_NONE, 'Add the example Olomouc, 77900, Brněnská 54 address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->installer->install((bool) $input->getOption('example-address'));
        $output->writeln('Missing location defaults installed; existing records retained.');
        return Command::SUCCESS;
    }
}
