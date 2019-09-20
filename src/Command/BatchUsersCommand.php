<?php
declare(strict_types = 1);

namespace App\Command;

use App\Helper\File;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BatchUsersCommand extends Command
{
    /** @var string $defaultName */
    protected static $defaultName = 'batch-users';

    /** @var string $file */
    private $file = '';

    /** @var bool $createTable */
    private $createTable = false;

    /** @var bool $dryRun */
    private $dryRun = false;

    /** @var string $dbUser */
    private $dbUser = '';

    /** @var string $dbPassword */
    private $dbPassword = '';

    /** @var string $dbHost */
    private $dbHost = '';

    /** @var string $description */
    private  $description = <<<DESC
PHP script, that is executed from the command line, which accepts a CSV file as input
(see command line directives below) and processes the CSV file. The parsed file data is to be
inserted into a MySQL database.
DESC;

    protected function configure()
    {
        $this
            ->setDescription($this->description)
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'name of the CSV to be parsed')
            ->addOption('create-table', 'c', InputOption::VALUE_OPTIONAL, 'MySQL users table to be built')
            ->addOption('dry-run', 'd', InputOption::VALUE_OPTIONAL, 'used with the --file directive in case we want to run the
script but not insert into the DB', false)
            ->addOption('db-user', 'u', InputOption::VALUE_OPTIONAL, 'MySQL username')
            ->addOption('db-password', 'p', InputOption::VALUE_OPTIONAL, 'MySQL password')
            ->addOption('db-host', null, InputOption::VALUE_OPTIONAL, 'MySQL host')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Batch Users');

        $this->processOptions($input->getOptions());

        try {
            list($errors, $records) = (new File)->parseCsv($this->file);
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            exit(128);
        };
        if (count($errors)) {
            $io->listing($errors);
        }
        if ($this->dryRun) {
            $io->title('Dry run, DB operations are not executed');
            exit(0);
        }
    }

    /**
     * @param array $options
     */
    private function processOptions(array $options = [])
    {
        $this->file = $options['file'] ?? '';
        $this->dryRun = $options['dry-run'] ?? true;
        $this->dryRun = $options['create-table'] ?? '';
        $this->dryRun = $options['db-user'] ?? '';
        $this->dryRun = $options['db-password'] ?? '';
        $this->dryRun = $options['db-host'] ?? '';
    }
}