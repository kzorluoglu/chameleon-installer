<?php

namespace KZorluoglu\Installer\Console\Interfaces;

interface DatabaseConfigurationInterface
{
    public function getHost(): string;

    public function getDatabaseName(): string;

    public function getUser(): string;

    public function getPassword(): string;
}