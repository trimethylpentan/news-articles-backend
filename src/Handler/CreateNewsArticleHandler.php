<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

class CreateNewsArticleHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $newsArticlesRepository,
    ) {}

    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface
    {
        $body = json_decode($request->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);
        
        $title = Title::fromString($body['title']);
        $text  = Text::fromString($body['text']);
        
        $newsArticle = NewsArticle::createNew($title, $text, new DateTimeImmutable());
        try {
            $this->newsArticlesRepository->createNewsArticle($newsArticle);
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
                'success' => 'false',
                'error'   => 'Es ist ein Fehler beim Erstellen des News-Artikel aufgetreten',
            ], 500);
        }
    }
}
