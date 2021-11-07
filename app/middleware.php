<?php
declare(strict_types=1);

use Slim\App;
use Trimethylpentan\NewsArticles\Middleware\CorsMiddleware;

/*
 * Hier werden die Middlewares konfiguriert, die durch das Framework vor dem Handler aufgerufen werden
 */
return function (App $app) {
    $app->add(CorsMiddleware::class);
};
