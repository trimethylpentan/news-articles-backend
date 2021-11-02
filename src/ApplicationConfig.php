<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles;

use DI\Definition\Source\DefinitionArray;
use mysqli;
use Trimethylpentan\NewsArticles\MySQL\Factory\MysqliFactory;
use function DI\env;
use function DI\factory;

class ApplicationConfig extends DefinitionArray
{
    public function __construct()
    {
        parent::__construct([
            mysqli::class    => factory(MysqliFactory::class),
            'mysql-hostname' => env('MYSQL_HOSTNAME'),
            'mysql-username' => env('MYSQL_USERNAME'),
            'mysql-password' => env('MYSQL_PASSWORD'),
            'mysql-database' => env('MYSQL_DATABASE'),
            'mysql-port'     => env('MYSQL_PORT'),
        ]);
    }
}
