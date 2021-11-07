<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\MySQL\Factory;

use mysqli;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Trimethylpentan\NewsArticles\MySQL\Factory\MysqliFactory
 */
class MysqliFactoryTest extends TestCase
{
    public function testCanCreateMysqli(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new MysqliFactory();
        
        $container->expects($this->exactly(5))->method('get')->withConsecutive(
            ['mysql-hostname'],
            ['mysql-username'],
            ['mysql-password'],
            ['mysql-database'],
            ['mysql-port'],
        )->willReturn('mariadb', 'testing', '1234', 'news_articles', '3306');
        
        $mysqli = $factory($container);
        $this->assertInstanceOf(mysqli::class, $mysqli);
    }
}
