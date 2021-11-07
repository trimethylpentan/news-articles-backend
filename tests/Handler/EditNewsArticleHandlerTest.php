<?php

declare(strict_types=1);

namespace Trimethylpentan\NewsArticles\Handler;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Trimethylpentan\NewsArticles\Entity\NewsArticle;
use Trimethylpentan\NewsArticles\MySQL\Exception\MysqliException;
use Trimethylpentan\NewsArticles\Repository\NewsArticlesRepository;
use Trimethylpentan\NewsArticles\Value\ArticleId;
use Trimethylpentan\NewsArticles\Value\Text;
use Trimethylpentan\NewsArticles\Value\Title;

/**
 * @covers \Trimethylpentan\NewsArticles\Handler\EditNewsArticleHandler
 */
class EditNewsArticleHandlerTest extends TestCase
{
    private MockObject | NewsArticlesRepository $articlesRepository;
    private MockObject | ServerRequest $request;
    private MockObject | Response $response;
    private EditNewsArticleHandler $handler;

    protected function setUp(): void
    {
        $this->articlesRepository = $this->createMock(NewsArticlesRepository::class);
        $this->request            = $this->createMock(ServerRequest::class);
        $this->response           = $this->createMock(Response::class);

        $this->handler = new EditNewsArticleHandler($this->articlesRepository);
    }

    public function testCanGetSuccessResponse(): void
    {
        $articleId = ArticleId::fromInt(1);
        $title     = Title::fromString('The title');
        $text      = Text::fromString('The text');
        
        $oldNewsArticle = NewsArticle::createNew(
            Title::fromString('The old title'),
            Text::fromString('The old text'),
            new DateTimeImmutable('today'),
            $articleId,
        );
        $newNewsArticle = NewsArticle::createNew($title, $text, new DateTimeImmutable('today'), $articleId);

        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'id'    => $articleId->asInt(),
            'title' => $title->asString(),
            'text'  => $text->asString(),
        ], JSON_THROW_ON_ERROR));
        
        $this->articlesRepository->expects($this->once())->method('getNewsArticleForId')->with($articleId)
            ->willReturn($oldNewsArticle);
        $this->articlesRepository->expects($this->once())->method('updateNewsArticle')->with($newNewsArticle);
        
        $this->response->expects($this->once())->method('withJson')->with(['success' => true], 200);
        
        $this->handler->__invoke($this->request, $this->response, []);
    }

    public function test404ResponseIsReturnedIfArticleNotFound(): void
    {
        $articleId = ArticleId::fromInt(1);
        $title     = Title::fromString('The title');
        $text      = Text::fromString('The text');
        
        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'id'    => $articleId->asInt(),
            'title' => $title->asString(),
            'text'  => $text->asString(),
        ], JSON_THROW_ON_ERROR));
        
        $this->articlesRepository->expects($this->once())->method('getNewsArticleForId')->with($articleId)
            ->willReturn(null);
        
        $this->response->expects($this->once())->method('withJson')->with([
            'success' => false,
            'error'   => 'Konnte den News-Artikel mit der id "1" nicht finden',
        ], 404);
        
        $this->handler->__invoke($this->request, $this->response, []);
    }

    public function testErrorResponseIsReturnedIfAnErrorOccurrs(): void
    {
        $articleId = ArticleId::fromInt(1);
        $title     = Title::fromString('The title');
        $text      = Text::fromString('The text');

        $oldNewsArticle = NewsArticle::createNew(
            Title::fromString('The old title'),
            Text::fromString('The old text'),
            new DateTimeImmutable('today'),
            $articleId,
        );
        $newNewsArticle = NewsArticle::createNew($title, $text, new DateTimeImmutable('today'), $articleId);

        $stream = $this->createMock(StreamInterface::class);
        $this->request->expects($this->once())->method('getBody')->willReturn($stream);
        $stream->expects($this->once())->method('getContents')->willReturn(json_encode([
            'id'    => $articleId->asInt(),
            'title' => $title->asString(),
            'text'  => $text->asString(),
        ], JSON_THROW_ON_ERROR));

        $this->articlesRepository->expects($this->once())->method('getNewsArticleForId')->with($articleId)
            ->willReturn($oldNewsArticle);
        $this->articlesRepository->expects($this->once())->method('updateNewsArticle')->with($newNewsArticle)
            ->willThrowException(new MysqliException());

        $this->response->expects($this->once())->method('withJson')->with([
            'success' => false,
            'error'   => 'Konnte den News-Artikel mit der id "1" nicht aktualisieren',
            
        ], 500);

        $this->handler->__invoke($this->request, $this->response, []);
    }
}
