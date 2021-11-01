<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Trimethylpentan\NewsArticles\Common\Handler\HandlerInterface;

class CreateNewsArticleHandler implements HandlerInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $params): ResponseInterface
    {
        // TODO: Implement __invoke() method.
    }
}
