<?php
declare(strict_types=1);

use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;
use Trimethylpentan\NewsArticles\Handler\CreateNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\EditNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\GetNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\ListNewsArticlesHandler;

return function (App $app) {
//    $app->options('/{routes:.*}', function (Request $request, Response $response) {
//        // CORS Pre-Flight OPTIONS Request Handler
//        return $response;
//    });
    $app->group('/news-article', function (Group $group) {
        $group->get('/list', ListNewsArticlesHandler::class);
        $group->get('/{articleId}', GetNewsArticleHandler::class);
        $group->post('/create', CreateNewsArticleHandler::class);
        $group->post('/edit', EditNewsArticleHandler::class);
    });
};
