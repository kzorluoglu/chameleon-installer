<?php

namespace KZorluoglu\Installer\Console;

use KZorluoglu\Installer\Console\Services\DatabaseConnectionService;
use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create',
    description: 'Creates a new chameleon shop.',
    aliases: ['create'],
    hidden: false
)]
class CreateCommand extends Command
{

    private const REQUIRED_PHP_EXTENSION = ['curl', 'mbstring', 'mysqli', 'pdo_mysql', 'zip', 'tidy'];
    private const PHP_8_1_VERSION_ID = 80100;
    private const GITHUB_REPO_URL = 'https://github.com/chameleon-system/chameleon-system';
    private const CMS_DEFAULT_LANGUAGE_ID = 24;
    private const LANGUAGE = 'de';
    private DatabaseConnectionService $databaseConnectionService;
    private string $directoryFullPath;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a Chameleon Shop.')
            ->addArgument('directory', InputArgument::REQUIRED, 'Installation Directory')
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a chameleon: chameleon create /home/www/project ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Chameleon Creating Tool');

        $io->section('Requirement Checks started');
        if (false === $this->checkRequirements($io)) {
            return Command::FAILURE;
        }

        $directory = $input->getArgument('directory');

        $io->section('Git cloning');
        if (false === $this->runGitCloneAndSetDirectoryFullPath($directory)) {
            return Command::FAILURE;
        }

        $io->section('Composer install');
        $composer = $this->findComposer();
        if (false === $this->runComposerInstall($composer, $directory, $io)) {
            return Command::FAILURE;
        }

        $io->section('Database Import');
        $this->databaseConnectionService = new DatabaseConnectionService(
            $this->directoryFullPath.'/app/config/parameters.yml'
        );
        $importDemoData = $io->confirm('Do you want to import demo data?', false);
        $databaseDump = $this->getDatabaseDump($importDemoData);
        if (false === $this->importDatabase($databaseDump, $io)) {
            return Command::FAILURE;
        }

        $io->section('CMS Admin creating');

        $username = $io->ask('Enter the admin username', 'admin');
        $password = $io->askHidden('Enter the admin password (input will be hidden)');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $email = $io->ask('Enter the admin E-Mail', 'example@domain.tld');
        $name = $io->ask('Enter the admin Name', 'John Doe');

        if (false === $this->createUser(
                $this->databaseConnectionService->createConnection(),
                $username,
                $hashedPassword,
                $email,
                $name,
                $this->generateUUID(),
                $io
            )) {
            $output->writeln('Failed to create admin user.');

            return Command::FAILURE;
        }
        $io->section('Awesome! Your Chameleon Shop is ready!');

        if ($importDemoData) {
            $io->writeln(
                'Please log in at [your_shop_url]/cms and update your portal\'s domain under "Portals" -> "Domain".'
            );
        } else {
            $io->writeln(
                'Please log in at [your_shop_url]/cms and add your portal domain under "Portals" -> "Domain".'
            );
        }

        return Command::SUCCESS;
    }

    protected function checkRequirements(SymfonyStyle $io): bool
    {
        $outputList = [];

        // Check PHP 8.1 version
        if (PHP_VERSION_ID < self::PHP_8_1_VERSION_ID) {
            $io->caution('PHP version 8.1 or higher is required. Current version: '.PHP_VERSION);

            return false;
        }

        // Check required extensions
        foreach (self::REQUIRED_PHP_EXTENSION as $ext) {
            if (false === extension_loaded($ext)) {
                $io->caution('Required PHP extension missing: '.$ext);

                return false;
            }
            $outputList[] = 'PHP Extension Check OK: '.$ext;
        }

        $io->listing($outputList);

        return true;
    }

    private function runComposerInstall(
        string $composerBinaryPath,
        string $directory,
        SymfonyStyle $io
    ): bool {
        $command = sprintf('cd %s && %s install', $directory, $composerBinaryPath);
        exec($command, $outputLines, $returnVar);
        $io->write($outputLines);

        if ($returnVar !== 0) {
            $io->caution($returnVar);

            return false;
        }

        return true;
    }

    private function runGitCloneAndSetDirectoryFullPath(string $directory): void
    {
        exec('git clone '.self::GITHUB_REPO_URL.' '.$directory);
        exec('cd '.$directory.' && pwd', $output);

        $this->setDirectoryFullPath($output[0]); // returns the full path of the cloned directory
    }

    private function getDatabaseDump(bool $importDemoData): string
    {
        if (true === $importDemoData) {
            return __DIR__.'/../databasedumps/shop-database-with-demo-data.sql';
        }

        return __DIR__.'/../databasedumps/shop-database.sql';
    }

    private function importDatabase(string $filePath, SymfonyStyle $io): bool
    {
        $pdo = $this->databaseConnectionService->createConnection();
        $handle = fopen($filePath, 'r');

        $io->writeln("Executing queries");

        $query = '';
        while (!feof($handle)) {
            $line = fgets($handle);
            $query .= $line;

            if (str_ends_with(trim($line), ';')) {
                try {
                    $pdo->exec($query);
                } catch (\PDOException $e) {
                    $io->error("Error executing query: ".$e->getMessage());

                    fclose($handle);

                    return false;
                }

                $query = ''; // Reset query to avoid memory buildup
            }
        }

        fclose($handle);
        $io->success("Database import completed.");

        return true;
    }

    private function setDirectoryFullPath(string $directoryFullPath): void
    {
        $this->directoryFullPath = $directoryFullPath;
    }

    private function findComposer(): string
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" composer.phar';
        }

        return 'composer';
    }

    private function generateUUID(): string
    {
        $chars = bin2hex(random_bytes(16));
        $uuid = substr($chars, 0, 8).'-';
        $uuid .= substr($chars, 8, 4).'-';
        $uuid .= substr($chars, 12, 4).'-';
        $uuid .= substr($chars, 16, 4).'-';
        $uuid .= substr($chars, 20, 12);

        return $uuid;
    }

    private function createUser(
        PDO $databaseConnectionService,
        string $username,
        string $hashedPassword,
        string $email,
        string $name,
        string $uuid,
        SymfonyStyle $io,
    ): bool {
        try {
            $databaseConnectionService->beginTransaction();


            $statement = $databaseConnectionService->prepare(
                'INSERT INTO `cms_user` 
    (`id`, `cmsident`, `email`, `login`,  `crypted_pw`, `name`, `cms_language_id`, `languages`,
     `images`, `cms_current_edit_language`,
     `allow_cms_login`, `task_show_count`, `is_system`,
     `show_as_rights_template`, `user_tbl_conf_hidden`,
     `cms_workflow_transaction_id`) 
     VALUES
            (:id, :cmsident, :email, :login, :crypted_pw, :name, :cms_language_id, :languages,
             :images, :cms_current_edit_language,
             :allow_cms_login, :task_show_count, :is_system,
             :show_as_rights_template, :user_tbl_conf_hidden,
             :cms_workflow_transaction_id)'
            );


            $statement->bindValue(':id', $uuid);
            $statement->bindValue(':cmsident', random_int(1, 999999999));
            $statement->bindValue(':email', $email);
            $statement->bindValue(':login', $username);
            $statement->bindValue(':crypted_pw', $hashedPassword);
            $statement->bindValue(':name', $name);
            $statement->bindValue(':cms_language_id', self::CMS_DEFAULT_LANGUAGE_ID, PDO::PARAM_INT);
            $statement->bindValue(':languages', self::LANGUAGE);
            $statement->bindValue(':images', true, PDO::PARAM_INT);
            $statement->bindValue(':cms_current_edit_language', self::LANGUAGE);
            $statement->bindValue(':allow_cms_login', true, PDO::PARAM_INT);
            $statement->bindValue(':task_show_count', 5, PDO::PARAM_INT);
            $statement->bindValue(':is_system', '1');
            $statement->bindValue(':show_as_rights_template', '1');
            $statement->bindValue(':user_tbl_conf_hidden', true, PDO::PARAM_INT);
            $statement->bindValue(':cms_workflow_transaction_id', 1, PDO::PARAM_INT);

            $statement->execute();

            $databaseConnectionService->commit();
            $io->write("CMS Admin ${username} created");

            return true;
        } catch (\PDOException $e) {
            $databaseConnectionService->rollBack();
            $io->error("Error Admin user creating: ".$e->getMessage());

            // You might want to log this error
            return false;
        }
    }

}