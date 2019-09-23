<?php
declare(strict_types = 1);

namespace App\Command;

use App\Helper\DbConnection;
use App\Helper\File;
use App\Service\DatabaseService;
use App\Service\UserService;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;
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

    protected function configure(): void
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

        $params = [
            'host' => $this->dbHost,
            'port' => 3306,
            'user' => $this->dbUser,
            'password' => $this->dbPassword,
            'driver' => 'pdo_mysql'
        ];
        try {
            $conn = (new DbConnection)->getConnection($params);
            if ($this->createTable) {
                (new DatabaseService($conn))->createTable();
            }
            foreach ($records as $record) {
                (new UserService($conn))->addUser($record);
            }
            $total_users = count($records);
            $io->success('total number of records added: ' . $total_users);
            exit(0);
        } catch (ConnectionException $connE) {
            $io->error("Could not access the DB" . $connE->getMessage());
        } catch (TableNotFoundException $e) {
            $io->error("Database/Table does not exists");
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }
        exit(128);
    }

    /**
     * @param array $options
     */
    private function processOptions(array $options = []): void
    {
        $this->file = $options['file'] ?? '';
        $this->dryRun = $options['dry-run'] ?? true;
        $this->createTable = $options['create-table'] ?? true;
        $this->dbUser = $options['db-user'] ?? '';
        $this->dbPassword = $options['db-password'] ?? '';
        $this->dbHost = $options['db-host'] ?? '';
    }
}
