<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

class EditNewsArticleHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $articlesRepository,
    ){}

    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);
        $articleId = ArticleId::fromInt((int)$body['id']);
        $title     = Title::fromString($body['title']);
        $text      = Text::fromString($body['text']);

        $newsArticle = $this->articlesRepository->getNewsArticleForId($articleId);

        if ($newsArticle === null) {
            return $response->withJson([
                'success' => false,
                'error'   => sprintf('Konnte den News-Artikel mit der id "%s" nicht finden', $articleId),
            ], 404);
        }

        try {
            $newsArticle->setText($text);
            $newsArticle->setTitle($title);
            $this->articlesRepository->updateNewsArticle($newsArticle);
            return $response->withJson([
                'success' => true,
            ]);
        } catch (MysqliException $exception) {
            // Den Fehler nur auf dev ausgeben, damit im Live-System keine Details zu Fehlern angezeigt werden
            if (APP_ENV === 'development') {
                return $response->withJson([
                    'success' => false,
                    'error'   => $exception->getMessage(),
                ], 500);
            }

            return $response->withJson([
                'success' => false,
                'error'   => sprintf('Konnte den News-Artikel mit der id "%s" nicht aktualisieren', $articleId),
            ], 500);
        }
    }
}
