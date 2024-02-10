<?php

namespace KZorluoglu\Installer\Console\Services;

use KZorluoglu\Installer\Console\Interfaces\DatabaseConfigurationInterface;
use PDO;

class DatabaseConnectionService
{

    private DatabaseConfigurationInterface $config;


    public function __construct(DatabaseConfigurationInterface $config)
    {
        $this->config = $config;
    }

    public function createConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s',
            $this->config->getHost(),
            $this->config->getDatabaseName()
        );

        return new PDO($dsn, $this->config->getUser(), $this->config->getPassword());
    }
}