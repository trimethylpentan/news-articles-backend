<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;

class ListNewsArticlesHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $newsArticlesRepository,
    ){}

    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface
    {
        $newsArticles = $this->newsArticlesRepository->getNewsArticles();
        
        return $response->withJson([
            'news-articles' => $newsArticles
        ], 200);
    }
}
