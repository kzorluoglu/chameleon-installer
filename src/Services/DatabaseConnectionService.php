<?php

namespace KZorluoglu\Installer\Console\Services;

use Symfony\Component\Yaml\Yaml;

class DatabaseConnectionService
{

    private string $configFilePath;

    public function __construct(string $configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

    public function createConnection(): \PDO
    {
        $parameters = Yaml::parse(file_get_contents($this->configFilePath));
        $parameters = $parameters['parameters'];

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s',
            $parameters['database_host'],
            $parameters['database_name']
        );

        return new \PDO($dsn, $parameters['database_user'], $parameters['database_password']);
    }
}