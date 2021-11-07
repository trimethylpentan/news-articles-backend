<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;

class DeleteNewsArticleHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $articlesRepository,
    ) {}

    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface
    {
        $articleId = ArticleId::fromInt(
            (int)json_decode($request->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR)['articleId']
        );
        
        try {
            $this->articlesRepository->deleteNewsArticle($articleId);
            
            return $response->withJson([
               'success' => true,
            ], 200);
        } catch (MysqliException $exception) {
            // Den Fehler nur auf dev ausgeben, damit im Live-System keine Details zu Fehlern angezeigt werden
            // @codeCoverageIgnoreStart
            if (APP_ENV === 'development') {
                return $response->withJson([
                    'success' => false,
                    'error'   => $exception->getMessage(),
                ], 500);
            }
            // @codeCoverageIgnoreEnd

            return $response->withJson([
                'success' => false,
                'error'   => sprintf('Konnte den News-Artikel mit der id "%s" nicht löschen', $articleId->asInt()),
            ], 500);
        }
    }
}
