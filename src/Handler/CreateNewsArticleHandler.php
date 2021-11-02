<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use DateTimeImmutable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\NewsArticle;

class CreateNewsArticleHandler implements HandlerInterface
{
    public function __construct(
        private NewsArticlesRepository $newsArticlesRepository,
    ){}

    /**
     * @param RequestInterface|ServerRequest $request
     * @param ResponseInterface|Response $response
     * @param array $params
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $params): ResponseInterface
    {
        $body = $request->getParsedBody();
        
        $title     = $body['title'];
        $text      = $body['text'];
        
        $newsArticle = NewsArticle::createNew($title, $text, new DateTimeImmutable());
        try {
            $this->newsArticlesRepository->createNewsArticle($newsArticle);
            return $response->withJson([
                'created' => true,
            ]);
        } catch (MysqliException $exception) {
            
            // TODO: Error nur in dev
            return $response->withJson([
                'created' => false,
                'error' => $exception->getMessage(),
            ]);
        }
        

    }
}
