<?php
declare(strict_types=1);

use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;
use Trimethylpentan\NewsArticles\Handler\CreateNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\DeleteNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\EditNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\GetNewsArticleHandler;
use Trimethylpentan\NewsArticles\Handler\ListNewsArticlesHandler;

/*
 * Hier werden die Routen definiert. In einer Gruppe können mehrere Routen zusammengefasst werden
 * Alle Routen in der Gruppe sind childroutes der Gruppe.
 * Als Callable sind die jeweiligen Handler angegeben, die dann vom Framework aufgerufen werden
 */
return function (App $app) {
    $app->group('/news-article', function (Group $group) {
        $group->get('/list', ListNewsArticlesHandler::class);
        $group->get('/{articleId}', GetNewsArticleHandler::class);
        $group->post('/create', CreateNewsArticleHandler::class);
        $group->post('/edit', EditNewsArticleHandler::class);
        $group->post('/delete', DeleteNewsArticleHandler::class);
    });
};
