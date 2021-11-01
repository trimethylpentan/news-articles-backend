<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;
use Trimethylpentan\NewsArticles\Handler\CreateNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\ListNewsArticlesHandler;

return function (App $app) {
//    $app->options('/{routes:.*}', function (Request $request, Response $response) {
//        // CORS Pre-Flight OPTIONS Request Handler
//        return $response;
//    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/news-article', function (Group $group) {
        $group->get('/list', ListNewsArticlesHandler::class);
        $group->post('/create', CreateNewsArticleHandler::class);
    });
};
