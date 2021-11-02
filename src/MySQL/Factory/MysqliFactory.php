<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\MySQL\Factory;

use mysqli;
use Psr\Container\ContainerInterface;

class MysqliFactory
{
    public function __invoke(ContainerInterface $container): mysqli
    {
        $hostname = $container->get('mysql-hostname');
        $username = $container->get('mysql-username');
        $password = $container->get('mysql-password');
        $database = $container->get('mysql-database');
        $port     = (int)$container->get('mysql-port');

        return new mysqli($hostname, $username, $password, $database, $port);
    }
}
