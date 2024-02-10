<?php

namespace KZorluoglu\Installer\Console\Config;

use KZorluoglu\Installer\Console\Interfaces\DatabaseConfigurationInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDatabaseConfiguration implements DatabaseConfigurationInterface
{
    private array $parameters;

    public function __construct(string $configFilePath)
    {
        $this->parameters = Yaml::parse(file_get_contents($configFilePath))['parameters'];
    }

    public function getHost(): string
    {
        return $this->parameters['database_host'];
    }

    public function getDatabaseName(): string
    {
        return $this->parameters['database_name'];
    }

    public function getUser(): string
    {
        return $this->parameters['database_user'];
    }

    public function getPassword(): string
    {
        return $this->parameters['database_password'];
    }
}