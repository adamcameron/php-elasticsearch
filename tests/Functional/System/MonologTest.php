<?php

namespace App\Tests\Functional\System;

use Monolog\Handler\TestHandler;
use Monolog\Level;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MonologTest extends KernelTestCase
{
    #[TestDox('It logs messages to the messaging log file')]
    public function testMessagingLog()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();

        $logger = $container->get('monolog.logger.messaging');

        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);

        $uniqueMessage = 'Test message ' . uniqid('', true);
        $logger->info($uniqueMessage);

        $this->assertTrue($testHandler->hasInfoRecords());
        $this->assertTrue($testHandler->hasRecord($uniqueMessage, Level::Info));

        $records = $testHandler->getRecords();
        $infoRecords = array_filter($records, fn($r) => $r['level'] === Level::Info->value);
        $lastInfoRecord = end($infoRecords);
        $this->assertEquals($uniqueMessage, $lastInfoRecord['message']);
    }
}
