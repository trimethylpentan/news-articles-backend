<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;

class ListNewsArticlesHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $newsArticlesRepository,
    ){}

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $params): ResponseInterface
    {
        $newsArticles = $this->newsArticlesRepository->getNewsArticles();
        
        /** @var Response $response */
        return $response->withJson([
            'news-articles' => $newsArticles
        ]);
    }
}
