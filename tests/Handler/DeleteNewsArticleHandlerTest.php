<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;

/**
 * @covers \Trimethylpentan\NewsArticles\Handler\DeleteNewsArticleHandler
 */
class DeleteNewsArticleHandlerTest extends TestCase
{
    private MockObject | NewsArticlesRepository $articlesRepository;
    private MockObject | ServerRequest $request;
    private MockObject | Response $response;
    private DeleteNewsArticleHandler $handler;

    protected function setUp(): void
    {
        $this->articlesRepository = $this->createMock(NewsArticlesRepository::class);
        $this->request            = $this->createMock(ServerRequest::class);
        $this->response           = $this->createMock(Response::class);

        $this->handler = new DeleteNewsArticleHandler($this->articlesRepository);
    }

    public function testCanGetSuccessResponse(): void
    {
        $articleId = ArticleId::fromInt(1);
        
        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'articleId' => $articleId->asInt(),
        ], JSON_THROW_ON_ERROR));
        
        $this->articlesRepository->expects($this->once())->method('deleteNewsArticle')->with($articleId);
        $this->response->expects($this->once())->method('withJson')->with(['success' => true], 200);
        $this->handler->__invoke($this->request, $this->response, []);
    }

    public function testExceptionLeadsToErrorResponse(): void
    {
        $articleId = ArticleId::fromInt(1);

        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'articleId' => $articleId->asInt(),
        ], JSON_THROW_ON_ERROR));

        $this->articlesRepository->expects($this->once())->method('deleteNewsArticle')->with($articleId)
            ->willThrowException(new MysqliException());
        $this->response->expects($this->once())->method('withJson')->with([
            'success' => false,
            'error'   => 'Konnte den News-Artikel mit der id "1" nicht löschen',
        ], 500);
        $this->handler->__invoke($this->request, $this->response, []);
    }
}
