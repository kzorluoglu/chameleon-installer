<?php

namespace KZorluoglu\Installer\Console\Tests\Services;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use KZorluoglu\Installer\Console\Services\DatabaseConnectionService;

class DatabaseConnectionServiceTest extends TestCase
{
    public function testCreateConnectionValidConfig()
    {
        $configFilePath = '/path/does/not/need/to/exist/for/test.yml';
        $service = $this->createMock(DatabaseConnectionService::class);
        $service->setConfigFilePath($configFilePath);
        $service->method('createConnection')->willReturn(new \PDO('sqlite::memory:'));
        $connection = $service->createConnection();
        $this->assertInstanceOf(\PDO::class, $connection);
    }


    /**
     * @throws Exception
     */
    public function testCreateConnectionInvalidConfig()
    {
        $this->expectException(\LogicException::class);
        $configFilePath = '/path/that/does/not/exist.yml';
        $service = new DatabaseConnectionService();
        $service->setConfigFilePath($configFilePath);
        $service->createConnection();
    }
}