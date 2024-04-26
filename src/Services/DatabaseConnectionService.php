<?php

namespace KZorluoglu\Installer\Console\Services;

use Symfony\Component\Yaml\Yaml;

class DatabaseConnectionService
{

    private ?string $configFilePath = null;

    public function setConfigFilePath(string $configFilePath): void
    {
        $this->configFilePath = $configFilePath;
    }

    public function createConnection(): \PDO
    {
        if (!$this->configFilePath || !file_exists($this->configFilePath)) {
            throw new \LogicException("Configuration file path is not set or does not exist.");
        }

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