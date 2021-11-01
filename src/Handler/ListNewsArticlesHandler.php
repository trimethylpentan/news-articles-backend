<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Trimethylpentan\NewsArticles\Common\Handler\HandlerInterface;

class ListNewsArticlesHandler implements HandlerInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $params): ResponseInterface
    {
        /** @var Response $response */
        return $response->withJson([
            'test' => true,
         ]);
    }
}
