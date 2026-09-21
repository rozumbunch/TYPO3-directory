<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Command;

use Rozumbunch\Directory\Service\DummyDataSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand;
use TYPO3\CMS\Core\Core\Bootstrap;

#[AsCommand('directory:seed-dummy', 'Legt Dummy-Daten für Personen, Organisationen und Standorte an.')]
#[AsNonSchedulableCommand]
final class SeedDummyDataCommand extends Command
{
    public function __construct(private readonly DummyDataSeeder $dummyDataSeeder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'pid',
                null,
                InputOption::VALUE_REQUIRED,
                'SysOrdner-PID. Standard: Site-Setting directory.storagePid.',
                '0'
            )
            ->addOption(
                'replace',
                null,
                InputOption::VALUE_NONE,
                'Vorhandene Dummy-Datensätze (identifier dummy-*) zuerst löschen.'
            )
            ->addOption(
                'wipe',
                null,
                InputOption::VALUE_NONE,
                'Alle Directory-Datensätze auf der Storage-PID löschen, dann neu anlegen.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        Bootstrap::initializeBackendAuthentication();

        try {
            $pid = $this->dummyDataSeeder->resolvePid((int)$input->getOption('pid'));
            $messages = $this->dummyDataSeeder->seed(
                $pid,
                (bool)$input->getOption('wipe'),
                (bool)$input->getOption('replace')
            );
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success('Dummy-Daten auf PID ' . $pid . ' angelegt.');
        foreach ($messages as $message) {
            $io->writeln(' - ' . $message);
        }

        return Command::SUCCESS;
    }
}
