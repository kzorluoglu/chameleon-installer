<?php

namespace KZorluoglu\Installer\Console\Factory;

use KZorluoglu\Installer\Console\Config\YamlDatabaseConfiguration;
use KZorluoglu\Installer\Console\Services\DatabaseConnectionService;

class DatabaseConnectionServiceFactory
{
    public static function createService(string $configFilePath): DatabaseConnectionService
    {
        // Ensure the configuration file exists
        if (!file_exists($configFilePath)) {
            // Handle the error appropriately
            throw new \RuntimeException("Configuration file not found at: {$configFilePath}");
        }

        $configuration = new YamlDatabaseConfiguration($configFilePath);

        return new DatabaseConnectionService($configuration);
    }
}