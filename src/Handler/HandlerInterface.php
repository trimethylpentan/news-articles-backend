<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

interface HandlerInterface
{
    public function __invoke(ServerRequest $request, Response $response, array $params): ResponseInterface;
}
