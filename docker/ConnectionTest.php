<?php

namespace docker;

use App\Tests\Integration\Fixtures\Database as DB;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[TestDox('Tests Database objects')]
class ConnectionTest extends KernelTestCase
{
    #[TestDox('It can connect using the raw DB connection values')]
    public function testPdoCanConnectToTheDatabase(): void
    {
        $connection = DB::getDbalConnection();

        $result = $connection->executeQuery('SELECT 1')->fetchOne();

        $this->assertEquals(1, $result);
    }

    #[TestDox('It can use DATABASE_URL to connect to the database')]
    public function testDoctrineCanConnectToTheDatabase(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $connection = $container->get('doctrine.dbal.default_connection');

        $result = $connection->executeQuery('SELECT 1')->fetchOne();

        $this->assertEquals(1, $result);
    }
}
