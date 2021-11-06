<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Routing\RouteContext;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;

class GetNewsArticleHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $articlesRepository,
    ){}

    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $articleId = $routeContext->getRoute()?->getArgument('articleId');

        $notFoundResponse = [
            'found'       => false,
            'news-artice' => null,
        ];
        if (!$articleId) {
            return $response->withJson($notFoundResponse, 404);
        }
        
        try {
            $articleId = ArticleId::fromInt((int)$articleId);

            $article = $this->articlesRepository->getNewsArticleForId($articleId);

            if (!$article) {
                return $response->withJson($notFoundResponse, 404);
            }

            return $response->withJson([
                'found'        => true,
                'news-article' => $article->asArray(),
            ]);
        } catch (InvalidArgumentException) {
            return $response->withJson($notFoundResponse, 400);
        }
    }
}
